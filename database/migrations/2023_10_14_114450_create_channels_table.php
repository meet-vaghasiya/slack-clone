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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('topic')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('creator_id');
            $table->boolean('is_private');
            $table->timestamps();

            // Define foreign key for creator/owner
            $table->foreign('workspace_id')->references('id')->on('workspaces');
            $table->foreign('creator_id')->references('id')->on('members');
            $table->unique(['workspace_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
