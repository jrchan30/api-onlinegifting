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
            'receiver_phone_number' => $this->receiver_phone_number,
            'receiver_full_address' => $this->receiver_full_address,
            'receiver_city' => $this->receiver_city,
            'receiver_postal_code' => $this->receiver_postal_code,
            'receiver_destination_code' => $this->receiver_destination_code,
            'total_weight' => $this->total_weight,
            'delivery_courier_code' => $this->delivery_courier_code,
            'delivery_courier_service' => $this->delivery_courier_service,
            'delivery_fee' => $this->delivery_fee ?? "0",
            'total_price' => $this->total_price,
            'arrival_date' => $this->arrival_date,
            'is_arrived' => $this->is_arrived,
            'payment_status' => $this->payment_status,
            'token' => $this->token,
            'payloads' => $this->payloads,
            'payment_type' => $this->payment_type,
            'va_number' => $this->va_number,
            'vendor_name' => $this->vendor_name,
            'biller_code' => $this->biller_code,
            'bill_key' => $this->bill_key,
            'transaction_status' => $this->transaction_status,
            'transaction_time' => $this->transaction_time,
            'fraud_status' => $this->fraud_status,
            'paid_boxes' => PaidBoxResource::collection($this->paidBoxes),
            'paid_bundles' => PaidBundleResource::collection($this->paidBundles),
            'created_at' => $this->created_at->diffForHumans(),
        ];

        if (auth()->user()->userDetail->type === 'admin') {
            $arrayData['user'] = new UserResource($this->user);
        }

        return $arrayData;
    }
}
