<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnIntoOrderPaymentPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_payment_plan',function(Blueprint $t){
            $t->timestamp('payment_date')->nullable();
            $t->string('status',10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_payment_plan',function(Blueprint $t){
            $t->dropColumn('payment_date');
            $t->dropColumn('status');
        });
        //
    }
}
