<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product' => $this->stripe_prod,
            'plan' => $this->stripe_plan,
            'name' => $this->name,
            'price' => $this->price,
            'currency' => $this->currency,
            'frequency' => $this->frequency,
            'user_limit' => $this->user_limit,
            'account_limit' => $this->account_limit,
            'active' => $this->active
        ];
    }
}
