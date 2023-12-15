<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttemptColumnIntoQuizAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_answers', function(Blueprint $t){
            $t->integer('attempt')->length(4)->after('topic_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_answers', function(Blueprint $t){
            $t->dropColumn('attempt')->length(4)->after('topic_id')->default(1);
        });
    }
}
