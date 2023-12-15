<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTitleDbTypeVarcharLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function(Blueprint $table){
            $table->string('title', '1000')->nullable()->change();
        });
        Schema::table('bundle_courses', function(Blueprint $table){
            $table->string('title', '1000')->nullable()->change();
        });
        Schema::table('bigbluemeetings', function(Blueprint $table){
            $table->string('meetingname', '1000')->nullable()->change();
            $table->string('welcomemsg', '1000')->nullable()->change();
        });
        Schema::table('course_classes', function(Blueprint $table){
            $table->string('title', '1000')->nullable()->change();
        });
        Schema::table('course_chapters', function(Blueprint $table){
            $table->string('chapter_name', '1000')->nullable()->change();
        });
        Schema::table('quiz_questions', function(Blueprint $table){
            $table->string('a', '1000')->nullable()->change();
            $table->string('b', '1000')->nullable()->change();
            $table->string('c', '1000')->nullable()->change();
            $table->string('d', '1000')->nullable()->change();
        });
        Schema::table('quiz_topics', function(Blueprint $table){
            $table->string('title', '1000')->nullable()->change();
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
