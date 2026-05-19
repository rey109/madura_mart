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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_diskon');
            $table->enum('tipe_diskon', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('nilai_diskon', 15, 2);
            $table->decimal('min_transaksi', 15, 2)->default(0);
            $table->decimal('max_diskon', 15, 2)->nullable();
            $table->unsignedBigInteger('id_barang')->nullable();
            $table->integer('min_qty')->default(1);
            $table->dateTime('tgl_mulai')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('id_barang')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
