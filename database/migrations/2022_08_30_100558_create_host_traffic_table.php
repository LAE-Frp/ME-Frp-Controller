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
        Schema::create('host_traffic', function (Blueprint $table) {
            $table->id();

            $table->date('date')->index();
            $table->unsignedBigInteger('bytes')->nullable()->index();

            $table->unsignedBigInteger('host_id')->index();
            $table->foreign('host_id')->references('id')->on('hosts')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tunnel_traffic');
    }
};
