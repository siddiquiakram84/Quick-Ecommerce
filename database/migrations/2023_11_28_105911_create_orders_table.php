<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('total_price', 8, 2)->default(0);
            $table->enum('status', ['Delivered', 'Pending', 'Processing']);
            $table->tinyInteger('payment_status')->default(0); // 0: Pending, 1: Paid
            $table->text('delivery_address')->nullable();
            $table->text('delivery_method')->nullable();
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
