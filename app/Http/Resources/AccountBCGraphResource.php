<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;


use Illuminate\Http\Resources\Json\JsonResource;

class AccountBCGraphResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'budget_target_graph_data' => [$this->budget_target_graph_data],
            'budget_actual_graph_data' => $this->budget_actual_graph_data,
            'kpi_target_graph_data' => $this->kpi_target_graph_data,
            'kpi_actual_graph_data' => $this->kpi_actual_graph_data,
        ];
    }
}
