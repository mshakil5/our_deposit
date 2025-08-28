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
        Schema::create('discuss_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('discussion_id')->nullable();
            $table->foreign('discussion_id')->references('id')->on('discussions');
            $table->string('date')->nullable();
            $table->longText('description')->nullable();
            $table->longText('note')->nullable();
            $table->longText('summery')->nullable();
            $table->longText('member')->nullable();
            $table->string('document')->nullable();
            $table->boolean('status')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discuss_histories');
    }
};
