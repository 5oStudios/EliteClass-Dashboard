<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScndLevelCategory extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('secondary_categories')) {

            Schema::create('secondary_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('category_id')->nullable();
                $table->string('title', 191)->nullable();
                $table->string('icon', 191)->nullable();
                $table->string('slug', 191)->nullable();
                $table->enum('status', array('1', '0'));
                $table->timestamps();
            });
            Schema::table('sub_categories', function (Blueprint $table) {
                $table->integer('scnd_category_id')->after('category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('secondary_categories');
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn('scnd_category_id')->after('category_id');
        });
    }

}
