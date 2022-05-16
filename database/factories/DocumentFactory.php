<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        //Array of available types to choose from
        $types = [
            'png',
            'txt',
            'pdf',
            'psd',
            'csv',
            'doc',
            'xls'
        ];

        return [
            'file_id'           => $this->faker->word(),
            'file_name'         => $this->faker->word(),
            'file_type'         => $types[rand(0, count($types) - 1)],

            //Creates random sentences splitted by the separator (see config file)
            'file_content'      => array_reduce($this->faker->sentences(rand(1, 10)), function ($carry, $sentence) {
                return (!empty($carry) ? $carry : '') . config('translationapi.sentence_separator') . $sentence;
            }),

            'file_comments'     => $this->faker->text(200)
        ];
    }
}
