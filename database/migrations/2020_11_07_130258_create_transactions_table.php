<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('transaction_number')->nullable();
            // $table->string('payment_type');
            $table->string('receiver_phone_number')->nullable();
            $table->string('receiver_full_address')->nullable();
            $table->integer('receiver_destination_code')->nullable();
            $table->integer('total_weight')->nullable();
            $table->string('delivery_courier_code')->nullable();
            $table->string('delivery_courier_service')->nullable();
            $table->decimal('delivery_fee', 8, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->timestamp('arrival_date')->nullable();
            $table->string('status')->nullable();

            $table->string('token')->nullable();
            $table->json('payloads')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('va_number')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('biller_code')->nullable();
            $table->string('bill_key')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
