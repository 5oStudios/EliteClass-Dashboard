<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemarks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('remarks')) {
            Schema::create('remarks', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('student_id');
                $table->unsignedInteger('topic_id');
                $table->unsignedInteger('instructor_id');
                $table->text('content');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('remarks')) {
            Schema::dropIfExists('remarks');
        }
    }
}
