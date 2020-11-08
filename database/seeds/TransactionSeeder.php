<?php

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Paid\Transaction;
use Illuminate\Support\Facades\Http;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $userCount = User::count();
        $users = User::skip(1)->take(round($userCount / 2))->get();

        foreach ($users as $user) {
            $cart = $user->cart()->first();
            if ($cart->boxes()->exists()) {
                $boxToDetach = $cart->boxes()->first();

                $userId = $user->id;
                $unix = Carbon::now()->timestamp;
                $counterTx = Transaction::count() + 1;
                $randomStr = strtoupper(Str::random(5));
                $txNumber = "INV/{$unix}/{$userId}/{$counterTx}-{$randomStr}";

                $paymentType = ['credit-card', 'virtual-account', 'transfer'];
                $fullAddress = $faker->address;
                $destinationCode = [39, 40];
                $destination = $destinationCode[array_rand($destinationCode, 1)];
                $boxProducts = $boxToDetach->boxProductQuantities()->get();
                $totalPrice = 0;
                $totalWeight = 0;
                foreach ($boxProducts as $row) {
                    $q = $row->quantity;
                    $price = $row->product->price;
                    $weight = $row->product->weight;
                    $rowPrice = $q * $price;
                    $rowWeight = $q * $weight;

                    $totalPrice += $rowPrice;
                    $totalWeight += $rowWeight;
                }

                $courierCodes = ['jne', 'pos', 'tiki'];
                $courier = $courierCodes[array_rand($courierCodes, 1)];
                $response = Http::asForm()->withHeaders([
                    'key' => env('RAJA_ONGKIR_KEY'),
                ])->post("https://api.rajaongkir.com/starter/cost", [
                    'origin' => '501',
                    'destination' => "{$destination}",
                    'weight' => $totalWeight,
                    'courier' => $courier,
                ]);
                $res = json_decode($response->body());
                $APIservices = $res->rajaongkir->results[0]->costs;
                dump(json_encode($APIservices));
                $service = $APIservices[array_rand($APIservices, 1)];

                $user->transactions()->create([
                    'transaction_number' => $txNumber,
                    'payment_type' => $paymentType[array_rand($paymentType, 1)],
                    'receiver_full_address' => $fullAddress,
                    'receiver_destination_code' => $destination,
                    'receiver_phone_number' => $faker->e164PhoneNumber,
                    'total_weight' => $totalWeight,
                    'delivery_courier_code' => $courier,
                    'delivery_courier_service' => $service->service,
                    'delivery_fee' => $service->cost[0]->value,
                    'total_price' => $totalPrice + $service->cost[0]->value,
                    'arrival_date' => null
                ]);
            }
        }
    }
}
