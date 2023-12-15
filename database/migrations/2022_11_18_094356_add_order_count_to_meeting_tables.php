<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderCountToMeetingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->integer('order_count')->default(0)->after('setMaxParticipants');
        });
        Schema::table('offline_sessions', function (Blueprint $table) {
            $table->integer('order_count')->default(0)->after('setMaxParticipants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->dropColumn('order_count');
        });
        Schema::table('offline_sessions', function (Blueprint $table) {
            $table->dropColumn('order_count');
        });
    }
}
