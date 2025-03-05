<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamp('order_date')->nullable();
            $table->time('delivery_time')->nullable();
            $table->json('order_list'); // информация о заказе в формате JSON
            $table->string('phone');
            $table->string('email'); // почта
            $table->string('address'); // адрес
            $table->enum('status', Order::statuses())->default(Order::STATUS_PENDING);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // id пользователя, который сделал заказ
            $table->timestamps(); // время создания и обновления записи
        });

        /*$request->validate([
            'status' => 'in:' . implode(',', Order::statuses()),
        ]);*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
