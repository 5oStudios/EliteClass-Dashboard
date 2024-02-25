<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsImageToQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('quiz_questions', 'is_image')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->boolean('is_image')->default(false);
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
        if (Schema::hasColumn('quiz_questions', 'is_image')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->dropColumn('is_image');
            });
        }
    }
}
