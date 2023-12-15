<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartEndIntoProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("course_progress", function(Blueprint $t){
           $t->date('start_date')->nullable(); 
           $t->date('end_date')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("course_progress", function(Blueprint $t){
           $t->dropColumn('start_date'); 
           $t->dropColumn('end_date'); 
        });
        //
    }
}
