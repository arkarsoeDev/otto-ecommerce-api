<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $stockStatus = "red";
        if($this->stock > 5) {
            $stockStatus = "green";
        } elseif($this->stock > 0 && $this->stock < 5) {
            $stockStatus = "yellow";
        } else {
            $stockStatus = "red";
        }
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "price" => $this->price,
            "details" => $this->details,
            "stock" => $this->stock,
            "stockStatus" => $stockStatus,
            "category" => new CategoryResource($this->category),
            "brand" => new BrandResource($this->brand),
            "photos" => PhotoResource::collection($this->photos)
        ];
    }
}
