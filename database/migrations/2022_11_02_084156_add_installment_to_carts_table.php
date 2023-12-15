<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstallmentToCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->float('price')->change();
            $table->float('offer_price')->change();
            $table->float('disamount')->change();
            $table->boolean('installment')->default(0)->after('bundle_id');
            $table->json('total_installments')->nullable()->after('installment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['installment', 'total_installments']);
        });
    }
}
