<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Basket>
 */
class BasketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'due_date'          => (Carbon::now())->addDays(rand(30,365)),
            'basket_price'      => 0,
        ];
    }
}
