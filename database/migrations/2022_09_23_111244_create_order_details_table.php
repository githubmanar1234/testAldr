<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('item_id');
            $table->foreign('order_id')
            ->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('item_id')
            ->references('id')->on('items')->onDelete('cascade');
            $table->decimal('total_price', 12,2)->default(0.00);
            $table->integer('count')->default(1);
            $table->tinyInteger('is_fired')->default(0);
            $table->enum('status', ['pending', 'preparing','done','canceled'])->default('pending');
            $table->string('notes')->nullable();
            $table->double('note_price')->nullable();
            $table->time('delay')->nullable();
            $table->double('cost',10,2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
