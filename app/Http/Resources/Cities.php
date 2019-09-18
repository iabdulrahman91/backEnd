<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Cities extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if ($request->get('details') == true) {
            return [
                'id' => $this->id,
                'region_id' => $this->region_id,
                'name_ar' => $this->name_ar,
                'name_en' => $this->name_en,
                'center' => json_decode($this->days),

            ];
        } else {
            return [
                'id' => $this->id,
                'name' => $this->name_ar . ' - ' . $this->name_en,
            ];
        }

    }
}
