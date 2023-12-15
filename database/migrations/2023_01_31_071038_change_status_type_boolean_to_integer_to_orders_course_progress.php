<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStatusTypeBooleanToIntegerToOrdersCourseProgress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('status')->default(0)->change();
        });
        Schema::table('course_progress', function (Blueprint $table) {
            $table->integer('status')->default(1)->change();
        });
        Schema::table('session_enrollments', function (Blueprint $table) {
            $table->integer('status')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('status')->default(0)->change();
        });
        Schema::table('course_progress', function (Blueprint $table) {
            $table->boolean('status')->default(1)->change();
        });
        Schema::table('session_enrollments', function (Blueprint $table) {
            $table->boolean('status')->default(1)->change();
        });
    }
}
