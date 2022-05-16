<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Basket extends Model
{
    use HasFactory;

    protected $table = "baskets";

    protected $fillable = [
        'project_id',
        'customer_id',
        'due_date',
        'basket_price',
    ];

    protected $casts = [
        'due_date'  => 'date:Y-m-d'
    ];

    /**
     * Gets the related project of the given basket
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**
     * Gets the related customer of the given basket
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * Related basket documents of the given basket
     *
     * @return HasMany
     */
    protected function basket_documents(): HasMany
    {
        return $this->hasMany(BasketDocument::class, 'basket_id', 'id');
    }

    /**
     * Related documents of the basket (through basket documents)
     *
     * @return HasManyThrough
     */
    public function documents(): HasManyThrough
    {
        return $this->hasManyThrough(Document::class, BasketDocument::class, 'document_id', 'basket_id', 'id', 'id');
    }

    /**
     * Related target languages of the given basket
     *
     * @return HasMany
     */
    public function basket_target_languages(): HasMany
    {
        return $this->hasMany(BasketTargetLanguage::class, 'basket_id', 'id');
    }

    /**
     * Destination languages of the given basket
     *
     * @return HasManyThrough
     */
    public function languages(): HasManyThrough
    {
        return $this->hasManyThrough(Language::class, BasketTargetLanguage::class, 'language_id', 'basket_id', 'id', 'id');
    }

    /**
     * Updates the translation price
     * This function is called using the BasketDocumentObserver
     * The price is calculated by calling the calculate price for every document in the given basket
     *
     * @return void
     */
    public function updateTranslationPrice()
    {
        $translationPrice = 0;

        $words = collect([]);
        $wordsRepeatedSameFile = collect([]);
        $wordsRepeatedOtherFile = collect([]);

        $sentences = collect([]);
        $sentencesRepeatedSameFile = collect([]);
        $sentencesRepeatedOtherFile = collect([]);


        foreach ($this->basket_documents as $basketDocument) {
            $currentDocumentPrice = 0;

            //Check sentences
            $fileSentences = explode(config('translationapi.sentence_separator'), $basketDocument->document->file_content);
            foreach ($fileSentences as $sentence) {
                if ($this->repeatedSentenceOtherDocument($basketDocument->document->file_id, $sentence) && $sentences->contains(strtolower($sentence))) {
                    $sentencesRepeatedOtherFile->push(strtolower($sentence));
                } else {
                    if ($this->repeatedSentenceSameDocument($basketDocument->document->file_id, $sentence) && $sentences->contains(strtolower($sentence))) {
                        $sentencesRepeatedSameFile->push(strtolower($sentence));
                    }
                }

                if ($sentences->contains(strtolower($sentence)) == false) {
                    $sentences->push(strtolower($sentence));
                }
            }

            //Check words
            foreach ($sentences as $sentence) {
                $sentenceWords = explode(' ', str_replace(config('translationapi.sentence_separator'), ' ', (string)$sentence));
                foreach ($sentenceWords as $word) {
                    if ($this->repeatedWordOtherDocument($basketDocument->document->file_id, $word) && $words->contains(strtolower($word))) {
                        $wordsRepeatedOtherFile->push(strtolower($word));
                    } else {
                        if ($this->repeatedWordSameDocument($basketDocument->document->file_id, $word) && $words->contains(strtolower($word))) {
                            $wordsRepeatedSameFile->push(strtolower($word));
                        }
                    }
                    if ($words->contains(strtolower($word)) == false) {
                        $words->push(strtolower($word));
                    }
                }
            }

            $currentDocumentPrice +=
                //Words
                ($words->count() * config('pricing.word_price')) +
                ($wordsRepeatedSameFile->count() * config('pricing.word_repeated_price')) +
                ($wordsRepeatedOtherFile->count() * config('pricing.word_file_repeated_price')) +
                //Sentences
                ($sentences->count() * config('pricing.sentence_price')) +
                ($sentencesRepeatedSameFile->count() * config('pricing.sentence_repeated_price')) +
                ($sentencesRepeatedOtherFile->count() * config('pricing.sentence_file_repeated_price'));

            $currentDocumentPrice = $this->applyFileTypePrice($basketDocument->document->file_id, $currentDocumentPrice);
            $currentDocumentPrice = $this->applyLanguagesPrice($currentDocumentPrice);

            $translationPrice += $currentDocumentPrice;
        }

        $this->basket_price = $translationPrice;
        $this->save();
    }

    /**
     * Gets the languages array for the given basjet
     *
     * @return array
     */
    public function languagesArray(): array
    {
        $languages = [];
        foreach ($this->basket_target_languages as $basketTargetLanguage) {
            array_push($languages, $basketTargetLanguage->language->iso);
        }
        return $languages;
    }

    /**
     * Gets the remaining time for the given basket expected due date
     *
     * @return string
     */
    public function remainingTime(): string
    {
        $now = Carbon::now();
        $dueDate = Carbon::createFromFormat('Y-m-d', date_format($this->due_date, 'Y-m-d'));
        $days = $now->diffInDays($dueDate);

        return $days > 1 ? ($days . ' days') : ($days == 1 ? 'Tomorrow' : ($days == 0 ? 'Today' : ($days . ' days overdue')));
    }

    /**
     * Gets the array of documents
     *
     * @return array
     */
    public function getBasketDocumentsArray(): array
    {
        $documents = [];
        foreach ($this->basket_documents as $basketDocument) {
            array_push($documents, [
                'id'        => $basketDocument->document->file_id,
                'name'      => $basketDocument->document->file_name,
                'type'      => $basketDocument->document->file_type,
                'comments'  => $basketDocument->document->file_comments,
            ]);
        }
        return $documents;
    }

    /**
     * Returns true when the word is repated on the given document
     *
     * @param string $documentIdToSearch
     * @param string $word
     * @return boolean
     */
    protected function repeatedWordSameDocument(string $documentIdToSearch, string $word): bool
    {
        $basketId = $this->id;
        $document = Document::whereFileId($documentIdToSearch)->whereHas('basket_documents', function (Builder $query) use ($basketId) {
            $query->where('basket_id', '=', $basketId);
        })->first();
        $documentWords = collect(explode(' ', str_replace(config('translationapi.sentence_separator'), ' ', strtolower($document->file_content))));
        return $documentWords->duplicates()->contains(strtolower($word));
    }

    /**
     * Looks for the word on the given document
     *
     * @param string $documentIdToSearch
     * @param string $word
     * @return boolean
     */
    protected function searchWordDocument(string $documentIdToSearch, string $word): bool
    {
        $basketId = $this->id;
        $document = Document::whereFileId($documentIdToSearch)->whereHas('basket_documents', function (Builder $query) use ($basketId) {
            $query->where('basket_id', '=', $basketId);
        })->first();
        $documentWords = collect(explode(' ', str_replace(config('translationapi.sentence_separator'), ' ', strtolower($document->file_content))));
        return $documentWords->contains(strtolower($word));
    }

    /**
     * Looks for the given word in the other documents of the basket
     *
     * @param string $documentIdToExclude
     * @param string $word
     * @return boolean
     */
    protected function repeatedWordOtherDocument(string $documentIdToExclude, string $word): bool
    {
        $basketId = $this->id;
        $documentsToSearch = Document::where('file_id', '<>', $documentIdToExclude)->whereHas('basket_documents', function (Builder $query) use ($basketId) {
            $query->where('basket_id', '=', $basketId);
        })->get();
        foreach ($documentsToSearch as $document) {
            if ($this->searchWordDocument($document->file_id, $word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Searches a given sentence on the same given document
     *
     * @param string $documentIdToSearch
     * @param string $sentence
     * @return boolean
     */
    protected function repeatedSentenceSameDocument(string $documentIdToSearch, string $sentence): bool
    {
        $basketId = $this->id;
        $document = Document::whereFileId($documentIdToSearch)->whereHas('basket_documents', function (Builder $query) use ($basketId) {
            $query->where('basket_id', '=', $basketId);
        })->first();
        $documentSentences = collect(explode(config('translationapi.sentence_separator'), strtolower($document->file_content)));
        return $documentSentences->duplicates()->contains(strtolower($sentence));
    }

    /**
     * Returns true when the given sentence is present in the given document
     *
     * @param string $documentIdToSearch
     * @param string $sentence
     * @return boolean
     */
    protected function searchSentenceDocument(string $documentIdToSearch, string $sentence): bool
    {
        $basketId = $this->id;
        $document = Document::whereFileId($documentIdToSearch)->whereHas('basket_documents', function (Builder $query) use ($basketId) {
            $query->where('basket_id', '=', $basketId);
        })->first();
        $documentSentences = collect(explode(config('translationapi.sentence_separator'), strtolower($document->file_content)));
        return $documentSentences->contains(strtolower($sentence));
    }

    /**
     * Searches the given sentence in the other documents of the basket
     *
     * @param string $documentIdToExclude
     * @param string $sentence
     * @return boolean
     */
    protected function repeatedSentenceOtherDocument(string $documentIdToExclude, string $sentence): bool
    {
        $basketId = $this->id;
        $documentsToSearch = Document::where('file_id', '<>', $documentIdToExclude)->whereHas('basket_documents', function (Builder $query) use ($basketId) {
            $query->where('basket_id', '=', $basketId);
        })->get();
        foreach ($documentsToSearch as $document) {
            if ($this->searchSentenceDocument($document->file_id, $sentence)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the price of the translation according to the discount policy
     *
     * @param string $documentId
     * @param float $currentPrice
     * @return float
     */
    protected function applyFileTypePrice(string $documentId, float $currentPrice): float
    {
        $basketId = $this->id;
        $document = Document::whereFileId($documentId)->whereHas('basket_documents', function (Builder $query) use ($basketId) {
            $query->where('basket_id', '=', $basketId);
        })->first();

        if (!empty($document->file_type) && strtolower($document->file_type) == "psd") {
            return $currentPrice + ($currentPrice * (config('pricing.file_format_percent')['psd'] / 100));
        }

        if (!empty($document->file_type) && strtolower($document->file_type) == "pdf") {
            return $currentPrice + ($currentPrice * (config('pricing.file_format_percent')['pdf'] / 100));
        }

        return $currentPrice;
    }

    /**
     * Gets the price that corresponds to the given language iso
     *
     * @param string $iso
     * @param float $currentPrice
     * @return float
     */
    protected function getPriceLanguage(string $iso, float $currentPrice): float
    {
        switch ($iso) {
            case "es_ES":
                return $currentPrice + ($currentPrice * (config('pricing.language_percent')['es_ES'] / 100));
                break;
            default:
                return $currentPrice;
                break;
        }
    }

    /**
     * Gets the total price according to the language pricing policy
     *
     * @param float $currentPrice
     * @return float
     */
    protected function applyLanguagesPrice(float $currentPrice): float
    {
        $totalPrice = 0;

        foreach ($this->basket_target_languages as $basketTargetLanguage) {
            $totalPrice += $this->getPriceLanguage($basketTargetLanguage->language->iso, $currentPrice);
        }
        return $totalPrice;
    }
}
