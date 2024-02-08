<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableQuestionnairesAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('questionnaires_answers')) {
            Schema::create('questionnaires_answers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('questionnaire_course_id');
                $table->unsignedInteger('student_id');
                $table->unsignedBigInteger('question_id');
                $table->integer('rate');
                $table->string('answer')->nullable();
                $table->dateTime('answer_date');
                $table->timestamps();

                $table->foreign('questionnaire_course_id')->references('id')->on('questionnaires_courses')->constrained();
                $table->foreign('student_id')->references('id')->on('users')->constrained();
                $table->foreign('question_id')->references('id')->on('questionnaires_questions')->constrained();
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
        if (Schema::hasTable('questionnaires_answers')) {
            Schema::dropIfExists('questionnaires_answers');
        }
    }
}
