<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(table: 'counterparties', callback: function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name', length: 255);
            $table->string(column: 'ogrn', length: 255);
            $table->string(column: 'address', length: 255);
            $table->foreignId(column: 'user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'counterparties');
    }
};
