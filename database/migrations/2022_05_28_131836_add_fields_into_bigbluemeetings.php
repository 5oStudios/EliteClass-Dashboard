<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsIntoBigbluemeetings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->after('id', function($table){
              $table->string('owner_id');
            });
            $table->after('meetingname', function($table){
              $table->string('image');
              $table->integer('main_category')->nullable();
              $table->integer('scnd_category_id')->nullable();
              $table->integer('sub_category')->nullable();
              $table->integer('ch_sub_category')->nullable();
              $table->double('price')->nullable();
              $table->double('discount_price')->nullable();
              $table->string('bigblue_url')->nullable();
              $table->string('time_zone')->nullable();
              $table->dropColumn('presen_name');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bigbluemeetings', function (Blueprint $table) {
            $table->dropColumn(
                'owner_id',
                'image',
                'main_category',
                'sub_category',
                'ch_sub_category',
                'price',
                'discount_price',
                'bigblue_url',
                'time_zone'
            );
        });
    }
}
