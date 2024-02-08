<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableQuestionnairesCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('questionnaires_courses')) {
            Schema::create('questionnaires_courses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('questionnaire_id');
                $table->unsignedInteger('course_id');
                $table->date('appointment');
                $table->timestamps();

                $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->constrained();
                $table->foreign('course_id')->references('id')->on('courses')->constrained();
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
        if (!Schema::hasTable('questionnaires_courses')) {
            Schema::dropIfExists('questionnaires_courses');
        }
    }
}
