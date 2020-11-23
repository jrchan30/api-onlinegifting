<?php

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Paid\Transaction;
use App\Models\ProductQuantity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class TransactionSeeder extends Seeder
{

    /**
     * Create Transaction Form
     * @param User $user
     * @param object $ongkirService
     * @return Transaction
     */
    public function transactionForm($user, $ongkirService)
    {
        $faker = Faker\Factory::create();

        $userId = $user->id;

        $unix = Carbon::now()->timestamp;
        $counterTx = Transaction::count() + 1;
        $randomStr = strtoupper(Str::random(5));
        $txNumber = "INV/{$unix}/{$userId}/{$counterTx}-{$randomStr}";

        $types = ['credit-card', 'virtual-account', 'transfer'];
        $paymentType = $types[array_rand($types, 1)];

        $fullAddress = $faker->address;
        $phoneNumber = $faker->e164PhoneNumber;

        $tx = $user->transactions()->create([
            'transaction_number' => $txNumber,
            'payment_type' => $paymentType,
            'receiver_full_address' => $fullAddress,
            'receiver_phone_number' => $phoneNumber,
            'receiver_destination_code' => $ongkirService->destination,
            'total_weight' => $ongkirService->totalWeight,
            'delivery_courier_code' => $ongkirService->courier,
            'delivery_courier_service' => $ongkirService->service,
            'delivery_fee' => $ongkirService->fee,
            'total_price' => $ongkirService->totalPrice + $ongkirService->fee,
            'arrival_date' => null,
            'created_at' => Carbon::now()->subMonthsNoOverflow(rand(1, 12))
        ]);

        return $tx;
    }

    /**
     * Calculate productquantities rows
     */
    public function calculateWeightPrice($products)
    {
        $totalPrice = 0;
        $totalWeight = 0;
        foreach ($products as $row) {
            $q = $row->quantity;
            $price = $row->product->price;
            $weight = $row->product->weight;
            $rowPrice = $q * $price;
            $rowWeight = $q * $weight;

            $totalPrice += $rowPrice;
            $totalWeight += $rowWeight;
        }
        return (object)['totalPrice' => $totalPrice, 'totalWeight' => $totalWeight];
    }

    /**
     * Check ongkir
     */

    public function ongkirService($products)
    {
        $result = $this->calculateWeightPrice($products);

        $destinationCode = [39, 114, 501];
        $destination = $destinationCode[array_rand($destinationCode, 1)];

        $courierCodes = ['jne', 'pos', 'tiki'];
        $courier = $courierCodes[array_rand($courierCodes, 1)];

        $response = Http::asForm()->withHeaders([
            // 'key' => env('RAJA_ONGKIR_KEY'),
            'key' => '43a49ea5195272f21e0c2afd4e8c2ecb'
        ])->post("https://api.rajaongkir.com/starter/cost", [
            'origin' => '501',
            'destination' => "{$destination}",
            'weight' => $result->totalWeight,
            'courier' => $courier,
        ]);

        if ($response->status() == 400) {
            dump($response->body());
        }

        $res = json_decode($response->body());
        $APIservices = $res->rajaongkir->results[0]->costs;
        // dump(json_encode($APIservices));
        $serviceChoice = $APIservices[array_rand($APIservices, 1)];

        $array = array(
            'destination' => $destination,
            'courier' => $courier,
            'service' => $serviceChoice->service,
            'fee' => $serviceChoice->cost[0]->value,
            'totalWeight' => $result->totalWeight,
            'totalPrice' => $result->totalPrice
        );

        return (object)$array;
    }

    public function createPaidModel($relation, $model, $transaction)
    {
        $reference = $relation == 'paidBoxes' ? 'box_id' : 'bundle_id';

        $paidModel = $transaction->{$relation}()->create([
            $reference => $model->id,
            'name' => $model->name,
            'path' => $model->detail->image->path,
            'url' => $model->detail->image->url
        ]);

        return (object)$paidModel;
    }

    public function cloneProducts($products, $paidModel, $modelToDetach)
    {
        foreach ($products as $product) {
            $productImg = $product->images()->first();
            $quant = $modelToDetach->productQuantities()
                ->where('product_id', $product->id)
                ->first();

            $paidModel->paidProducts()->create([
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'quantity' => $quant->quantity,
                'weight' => $product->weight,
                'path' => $productImg->path,
                'url' => $productImg->url
            ]);
        }
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userCount = User::count();
        $users = User::skip(1)->take(round($userCount / 2))->get();

        foreach ($users as $user) {
            $rand = random_int(1, 2);
            $cart = $user->cart()->first();
            if ($cart->boxes()->exists() && $rand == 1) {

                //model yang mau dibayar
                $boxToDetach = $cart->boxes()->first();

                //isi dari model yang mau di bayar
                $boxProducts = $boxToDetach->productQuantities()->get();

                //check ongkir, total berat, total harga tanpa ongkir
                $ongkirService = $this->ongkirService($boxProducts);

                //bikin model transaksi
                $tx = $this->transactionForm($user, $ongkirService);

                //bikin model yang mau dibayar
                $paidBox = $this->createPaidModel('paidBoxes', $boxToDetach, $tx);

                //ambil semua product yang ada di model yang mau di bayar
                $boxToDetachProducts = $boxToDetach->products()->get();

                //clone productnya
                $this->cloneProducts($boxToDetachProducts, $paidBox, $boxToDetach);

                //detach modelnya
                $cart->boxes($boxToDetach)->detach();
            } else if ($cart->bundles()->exists() && $cart->boxes()->exists()) {
                $bundlesToDetach = $cart->bundles()->get();
                $boxesToDetach = $cart->boxes()->get();

                $merged = $bundlesToDetach->merge($boxesToDetach);
                $allProductsMerged = new Collection();
                foreach ($merged as $item) {
                    $productsRows = $item->productQuantities()->get();
                    $allProductsMerged = $allProductsMerged->merge($productsRows);
                }
                $ongkirService = $this->ongkirService($allProductsMerged);
                $tx = $this->transactionForm($user, $ongkirService);

                foreach ($merged as $item) {
                    $relationTable = $item->getTable();
                    $relation = $relationTable == 'bundles' ? 'paidBundles' : 'paidBoxes';

                    $paidModel = $this->createPaidModel($relation, $item, $tx);
                    $itemProducts = $item->products()->get();
                    $this->cloneProducts($itemProducts, $paidModel, $item);
                    $cart->{$relationTable}($item)->detach();
                }
            }
        }
    }
}
