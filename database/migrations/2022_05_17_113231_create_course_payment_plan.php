<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursePaymentPlan extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('installment')->default(0);
            $table->integer('installment_price')->nullable();
            $table->integer('total_installments')->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'bundle_course_id')) {
                $table->string('bundle_course_id', 191)->nullable();
            }
        });
        Schema::table('course_classes', function (Blueprint $table) {
            $table->boolean('unlock_installment')->nullable();
        });
        Schema::create('course_payment_plan', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id');
            $table->integer('sort');
            $table->integer('amount');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('installment');
            $table->dropColumn('installment_price');
            $table->dropColumn('total_installments');
        });
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'bundle_course_id')) {
                $table->dropColumn('bundle_course_id');
            }
            $table->dropColumn('installment');
            $table->dropColumn('installment_price');
            $table->dropColumn('total_installments');
        });
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropColumn('unlock_installment');
        });
        Schema::dropIfExists('course_payment_plan');
    }

}
