<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFourthCategoryDataType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->longText('childcategory_id')->change();
        });
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->longText('ch_sub_category')->change();
        });
        Schema::table('offline_sessions', function (Blueprint $table) {
            $table->longText('ch_sub_category')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('childcategory_id')->change();
        });
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->integer('ch_sub_category')->change();
        });
        Schema::table('offline_sessions', function (Blueprint $table) {
            $table->integer('ch_sub_category')->change();
        });
    }
}
