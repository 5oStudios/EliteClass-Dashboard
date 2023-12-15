<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleIntoOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders',function(Blueprint $t){
            $t->integer('discount_price')->nullable()->after('id');
            $t->integer('price')->nullable()->after('id');
            $t->string('title')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders',function(Blueprint $t){
            $t->dropColumn('discount_price');
            $t->dropColumn('price');
            $t->dropColumn('title');
        });
    }
}
