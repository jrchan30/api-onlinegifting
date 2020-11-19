<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $arrayData = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'transaction_number' => $this->transaction_number,
            'payment_type' => $this->payment_type,
            'receiver_phone_number' => $this->receiver_phone_number,
            'receiver_full_address' => $this->receiver_full_address,
            'receiver_destination_code' => $this->receiver_destination_code,
            'total_weight' => $this->total_weight,
            'delivery_courier_code' => $this->delivery_courier_code,
            'delivery_fee' => $this->delivery_fee,
            'total_price' => $this->total_price,
            'arrival_date' => $this->arrival_date,
            'created_at' => $this->created_at,
            'paid_boxes' => PaidBoxResource::collection($this->paidBoxes),
            'paid_bundles' => PaidBundleResource::collection($this->paidBundles),
        ];

        if (auth()->user()->userDetail->type === 'admin') {
            $arrayData['user'] = new UserResource($this->user);
        }

        return $arrayData;
    }
}
