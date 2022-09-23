<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('category_id')->nullable();
           
            $table->tinyInteger('is_available')->default(1);
            $table->tinyInteger('in_orderes')->default(1);
            $table->integer('order')->nullable();
            $table->integer('menu_order')->nullable();
            $table->bigInteger('menu_cat_id')->nullable();
            $table->decimal('monthly_avg', 8,2)->default(0.00);
            $table->enum('rate_star', ['1', '2', '3','4','5'])->default(4);
            $table->decimal('sell_price', 15,2)->default(0.00);
            $table->integer('parent_id')->nullable();
            $table->foreign('category_id')
            ->references('id')->on('categories')->onDelete('cascade');
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
        Schema::dropIfExists('items');
    }
}
