<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsIntoChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_chapters', function(Blueprint $table){
            $table->text('detail')->nullable()->after('chapter_name');
            $table->float('price')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->dropColumn('detail','price');
        });
    }
}
