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
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_type')->default('loan')->comment('loan, payment'); // loan = peminjaman, payment = pembayaran (pembayaran pinjaman dari karyawan akan menambah saldo kas jika dibayarkan ke Kas Kecil)
            $table->foreignId('employee_id')->constrained(); // pegawai yang meminjam atau membayar
            $table->date('paid_at'); // tanggal pembayaran atau peminjaman
            $table->foreignId('cost_id')->nullable()->constrained()->cascadeOnDelete(); // menambahkan id cost jika pembayaran dari kas kecil (mengurangi saldo kas kecil)
            $table->boolean('big_cash')->default(false); // true = kas besar, false = kas kecil (jika pembayaran dari kas besar, maka tidak perlu menambahkan id cost)
            $table->decimal('amount', 10, 2)->unsigned(); // jumlah pembayaran atau peminjaman
            $table->string('description')->nullable(); // keterangan pembayaran atau peminjaman
            $table->foreignId('created_by')->constrained('users'); // pembuat pembayaran atau peminjaman (user yang membuat pembayaran atau peminjaman)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_loans');
    }
};
