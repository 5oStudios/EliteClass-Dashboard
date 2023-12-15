<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoriesIntoUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('users')){
            Schema::table('users', function (Blueprint $table) {
              //  $table->integer('age')->nullable()->change();
                $table->integer('main_category')->nullable();
                $table->integer('sub_category')->nullable();
                $table->integer('ch_sub_category')->nullable();
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
        
        if(Schema::hasTable('users')){
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('main_category');
                $table->dropColumn('sub_category');
                $table->dropColumn('ch_sub_category');
            });
        }
    }
}
