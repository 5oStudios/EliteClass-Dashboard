<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWhatsappGrouplinkIntoCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("courses",function(Blueprint $t){
           $t->string("wtsap_link")->nullable(); 
        });
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("courses",function(Blueprint $t){
           $t->dropColumn("wtsap_link"); 
        });
        //
    }
}
