<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_enrollments', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->string('meeting_id')->nullable();
            $table->integer('offline_session_id')->nullable();
            $table->integer('user_id');
            // $table->date('enroll_start');
            // $table->date('enroll_expire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_enrollments');
    }
}
