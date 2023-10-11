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
            $table->string('content');  // todo convert this into text later as message should be more than 255 chat
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('sender_id')->comment('Member id from members table');
            $table->unsignedBigInteger('receiver_id')->comment('Member id from members table');
            $table->unsignedBigInteger('parent_message_id')->nullable();
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces');
            $table->foreign('sender_id')->references('id')->on('members');
            $table->foreign('receiver_id')->references('id')->on('members');
            $table->foreign('parent_message_id')->references('id')->on('messages');
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
