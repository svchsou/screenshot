<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('storage_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['local', 'ftp', 's3', 'spaces']);
            $table->text('credentials'); // encrypted JSON
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('storage_destinations');
    }
};
