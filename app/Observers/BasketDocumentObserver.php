<?php

namespace App\Observers;

use App\Models\BasketDocument;

class BasketDocumentObserver
{
    /**
     * When a basket document is created then update the translation price
     *
     * @param  \App\Models\BasketDocument  $basketDocument
     * @return void
     */
    public function created(BasketDocument $basketDocument)
    {
        $basketDocument->basket->updateTranslationPrice();
    }

    /**
     * When a basket document is updated then update the translation price
     *
     * @param  \App\Models\BasketDocument  $asketDocument
     * @return void
     */
    public function updated(BasketDocument $basketDocument)
    {
        $basketDocument->basket->updateTranslationPrice();
    }

    /**
     *  When a basket document is deleted then update the translation price
     *
     * @param  \App\Models\BasketDocument  $asketDocument
     * @return void
     */
    public function deleted(BasketDocument $basketDocument)
    {
        $basketDocument->basket->updateTranslationPrice();
    }
}
