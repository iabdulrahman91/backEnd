<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_requests', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedBigInteger('listing_id');
            $table->foreign('listing_id')->references('id')->on('listings');

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');


            $table->json('days');

            $table->float('cost', 8, 2);
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('rent_requests');
    }
}
