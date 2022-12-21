<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "billing_name" => $this->billing_name,
            "billing_email" => $this->billing_email,
            "billing_address" => $this->billing_address,
            "billing_city" => $this->billing_city,
            "billing_state" => $this->billing_state,
            "billing_phone" => $this->billing_phone,
            "billing_total" => $this->billing_total,
            "shipped" => $this->shipped,
            "customer" => new UserResource($this->user),
            "created_at" => strtotime($this->created_at),
        ];
    }
}
