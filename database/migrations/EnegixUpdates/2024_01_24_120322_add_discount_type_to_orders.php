<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountTypeToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('orders', 'discount_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('discount_type')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('orders', 'discount_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('discount_type');
            });
        }
    }
}
