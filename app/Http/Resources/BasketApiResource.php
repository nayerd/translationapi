<?php

namespace App\Http\Resources;

use App\Models\Basket;
use Illuminate\Http\Resources\Json\JsonResource;

class BasketApiResource extends JsonResource
{
    protected $basket;

    public function __construct(Basket $basket)
    {
        $this->basket = $basket;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'project_id'        => $this->basket->project->project_id,
            'customer_id'       => $this->basket->customer->customer_id,
            'target_languages'  => $this->basket->languagesArray(),
            'expected_due_date' => $this->basket->due_date->format('Y-m-d'),
            'remaining_time'    => $this->basket->remainingTime(),
            'files'             => $this->basket->getBasketDocumentsArray(),
            'calculated_price'  => round($this->basket->basket_price, 2) . ' €'
        ];
    }


}
