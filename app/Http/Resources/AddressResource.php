<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="AddressResource",
 *     description="Address resource model",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID of the address"),
 *     @OA\Property(property="street", type="string", description="Street name"),
 *     @OA\Property(property="city", type="string", description="City"),
 *     @OA\Property(property="province", type="string", description="Province"),
 *     @OA\Property(property="country", type="string", description="Country"),
 *     @OA\Property(property="postal_code", type="string", description="Postal code")
 * )
 */
class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'street' => $this->street,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'postal_code' => $this->postal_code
        ];
    }
}
