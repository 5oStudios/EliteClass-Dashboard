<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfferTypeToCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            Schema::table('carts', function (Blueprint $table) {
                $table->string('offer_type')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('carts', 'offer_type')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropColumn('offer_type');
            });
        }
    }
}
