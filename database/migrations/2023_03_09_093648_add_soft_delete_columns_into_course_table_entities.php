<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteColumnsIntoCourseTableEntities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('course_payment_plan', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('what_learns', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('course_classes', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('quiz_topics', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('review_ratings', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('offline_sessions', function (Blueprint $table) {
            $table->softDeletes();
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
            $table->dropSoftDeletes();
        });
        Schema::table('course_payment_plan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('what_learns', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('quiz_topics', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('review_ratings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('offline_sessions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
