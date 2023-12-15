<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BundlePaymentPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bundle_courses', function (Blueprint $table) {
            $table->boolean('installment')->default(0);
            $table->integer('installment_price')->nullable();
            $table->integer('total_installments')->default(3);
        });
       
        Schema::create('courses_in_bundle', function (Blueprint $table) {
            $table->id();
            $table->integer('bundle_id');
            $table->integer('course_id');
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
        
        Schema::table('course_payment_plan', function (Blueprint $table) {
            $table->integer('bundle_id')->nullable();
            $table->integer('course_id')->nullable()->change();
        });
        
        Schema::table('orders',function(Blueprint $table){
            $table->integer('meeting_id')->nullable()->after('bundle_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('bundle_courses', function (Blueprint $table) {
            $table->dropColumn('installment');
            $table->dropColumn('installment_price');
            $table->dropColumn('total_installments');
        });
        
        Schema::dropIfExists('courses_in_bundle');
        
        Schema::table('course_payment_plan', function (Blueprint $table) {
            $table->dropColumn('bundle_id');
        });
        
        Schema::table('orders',function(Blueprint $table){
            $table->dropColumn('meeting_id');
        });
    }
}
