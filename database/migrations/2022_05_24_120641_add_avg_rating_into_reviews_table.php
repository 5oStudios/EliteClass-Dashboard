<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvgRatingIntoReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('review_ratings',function(Blueprint $table){
            $table->float('avg_rating',8,1)
                    ->default(0)
//                    ->default(DB::raw("( ( AVG(price)+ avg(`value`)+ avg(learn) ) /3)"))
                    ->after("value");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('review_ratings',function(Blueprint $table){
            $table->dropColumn('avg_rating');
        });
        //
    }
}
