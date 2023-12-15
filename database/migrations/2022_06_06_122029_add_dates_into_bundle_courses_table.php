<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesIntoBundleCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bundle_courses', function (Blueprint $table) {
            $table->date('start_date')->after('slug');
            $table->date('end_date')->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bundle_courses', function (Blueprint $table) {
            $table->dropColumn(
                'start_date',
                'end_date',
            );
        });
    }
}
