<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DbChangeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->longtext('answer')->nullable()->change();
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->longtext('question')->nullable()->change();
        });
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->longtext('detail')->nullable()->change();
            $table->integer('main_category')->nullable()->change();
            $table->integer('scnd_category_id')->nullable()->change();
            $table->integer('sub_category')->nullable()->change();
            $table->integer('ch_sub_category')->nullable()->change();
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->longtext('detail')->nullable()->change();
            $table->integer('price')->default(0)->change();
            $table->integer('discount_price')->default(0)->change();
        });
        Schema::table('bundle_courses', function (Blueprint $table) {
            $table->longtext('detail')->nullable()->change();
        });
        Schema::table('course_classes', function (Blueprint $table) {
            $table->longtext('long_text')->nullable()->change();
            $table->integer('meeting_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
