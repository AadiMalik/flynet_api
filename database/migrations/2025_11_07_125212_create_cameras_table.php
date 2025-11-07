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
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name')->nullable();
            $table->string('ip_address'); // IP address or P2P ID
            $table->string('protocol'); // Protocol type (RTSP, P2P, RTMP, etc.)
            $table->string('manufacturer')->nullable();
            $table->string('stream_url')->nullable(); // URL (for RTMP, RTSP URL, etc.)
            $table->string('username')->nullable(); // Camera username
            $table->string('password')->nullable(); // Camera password
            $table->string('port')->nullable(); // Port (default or custom)
            $table->string('location')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('status')->default('enabled');
            $table->integer('business_id')->nullable();
            $table->integer('location_id')->nullable();

            $table->integer('createdby_id')->nullable();
            $table->integer('updatedby_id')->nullable();
            $table->integer('deletedby_id')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('cameras');
    }
};
