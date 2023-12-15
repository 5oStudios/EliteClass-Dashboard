<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_coupons', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('cart_id')->nullable();
            $table->integer('order_payment_plan_id')->nullable();
            $table->integer('coupon_id');
            $table->integer('installment_id')->nullable();
            $table->string('distype');
            $table->decimal('disamount', 10,3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_coupons');
    }
}
