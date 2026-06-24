<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('doctor')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->string('country')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('country');
            $table->boolean('is_verified')->default(false)->after('avatar');
            $table->string('locale')->default('en')->after('is_verified');
        });

        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('license_number')->nullable();
            $table->string('specialization')->nullable();
            $table->string('clinic_name')->nullable();
            $table->text('bio')->nullable();
            $table->string('license_file')->nullable();
            $table->timestamps();
        });

        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('country');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedSmallInteger('avg_turnaround_days')->default(7);
            $table->decimal('starting_price', 10, 2)->default(0);
            $table->string('approval_status')->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('specialties')->nullable();
            $table->timestamps();
        });

        Schema::create('lab_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('turnaround_days')->default(7);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lab_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_name');
            $table->string('tooth_number')->nullable();
            $table->string('material')->nullable();
            $table->string('shade')->nullable();
            $table->string('status')->default('received');
            $table->decimal('cost', 10, 2);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->boolean('is_express')->default(false);
            $table->unsignedSmallInteger('turnaround_days')->nullable();
            $table->timestamp('expected_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('label');
            $table->unsignedTinyInteger('sort_order');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expected_at')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->string('message');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lab_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->text('review')->nullable();
            $table->timestamps();
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('completed');
            $table->string('reference')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('label');
            $table->string('last_four')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('icon')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lab_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('approvable');
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('rate', 5, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_settings');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('order_logs');
        Schema::dropIfExists('order_stages');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('lab_services');
        Schema::dropIfExists('labs');
        Schema::dropIfExists('doctor_profiles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'country', 'avatar', 'is_verified', 'locale']);
        });
    }
};
