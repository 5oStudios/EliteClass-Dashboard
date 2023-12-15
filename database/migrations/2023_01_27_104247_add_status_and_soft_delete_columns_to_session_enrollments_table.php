<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndSoftDeleteColumnsToSessionEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session_enrollments', function (Blueprint $table) {
            $table->boolean('status')->default(1)->after('user_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('session_enrollments', function (Blueprint $table) {
            $table->dropColumn(['status', 'deleted_at']);
        });
    }
}
