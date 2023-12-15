<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScndCatetoryIdIntoChildCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('child_categories', function (Blueprint $table) {
            $table->integer('scnd_category_id')->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('child_categories', function (Blueprint $table) {
            $table->dropColumn(
                'scnd_category_id'
            );
        });
    }
}
