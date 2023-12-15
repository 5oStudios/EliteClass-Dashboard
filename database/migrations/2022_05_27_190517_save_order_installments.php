<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SaveOrderInstallments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('course_classes', function (Blueprint $table) {
            $table->longtext('long_text')->nullable();
        });
        Schema::table('course_payment_plan', function (Blueprint $table) {
            $table->date('due_date')->nullable();
        });
        
         Schema::create('order_payment_plan', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('amount');
            $table->date('due_date')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        
        Schema::table('course_payment_plan', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
        
         Schema::dropIfExists('order_payment_plan');
    }
}
