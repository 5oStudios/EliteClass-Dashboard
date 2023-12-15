<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllDbTablesRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('bundle_courses', function (Blueprint $table) {
        //     $table->unsignedInteger('id')->increment()->change();
        // });
        // Schema::table('bigbluemeetings', function (Blueprint $table) {
        //     $table->unsignedInteger('id')->increment()->change();
        //     $table->unsignedInteger('owner_id')->nullable()->change();
        //     $table->unsignedInteger('instructor_id')->nullable()->change();
        //     $table->unsignedInteger('course_id')->nullable()->change();
        //     $table->unsignedInteger('main_category')->nullable()->change();
        //     $table->unsignedInteger('scnd_category_id')->nullable()->change();
        //     $table->unsignedInteger('sub_category')->nullable()->change();
        //     $table->unsignedInteger('ch_sub_category')->nullable()->change();

        //     $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        //     $table->foreign('main_category')->references('id')->on('categories')->onDelete('cascade');
        //     $table->foreign('scnd_category_id')->references('id')->on('secondary_categories')->onDelete('cascade');
        //     $table->foreign('sub_category')->references('id')->on('sub_categories')->onDelete('cascade');
        //     $table->foreign('ch_sub_category')->references('id')->on('child_categories')->onDelete('cascade');
        // });
        // Schema::table('course_progress', function (Blueprint $table) {
        //     $table->unsignedInteger('id')->increment()->change();
        // });
        
        // Schema::table('secondary_categories', function (Blueprint $table) {
        //     $table->unsignedInteger('category_id')->nullable()->change();

        //     $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        // });
        // Schema::table('sub_categories', function (Blueprint $table) {
        //     $table->unsignedInteger('category_id')->nullable()->change();
        //     $table->unsignedInteger('scnd_category_id')->nullable()->change();

        //     $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        //     $table->foreign('scnd_category_id')->references('id')->on('secondary_categories')->onDelete('cascade');
        // });
        // Schema::table('child_categories', function (Blueprint $table) {
        //     $table->unsignedInteger('category_id')->nullable()->change();
        //     $table->unsignedInteger('scnd_category_id')->nullable()->change();
        //     $table->unsignedInteger('subcategory_id')->nullable()->change();

        //     $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        //     $table->foreign('scnd_category_id')->references('id')->on('secondary_categories')->onDelete('cascade');
        //     $table->foreign('subcategory_id')->references('id')->on('sub_categories')->onDelete('cascade');
        // });
        // Schema::table('coupons', function (Blueprint $table) {
        //     $table->unsignedInteger('course_id')->nullable()->change();
        //     $table->unsignedInteger('bundle_id')->nullable()->change();
        //     $table->unsignedInteger('meeting_id')->nullable()->change();
            
        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        //     $table->foreign('bundle_id')->references('id')->on('bundle_courses')->onDelete('cascade');
        //     $table->foreign('meeting_id')->references('id')->on('bigbluemeetings')->onDelete('cascade');
        // });
        // Schema::table('courses', function (Blueprint $table) {
        //     $table->unsignedInteger('user_id')->nullable()->change();
        //     $table->unsignedInteger('category_id')->nullable()->change();
        //     $table->unsignedInteger('scnd_category_id')->nullable()->change();
        //     $table->unsignedInteger('subcategory_id')->nullable()->change();

        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        //     $table->foreign('scnd_category_id')->references('id')->on('secondary_categories')->onDelete('cascade');
        //     $table->foreign('subcategory_id')->references('id')->on('sub_categories')->onDelete('cascade');
        // });
        // Schema::table('courses_in_bundle', function (Blueprint $table) {
        //     $table->unsignedInteger('bundle_id')->nullable()->change();
        //     $table->unsignedInteger('course_id')->nullable()->change();
        //     $table->unsignedInteger('created_by')->nullable()->change();

        //     $table->foreign('bundle_id')->references('id')->on('bundle_courses')->onDelete('cascade');
        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        //     $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        // });
        // Schema::table('course_chapters', function (Blueprint $table) {
        //     $table->unsignedInteger('course_id')->nullable()->change();
        //     $table->unsignedInteger('user_id')->nullable()->change();

        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        // });
        // Schema::table('course_classes', function (Blueprint $table) {
        //     $table->unsignedInteger('course_id')->nullable()->change();
        //     $table->unsignedInteger('coursechapter_id')->nullable()->change();
        //     $table->unsignedInteger('user_id')->nullable()->change();
        //     $table->unsignedInteger('meeting_id')->nullable()->change();
        //     $table->unsignedInteger('url')->nullable()->change();

        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        //     $table->foreign('coursechapter_id')->references('id')->on('course_chapters')->onDelete('cascade');
        //     $table->foreign('meeting_id')->references('id')->on('bigbluemeetings')->onDelete('cascade');
        //     $table->foreign('url')->references('id')->on('quiz_topics')->onDelete('cascade');
        // });
        // Schema::table('course_payment_plan', function (Blueprint $table) {
        //     $table->unsignedInteger('course_id')->nullable()->change();
        //     $table->unsignedInteger('bundle_id')->nullable()->change();
        //     $table->unsignedInteger('created_by')->nullable()->change();
        //     $table->unsignedInteger('updated_by')->nullable()->change();

        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        //     $table->foreign('bundle_id')->references('id')->on('bundle_courses')->onDelete('cascade');
        //     $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        // });
        // Schema::table('course_progress', function (Blueprint $table) {
        //     $table->unsignedInteger('course_id')->nullable()->change();
        //     $table->unsignedInteger('user_id')->nullable()->change();

        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });
        //     $table->unsignedInteger('user_id')->nullable()->change();
        //     $table->unsignedInteger('instructor_id')->nullable()->change();
        //     $table->unsignedInteger('course_id')->nullable()->change();
        //     $table->unsignedInteger('bundle_id')->nullable()->change();
        //     $table->unsignedInteger('meeting_id')->nullable()->change();
        //     $table->unsignedBigInteger('transaction_id')->nullable()->change();
        
        //     $table->foreign('user_id')->references('id')->on('users');
        //     $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        //     $table->foreign('bundle_id')->references('id')->on('bundle_courses')->onDelete('cascade');
        //     $table->foreign('meeting_id')->references('id')->on('bigbluemeetings')->onDelete('cascade');
        //     $table->foreign('transaction_id')->references('id')->on('wallet_transactions')->onDelete('cascade');
        // });
        // Schema::table('order_installments', function (Blueprint $table) {
        //     $table->unsignedInteger('user_id')->nullable()->change();
        //     $table->unsignedInteger('order_id')->nullable()->change();
        //     $table->unsignedInteger('coupon_id')->nullable()->change();
        //     $table->unsignedBigInteger('transaction_id')->nullable()->change();

        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        //     $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
        //     $table->foreign('transaction_id')->references('id')->on('wallet_transactions')->onDelete('cascade');
        // });
        // Schema::table('order_payment_plan', function (Blueprint $table) {
        //     $table->unsignedInteger('user_id')->nullable()->change();
        //     $table->unsignedInteger('order_id')->nullable()->change();
        //     $table->unsignedInteger('coupon_id')->nullable()->change();
        //     $table->unsignedBigInteger('transaction_id')->nullable()->change();

        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        //     $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
        //     $table->foreign('transaction_id')->references('id')->on('wallet_transactions')->onDelete('cascade');
        // });
        // Schema::table('wallet', function (Blueprint $table) {
        //     $table->unsignedInteger('user_id')->nullable()->change();

        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });
        // Schema::table('wallet_transactions', function (Blueprint $table) {
        //     $table->unsignedInteger('user_id')->nullable()->change();
        //     $table->unsignedBigInteger('wallet_id')->nullable()->change();

        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('wallet_id')->references('id')->on('wallet')->onDelete('cascade');
        // });
        // Schema::table('wishlists', function (Blueprint $table) {
        //     $table->unsignedInteger('user_id')->nullable()->change();
        //     $table->unsignedInteger('course_id')->nullable()->change();
        //     $table->unsignedInteger('bundle_id')->nullable()->change();
        //     $table->unsignedInteger('meeting_id')->nullable()->change();

        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        //     $table->foreign('bundle_id')->references('id')->on('bundle_courses')->onDelete('cascade');
        //     $table->foreign('meeting_id')->references('id')->on('bigbluemeetings')->onDelete('cascade');
        // });
        // Schema::table('what_learns', function (Blueprint $table) {
        //     $table->unsignedInteger('course_id')->nullable()->change();

        //     $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
