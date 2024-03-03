<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('coupons', 'user_id')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('coupons', 'user_id')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
}
