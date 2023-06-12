<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sender_id');
            $table->string('sender_name');
            $table->string('sender_email');
            $table->string('sender_telefono')->nullable();
            $table->unsignedBigInteger('addresse_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title');
            $table->date('event_date')->format('d-m-Y');
            $table->unsignedBigInteger('location_id');
            $table->text('description');
            $table->text('message')->nullable();
            $table->boolean('read')->default(false);
            
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('addresse_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('messages')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};