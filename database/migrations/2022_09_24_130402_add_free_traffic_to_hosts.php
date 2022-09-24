<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hosts', function (Blueprint $table) {
            //
            $table->unsignedFloat('free_traffic')->index()->default(0);

            // 上次补给时间
            $table->datetime('last_add_free_traffic_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hosts', function (Blueprint $table) {
            //

            $table->dropColumn('free_traffic');

            $table->dropColumn('last_add_free_traffic_at');
        });
    }
};
