<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_detail_id');
            $table->unsignedBigInteger('department_id');
            $table->foreign('order_detail_id')
            ->references('id')->on('order_details')->onDelete('cascade');
            $table->foreign('department_id')
            ->references('id')->on('departments')->onDelete('cascade');
            $table->enum('status', ['pending','processing', 'ready','canceled'])->default('pending');
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
        Schema::dropIfExists('department_order_details');
    }
}
