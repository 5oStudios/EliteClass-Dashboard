<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIntoCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('coupon_type', 191)->after('amount');
            $table->string('link_by')->nullable()->change();
            $table->integer('bundle_id')->unsigned()->nullable()->after('course_id');
            $table->integer('meeting_id')->unsigned()->nullable()->after('bundle_id');
            $table->integer('offline_session_id')->unsigned()->nullable()->after('meeting_id');
            $table->string('payment_type', 191)->nullable()->after('offline_session_id');
            $table->integer('installment_number')->unsigned()->nullable()->after('payment_type');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['coupon_type', 'offline_session_id', 'payment_type', 'installment_number']);
        });
    }
}
