<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMarkChapterIdTypeToCourseProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_progress', function (Blueprint $table) {
            $table->longText('mark_chapter_id')->nullable()->change();
            $table->longText('all_chapter_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_progress', function (Blueprint $table) {
            $table->string('mark_chapter_id')->nullable()->change();
            $table->string('all_chapter_id')->nullable()->change();
        });
    }
}
