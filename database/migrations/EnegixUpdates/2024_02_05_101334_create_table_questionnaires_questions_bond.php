<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableQuestionnairesQuestionsBond extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('questionnaires_questions_bond')) {
            Schema::create('questionnaires_questions_bond', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('questionnaire_id');
                $table->unsignedBigInteger('question_id');
                $table->timestamps();

                $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->constrained();
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
        if (Schema::hasTable('questionnaires_questions_bond')) {
            Schema::dropIfExists('questionnaires_questions_bond');
        }
    }
}
