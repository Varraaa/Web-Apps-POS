<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel utama untuk menyimpan data siswa.
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 30)->unique();
            $table->string('name');
            $table->string('class_name', 30);
            $table->string('major', 100);
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->date('birth_date');
            $table->text('address');
            $table->enum('status', ['Aktif', 'Alumni', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
