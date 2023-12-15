<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsIntoCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('courses', function (Blueprint $table) {
            $table->integer('scnd_category_id')->after('category_id');
            $table->date('start_date')->after('slug');
            $table->date('end_date')->after('start_date');
        });
//        Schema::table('bigbluemeetings', function (Blueprint $table) {
//            $table->integer('scnd_category_id')->after('main_category');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(
                'scnd_category_id',
                'start_date',
                'end_date'
            );
        });
    }
}
