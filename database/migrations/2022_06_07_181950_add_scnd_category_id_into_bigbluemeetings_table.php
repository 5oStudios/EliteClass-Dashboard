<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScndCategoryIdIntoBigbluemeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bigbluemeetings', function(Blueprint $table){
//            $table->integer('scnd_category_id')->after('main_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bigbluemeetings', function(Blueprint $table){
            $table->dropColumn('scnd_category_id');
        });
    }
}
