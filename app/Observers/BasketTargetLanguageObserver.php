<?php

namespace App\Observers;

use App\Models\BasketTargetLanguage;

class BasketTargetLanguageObserver
{
    /**
     * When a BasketTargetLanguage object is created then update the translation price
     *
     * @param  \App\Models\BasketTargetLanguage  $basketTargetLanguage
     * @return void
     */
    public function created(BasketTargetLanguage $basketTargetLanguage)
    {
        $basketTargetLanguage->basket->updateTranslationPrice();
    }

    /**
     * When a BasketTargetLanguage object is updated then update the translation price
     *
     * @param  \App\Models\BasketTargetLanguage  $basketTargetLanguage
     * @return void
     */
    public function updated(BasketTargetLanguage $basketTargetLanguage)
    {
        $basketTargetLanguage->basket->updateTranslationPrice();
    }

    /**
     * When a BasketTargetLanguage object is deleted then update the translation price
     *
     * @param  \App\Models\BasketTargetLanguage  $basketTargetLanguage
     * @return void
     */
    public function deleted(BasketTargetLanguage $basketTargetLanguage)
    {
        $basketTargetLanguage->basket->updateTranslationPrice();
    }
}
