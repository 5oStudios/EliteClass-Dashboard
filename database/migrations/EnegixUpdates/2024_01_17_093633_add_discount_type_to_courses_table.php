<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountTypeToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('courses', 'discount_type')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('discount_type')->nullable();
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
        if (Schema::hasColumn('courses', 'discount_type')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('discount_type');
            });
        }
    }
}
