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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();

            $table->string('server_address')->index();
            $table->string('server_port')->index();
            $table->unsignedSmallInteger('dashboard_port');
            $table->string('dashboard_user')->nullable();
            $table->string('dashboard_password')->nullable();

            $table->string('token')->nullable()->index();

            $table->boolean('allow_http')->index()->default(1);
            $table->boolean('allow_https')->index()->default(1);
            $table->boolean('allow_tcp')->index()->default(1);
            $table->boolean('allow_udp')->index()->default(1);
            $table->boolean('allow_stcp')->index()->default(1);

            $table->unsignedSmallInteger('min_port')->default(10000)->index();
            $table->unsignedSmallInteger('max_port')->default(60000)->index();

            $table->unsignedInteger('max_tunnels')->default(100)->index();
            $table->unsignedInteger('tunnels')->default(0)->index();

            $table->string('status')->default('maintenance');

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
        Schema::dropIfExists('servers');
    }
};
