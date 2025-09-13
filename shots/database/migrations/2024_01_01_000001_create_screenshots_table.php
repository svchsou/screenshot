<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('screenshots', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('slug', 12)->unique();
            $table->string('disk');
            $table->string('path');
            $table->string('mime');
            $table->unsignedBigInteger('size_bytes');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->string('ip_hash', 64)->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->char('delete_token', 32)->unique();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('screenshots');
    }
};
