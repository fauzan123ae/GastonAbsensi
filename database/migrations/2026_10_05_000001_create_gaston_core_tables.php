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
        // 1. SPORTS TABLE
        Schema::create('sports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->timestamps();
        });

        // 2. LOCATIONS TABLE
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->timestamps();
        });

        // 3. SPORT SESSIONS TABLE
        Schema::create('sport_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained('sports')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->date('date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['draft', 'open', 'ongoing', 'completed', 'cancelled'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. SESSION PARTICIPANTS TABLE
        Schema::create('session_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sport_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('registration_source', ['whatsapp', 'manual'])->default('manual');
            $table->enum('status', ['registered', 'cancelled'])->default('registered')->index();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            $table->unique(['session_id', 'user_id'], 'uniq_session_user_participant');
        });

        // 5. ATTENDANCES TABLE
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sport_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('checked_in_at')->useCurrent()->index();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('method', ['qr', 'manual'])->default('qr');
            $table->enum('status', ['present', 'cancelled'])->default('present')->index();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'user_id'], 'uniq_session_user_attendance');
        });

        // 6. PERMISSION REQUESTS TABLE
        Schema::create('permission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sport_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason');
            $table->string('proof')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'user_id'], 'uniq_session_user_permission');
        });

        // 7. AUDIT LOGS TABLE
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->index();
            $table->string('target_type')->nullable()->index();
            $table->unsignedBigInteger('target_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('permission_requests');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('session_participants');
        Schema::dropIfExists('sport_sessions');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('sports');
    }
};
