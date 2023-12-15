<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogSendingMailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_sending_mail', function (Blueprint $table) {
            $table->id();
            $table->string('to')->nullable();
            $table->string('from')->nullable();
            $table->string('sender')->nullable();
            $table->string('date')->nullable();
            $table->string('subject')->nullable();
            $table->mediumText('body')->nullable();
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
        Schema::dropIfExists('log_sending_mail');
    }
}
