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
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); // Автоматически создает первичный ключ `id`
            $table->unsignedBigInteger('user_id'); // ID пользователя
            $table->unsignedInteger('product_id'); // ID продукта
            $table->enum('product_type', ['pizza', 'drink']); // Тип продукта
            $table->unsignedInteger('quantity'); // Количество продукта
            $table->timestamps(); // Автоматически создает `created_at` и `updated_at`

            // Внешний ключ для связи с таблицей users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
