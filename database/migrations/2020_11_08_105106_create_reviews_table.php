<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('body');
            $table->integer('rating');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('paid_product_id')->nullable();
            $table->unsignedBigInteger('paid_bundle_id')->nullable();
            $table->unsignedBigInteger('reviewable_id');
            $table->string('reviewable_type');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('paid_product_id')->references('id')->on('paid_products');
            $table->foreign('paid_bundle_id')->references('id')->on('paid_bundles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
