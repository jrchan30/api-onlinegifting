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
            $table->string('transaction_number');
            $table->string('payment_type');
            $table->string('receiver_phone_number');
            $table->string('receiver_full_address');
            $table->integer('receiver_destination_code');
            $table->integer('total_weight');
            $table->string('delivery_courier_code');
            $table->string('delivery_courier_service');
            $table->decimal('delivery_fee', 8, 2);
            $table->decimal('total_price', 10, 2);
            $table->date('arrival_date')->nullable();
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
