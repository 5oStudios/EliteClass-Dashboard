<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderInstallmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_installments', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->integer("order_id");
            $table->integer("coupon_id")->nullable();
            $table->integer("coupon_discount")->nullable();
            $table->integer("total_amount");
            $table->string('payment_method',191);
            $table->string('transaction_id',191);
            $table->string('currency',30);
            $table->string('currency_icon',30);
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
        Schema::dropIfExists('order_installments');
    }
}
