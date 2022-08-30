<?php

use App\Models\Module\ProviderModule;
use App\Models\User;
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
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();

            // name
            $table->string('name')->index();

            // price
            $table->double('price', 60, 8)->index();

            $table->char('protocol', 5)->index()->default("tcp");

            $table->string('custom_domain')->nullable()->index();

            $table->string('local_address')->index();

            $table->unsignedSmallInteger('remote_port')->index()->nullable();

            $table->string('client_token')->index()->unique();

            $table->string('sk')->index()->nullable();

            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->unsignedBigInteger('server_id')->index();
            $table->foreign('server_id')->references('id')->on('servers')->cascadeOnDelete();

            // use_encryption
            $table->boolean('use_encryption')->default(false)->index();

            // use_compression
            $table->boolean('use_compression')->default(false)->index();

            // host_id
            $table->unsignedBigInteger('host_id')->index();
            
            // status
            $table->string('status')->default('pending')->index();

            $table->timestamp('suspended_at')->nullable()->index();

            // soft delete
            $table->softDeletes();


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
        Schema::dropIfExists('hosts');
    }
};
