<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFpjsidColumnIntoOauthAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_access_tokens', function(Blueprint $table){
            $table->string('fpjsid')->nullable()->after('client_id');
            $table->string('created')->nullable()->after('fpjsid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_access_tokens', function(Bluprint $table){

            $table->dropColumn('fpjsid', 'created');
        });
    }
}
