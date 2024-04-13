<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

       
        $baseUrl = $request->isSecure() ? secure_url('/') : url('/');

        $data['image_url'] = $baseUrl . '/storage/' . $this->image; 

        return $data;
    }
}
