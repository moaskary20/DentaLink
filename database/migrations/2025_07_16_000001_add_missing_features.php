<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            $table->string('license_file')->nullable()->after('logo');
        });

        Schema::table('order_stages', function (Blueprint $table) {
            $table->timestamp('doctor_approved_at')->nullable()->after('is_current');
            $table->timestamp('lab_approved_at')->nullable()->after('doctor_approved_at');
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->string('direction')->default('doctor_to_lab')->after('lab_id');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('body');
            $table->string('attachment_type')->nullable()->after('attachment_path');
        });

        Schema::create('order_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('type')->default('image');
            $table->string('original_name')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lab_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('paid');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_attachments');
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_type']);
        });
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropColumn('direction');
        });
        Schema::table('order_stages', function (Blueprint $table) {
            $table->dropColumn(['doctor_approved_at', 'lab_approved_at']);
        });
        Schema::table('labs', function (Blueprint $table) {
            $table->dropColumn('license_file');
        });
    }
};
