<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfflineSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id')->unsigned();
            $table->integer('instructor_id')->unsigned();
            $table->string('title', 191);
            $table->longtext('detail')->nullable();
            $table->string('image');
            $table->string('start_time', 200);
            $table->string('duration', 191);
            $table->string('location', 191);
            $table->string('google_map_link', 191);
            $table->string('setMaxParticipants', 191)->default('-1');
            $table->double('price')->nullable();
            $table->double('discount_price')->nullable();
            $table->string('link_by')->nullable();
            $table->integer('course_id')->nullable();
            $table->integer('main_category')->nullable();
            $table->integer('scnd_category_id')->nullable();
            $table->integer('sub_category')->nullable();
            $table->integer('ch_sub_category')->nullable();
            $table->integer('is_ended')->default(0);
            $table->string('time_zone')->nullable();
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
        Schema::dropIfExists('offline_sessions');
    }
}
