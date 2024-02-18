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
        Schema::create('users', function (Blueprint $table) {
           $table->id();
            $table->string('name'); // * Nama Lengkap
            $table->string('username')->unique(); // * Username
            $table->string('email')->unique(); // * Email
            $table->string('phone_number')->unique(); // * Nomor Telepon
            $table->string('address')->nullable(); // * Alamat
            $table->string('subdistrict')->nullable(); // * Kelurahan
            $table->string('district')->nullable(); // * Kecamatan
            $table->string('city')->nullable(); // * Kota / Kabupaten
            $table->string('province')->nullable(); // * Provinsi
            $table->string('postal_code')->nullable(); // * Kode Pos
            $table->string('profile_picture')->nullable(); // * Foto Profil
            $table->date('date_of_birth')->nullable(); // * Tanggal Lahir
            $table->string('place_of_birth')->nullable(); // * Tempat Lahir
            $table->enum('gender', ['male','female','other'])->nullable(); // * Jenis Kelamin
            $table->enum('position', ['superadmin', 'admin', 'user'])->default('user'); // * Jabatan
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active'); // * Status
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
