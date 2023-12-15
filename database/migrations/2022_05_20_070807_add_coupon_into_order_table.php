<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCouponIntoOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders',function(Blueprint $table){
            $table->integer('coupon_id')->nullable()->after('coupon_discount');
            $table->boolean('installments')->default(0)->after('transaction_id');
            $table->integer('paid_amount')->default(0)->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders',function(Blueprint $table){
            $table->dropColumn('coupon_id');
            $table->dropColumn('installments');
            $table->dropColumn('paid_amount');
        });
        //
    }
}
