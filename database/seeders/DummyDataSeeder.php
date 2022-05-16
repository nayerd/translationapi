<?php

namespace Database\Seeders;

use App\Models\Basket;
use App\Models\BasketDocument;
use App\Models\BasketTargetLanguage;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Language;
use App\Models\Project;

use Illuminate\Database\Seeder;
use Symfony\Component\Console\Helper\ProgressBar;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $baskets = rand(3, 10);

        $bar = $this->command->getOutput()->createProgressBar($baskets);
        $bar->start();

        while ($baskets > 0) {
            $project = Project::factory()->create();
            $customer = Customer::factory()->create();

            $basket = Basket::factory()->create([
                'project_id'     => $project->id,
                'customer_id'    => $customer->id,
            ]);

            //Set languages
            $languages = rand(1, 5);
            while ($languages > 0) {
                $language = Language::inRandomOrder()->first();
                BasketTargetLanguage::updateOrCreate([
                    'basket_id'             => $basket->id,
                    'language_id'           => $language->id,
                    'translation_price'     => 0,
                ]);
                $languages -= 1;
            }

            //Set documents
            $documents = rand(1, 5);
            while ($documents > 0) {
                $document = Document::factory()->create();
                BasketDocument::updateOrCreate([
                    'basket_id'     => $basket->id,
                    'document_id'   => $document->id
                ]);
                $documents -= 1;
            }

            //Basket price
            $basket->updateTranslationPrice();

            $baskets -= 1;
            $bar->advance();
        }
        $bar->finish();
        $this->command->info('');
    }
}
