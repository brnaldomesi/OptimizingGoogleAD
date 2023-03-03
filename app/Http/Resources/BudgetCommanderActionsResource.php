<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetCommanderActionsResource extends JsonResource
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
            'notify_via_email' => '',
            'pause_campaigns' => '',
            'enable_campaigns' => '',
            'rollover_spend' => '',
            'control_spend' => ''
        ];
    }
}
