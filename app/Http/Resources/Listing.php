<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App;
class Listing extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'location' => ($this->city),
            'item' => json_decode($this->item),
            'days' => json_decode($this->days),
            'price' => $this->price,
            'active' => $this->active

        ];
    }
}
