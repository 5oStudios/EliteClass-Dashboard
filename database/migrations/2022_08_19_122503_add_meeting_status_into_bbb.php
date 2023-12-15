<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeetingStatusIntoBbb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bigbluemeetings',function(Blueprint $b){
            $b->enum("reco_status",['0','1','2','3'])->nullable()->comment('0 for pending, 1 for processing, 2 for rec ready, 3 for rec deleted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bigbluemeetings',function(Blueprint $b){
            $b->dropColumn("reco_status");
        });
        
    }
}
