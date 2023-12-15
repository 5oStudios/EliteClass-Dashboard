<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsPurchasableIntoCourseChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->boolean('is_purchasable')->default(1)->after('discount_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->dropColumn('is_purchasable');
        });
    }
}
