<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageFieldsIntoAllCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('secondary_categories',function(Blueprint $table){
            $table->string('image')->after('title');
        });
        Schema::table('sub_categories',function(Blueprint $table){
            $table->string('image')->after('title');
        });
        Schema::table('child_categories',function(Blueprint $table){
            $table->string('image')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('secondary_categories',function(Blueprint $table){
            $table->dropColumn("image");
        });
        Schema::table('sub_categories',function(Blueprint $table){
            $table->dropColumn("image");
        });
        Schema::table('child_categories',function(Blueprint $table){
            $table->dropColumn("image");
        });
    }
}
