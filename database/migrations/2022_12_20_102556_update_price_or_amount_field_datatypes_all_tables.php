<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePriceOrAmountFieldDatatypesAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('price', 10,3)->nullable()->change();
            $table->decimal('discount_price', 10,3)->nullable()->change();
            $table->decimal('total_amount', 10,3)->change();
            $table->decimal('paid_amount', 10,3)->change();
            $table->decimal('coupon_discount', 10,3)->nullable()->change();
            $table->decimal('instructor_revenue', 10,3)->nullable()->change();
            $table->decimal('installment_price', 10,3)->nullable()->change();
        });
        Schema::table('order_payment_plan', function (Blueprint $table) {
            $table->decimal('amount', 10,3)->change();
        });

        Schema::table('order_installments', function (Blueprint $table) {
            $table->decimal('coupon_discount', 10,3)->nullable()->change();
            $table->decimal('total_amount', 10,3)->change();
        });

        Schema::table('bundle_courses', function (Blueprint $table) {
            $table->decimal('price', 10,3)->nullable()->change();
            $table->decimal('discount_price', 10,3)->nullable()->change();
            $table->decimal('installment_price', 10,3)->nullable()->change();
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('price', 10,3)->nullable()->change();
            $table->decimal('discount_price', 10,3)->nullable()->change();
            $table->decimal('installment_price', 10,3)->nullable()->change();
        });
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->decimal('price', 10,3)->change();
            $table->decimal('discount_price', 10,3)->change();
        });
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->decimal('price', 10,3)->change();
            $table->decimal('discount_price', 10,3)->change();
        });
        Schema::table('offline_sessions', function (Blueprint $table) {
            $table->decimal('price', 10,3)->change();
            $table->decimal('discount_price', 10,3)->change();
        });
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->decimal('total_amount', 10,3)->change();
        });
        Schema::table('wallet', function (Blueprint $table) {
            $table->decimal('balance', 10,3)->nullable()->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('price')->nullable()->change();
            $table->integer('discount_price')->nullable()->change();
            $table->string('total_amount')->change();
            $table->integer('paid_amount')->change();
            $table->integer('coupon_discount')->nullable()->change();
            $table->integer('instructor_revenue')->nullable()->change();
            $table->integer('installment_price')->nullable()->change();
        });
        Schema::table('order_payment_plan', function (Blueprint $table) {
            $table->integer('amount')->change();
        });
        Schema::table('order_installments', function (Blueprint $table) {
            $table->integer('coupon_discount')->nullable()->change();
            $table->integer('total_amount')->change();
        });
        Schema::table('bundle_courses', function (Blueprint $table) {
            $table->integer('price')->nullable()->change();
            $table->integer('discount_price')->nullable()->change();
            $table->integer('installment_price')->nullable()->change();
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('price')->nullable()->change();
            $table->integer('discount_price')->nullable()->change();
            $table->integer('installment_price')->nullable()->change();
        });
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->float('price')->change();
            $table->float('discount_price')->change();
        });
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->double('price')->change();
            $table->double('discount_price')->change();
        });
        Schema::table('offline_sessions', function (Blueprint $table) {
            $table->double('price')->change();
            $table->double('discount_price')->change();
        });
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->float('total_amount', 10, 0)->change();
        });
        Schema::table('wallet', function (Blueprint $table) {
            $table->float('balance', 10, 0)->nullable()->default(0.00)->change();
        });
    }
}
