<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDownloadableAndPrintableColumnsIntoCourseClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_classes', function (Blueprint $table) {
            $table->boolean('downloadable')->default(0)->after('audio');
            $table->boolean('printable')->default(0)->after('downloadable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropColumn(['downloadable', 'printable']);
        });
    }
}
