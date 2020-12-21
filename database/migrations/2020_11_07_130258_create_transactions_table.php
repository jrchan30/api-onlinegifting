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
            $table->string('transaction_number')->nullable(); //not null, udah
            $table->string('receiver_phone_number')->nullable(); //not null, udah
            $table->string('receiver_full_address')->nullable(); // not null, udah
            $table->string('receiver_city')->nullable(); // not null, udah
            $table->string('receiver_postal_code')->nullable(); // not null, udah
            $table->integer('receiver_destination_code')->nullable(); //not null, udah, not used
            $table->integer('total_weight')->nullable(); //not null, udah
            $table->string('delivery_courier_code')->nullable(); //not null, udah
            $table->string('delivery_courier_service')->nullable()->default('AOT'); //not null, udah
            $table->decimal('delivery_fee', 8, 2)->nullable(); //not null, udah
            $table->decimal('total_price', 10, 2)->nullable(); //not null, udah
            $table->timestamp('arrival_date')->nullable(); //udah
            $table->string('payment_status')->nullable(); //change paid unpaid

            $table->string('token')->nullable(); //not null, udah
            $table->json('payloads')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('va_number')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('biller_code')->nullable();
            $table->string('bill_key')->nullable();

            $table->string('transaction_status')->nullable();
            $table->string('transaction_time')->nullable();
            $table->string('fraud_status')->nullable();

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
