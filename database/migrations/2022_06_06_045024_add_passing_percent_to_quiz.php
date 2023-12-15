<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPassingPercentToQuiz extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_topics', function (Blueprint $table) {
            $table->integer('p_percent')->default('60')->after('per_q_mark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_topics', function (Blueprint $table) {
            $table->dropColumn('p_percent');
        });
    }
}
