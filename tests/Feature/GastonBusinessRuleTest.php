<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\PermissionRequest;
use App\Models\SessionParticipant;
use App\Models\Sport;
use App\Models\SportSession;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GastonBusinessRuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;
    protected Sport $sport;
    protected Location $location;
    protected SportSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'member_id' => 'GST-00000',
            'name' => 'Admin Gaston',
            'email' => 'admin@gaston.test',
            'password' => bcrypt('password'),
            'qr_token' => 'GST-MBR-ADMIN00001',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->member = User::create([
            'member_id' => 'GST-00001',
            'name' => 'Fauzan Test',
            'email' => 'fauzan@gaston.test',
            'password' => bcrypt('password'),
            'qr_token' => 'GST-MBR-TEST00001',
            'role' => 'member',
            'status' => 'active',
        ]);

        $this->sport = Sport::create(['name' => 'Futsal', 'status' => 'active']);
        $this->location = Location::create(['name' => 'GOR Manahan', 'status' => 'active']);

        $this->session = SportSession::create([
            'sport_id' => $this->sport->id,
            'location_id' => $this->location->id,
            'date' => now()->toDateString(),
            'start_time' => '19:00:00',
            'end_time' => '21:00:00',
            'status' => 'open',
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * Test Authentication & Role protection
     */
    public function test_admin_can_access_admin_dashboard_and_member_cannot(): void
    {
        // Member trying to access admin dashboard should be redirected to member dashboard
        $response = $this->actingAs($this->member)->get(route('admin.dashboard'));
        $response->assertRedirect(route('member.dashboard'));

        // Admin can access admin dashboard
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    /**
     * Test QR attendance and verification
     */
    public function test_member_can_be_verified_and_confirmed_for_attendance(): void
    {
        // Register member to session
        SessionParticipant::create([
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'registration_source' => 'whatsapp',
            'status' => 'registered',
        ]);

        $service = new AttendanceService();
        $verification = $service->verifyMemberForSession($this->session, $this->member->qr_token);

        $this->assertTrue($verification['success']);
        $this->assertEquals('READY_FOR_CONFIRMATION', $verification['code']);
        $this->assertTrue($verification['is_registered']);

        // Admin confirms attendance
        $attendance = $service->confirmAttendance(
            session: $this->session,
            member: $this->member,
            admin: $this->admin,
            method: 'qr'
        );

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'method' => 'qr',
            'status' => 'present',
        ]);
    }

    /**
     * Test Duplicate Attendance Prevention
     */
    public function test_duplicate_attendance_is_prevented(): void
    {
        SessionParticipant::create([
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'status' => 'registered',
        ]);

        $service = new AttendanceService();
        $service->confirmAttendance(
            session: $this->session,
            member: $this->member,
            admin: $this->admin,
            method: 'qr'
        );

        // Second verification should report already checked in
        $verifyAgain = $service->verifyMemberForSession($this->session, $this->member->qr_token);
        $this->assertFalse($verifyAgain['success']);
        $this->assertEquals('ALREADY_CHECKED_IN', $verifyAgain['code']);
    }

    /**
     * Test Unregistered Member becomes Walk-In
     */
    public function test_unregistered_member_prompts_and_records_as_walk_in(): void
    {
        $service = new AttendanceService();
        $verification = $service->verifyMemberForSession($this->session, $this->member->qr_token);

        // Verification indicates not registered but allows walk-in decision
        $this->assertTrue($verification['success']);
        $this->assertEquals('NOT_REGISTERED', $verification['code']);
        $this->assertFalse($verification['is_registered']);

        // Admin records walk-in
        $attendance = $service->recordWalkIn(
            session: $this->session,
            member: $this->member,
            admin: $this->admin,
            method: 'qr'
        );

        $this->assertDatabaseHas('session_participants', [
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'registration_source' => 'manual',
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'status' => 'present',
        ]);
    }

    /**
     * Test Inactive Member is Rejected
     */
    public function test_inactive_member_attendance_is_rejected(): void
    {
        $this->member->update(['status' => 'inactive']);

        $service = new AttendanceService();
        $verification = $service->verifyMemberForSession($this->session, $this->member->qr_token);

        $this->assertFalse($verification['success']);
        $this->assertEquals('INACTIVE', $verification['code']);
    }

    /**
     * Test Attendance on Cancelled Session is Blocked
     */
    public function test_attendance_on_cancelled_session_is_blocked(): void
    {
        $this->session->update(['status' => 'cancelled']);

        $service = new AttendanceService();
        $verification = $service->verifyMemberForSession($this->session, $this->member->qr_token);

        $this->assertFalse($verification['success']);
        $this->assertEquals('SESSION_CLOSED', $verification['code']);
    }

    /**
     * Test Attendance Rate Formula with Approved Permission
     */
    public function test_approved_permission_is_excluded_from_absent_in_attendance_rate(): void
    {
        $member2 = User::create([
            'member_id' => 'GST-00002',
            'name' => 'Budi Perm',
            'email' => 'budi@gaston.test',
            'password' => bcrypt('password'),
            'qr_token' => 'GST-MBR-TEST00002',
            'role' => 'member',
            'status' => 'active',
        ]);

        // 2 participants registered
        SessionParticipant::create(['session_id' => $this->session->id, 'user_id' => $this->member->id, 'status' => 'registered']);
        SessionParticipant::create(['session_id' => $this->session->id, 'user_id' => $member2->id, 'status' => 'registered']);

        // Member 1 attends
        $service = new AttendanceService();
        $service->confirmAttendance($this->session, $this->member, $this->admin);

        // Member 2 has approved permission
        PermissionRequest::create([
            'session_id' => $this->session->id,
            'user_id' => $member2->id,
            'reason' => 'Demam',
            'status' => 'approved',
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $summary = $service->getSessionSummary($this->session);

        // Formula: 1 present / (2 registered - 1 permission) * 100 = 100%
        $this->assertEquals(2, $summary['total_registered']);
        $this->assertEquals(1, $summary['present']);
        $this->assertEquals(1, $summary['permission']);
        $this->assertEquals(0, $summary['absent']);
        $this->assertEquals(100.0, $summary['attendance_rate']);
    }

    /**
     * Test admin can view and update own profile with password
     */
    public function test_admin_can_update_profile_and_change_password(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.profile'));
        $response->assertStatus(200);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.profile.update'), [
            'name' => 'Admin Gaston Updated',
            'email' => 'admin@gaston.test',
            'phone' => '081234567890',
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'name' => 'Admin Gaston Updated',
            'phone' => '081234567890',
        ]);

        $this->admin->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $this->admin->password));
    }

    /**
     * Test forgot password reset link flow
     */
    public function test_forgot_password_can_send_reset_link_email(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);

        $postResponse = $this->post(route('password.email'), [
            'email' => $this->admin->email,
        ]);

        $postResponse->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $this->admin->email,
        ]);
    }

    /**
     * Test login throttling after 5 attempts
     */
    public function test_login_throttling_triggers_after_max_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), [
                'email' => 'throttletest@gaston.test',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->post(route('login.post'), [
            'email' => 'throttletest@gaston.test',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan login', session('errors')->first('email'));
    }

    /**
     * Test member cannot access any admin routes
     */
    public function test_member_cannot_access_admin_management_and_audit_logs(): void
    {
        $response = $this->actingAs($this->member)->get(route('admin.audit-logs.index'));
        $response->assertRedirect(route('member.dashboard'));

        $response = $this->actingAs($this->member)->get(route('admin.sessions.index'));
        $response->assertRedirect(route('member.dashboard'));
    }

    /**
     * Test permission proof file authorization
     */
    public function test_permission_proof_file_authorization(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        $file = \Illuminate\Http\UploadedFile::fake()->create('surat_dokter.pdf', 100, 'application/pdf');
        $path = $file->store('permissions', 'local');

        $permission = PermissionRequest::create([
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'reason' => 'Sakit flu',
            'proof' => $path,
            'status' => 'pending',
        ]);

        $otherMember = User::create([
            'member_id' => 'GST-00099',
            'name' => 'Other Member',
            'email' => 'other@gaston.test',
            'password' => bcrypt('password'),
            'qr_token' => 'GST-MBR-OTHER001',
            'role' => 'member',
            'status' => 'active',
        ]);

        // Unauthorized user gets 403
        $response = $this->actingAs($otherMember)->get(route('permissions.proof', $permission));
        $response->assertStatus(403);

        // Owner member can access
        $response = $this->actingAs($this->member)->get(route('permissions.proof', $permission));
        $response->assertStatus(200);

        // Admin can access
        $response = $this->actingAs($this->admin)->get(route('permissions.proof', $permission));
        $response->assertStatus(200);
    }

    /**
     * Test admin cannot be recorded or verified as attendee
     */
    public function test_admin_cannot_be_verified_or_confirmed_as_attendee(): void
    {
        $service = new AttendanceService();
        $verification = $service->verifyMemberForSession($this->session, $this->admin->qr_token);

        // verifyMemberForSession must not find admin account
        $this->assertFalse($verification['success']);
        $this->assertEquals('NOT_FOUND', $verification['code']);

        // Controller confirm rejects non-member
        $response = $this->actingAs($this->admin)->postJson(route('admin.sessions.scan.confirm', $this->session), [
            'member_id' => $this->admin->id,
            'is_walk_in' => true,
        ]);

        $response->assertStatus(422);
        $this->assertFalse($response->json('success'));
        $this->assertEquals('Akun admin tidak dapat dicatat sebagai peserta kehadiran.', $response->json('message'));
    }

    /**
     * Test walk-in does not increase attendance rate and is filtered properly
     */
    public function test_walk_in_does_not_inflate_attendance_rate_and_reconciles(): void
    {
        $member2 = User::create([
            'member_id' => 'GST-00002',
            'name' => 'Walk In Person',
            'email' => 'walkin@gaston.test',
            'password' => bcrypt('password'),
            'qr_token' => 'GST-MBR-TEST00002',
            'role' => 'member',
            'status' => 'active',
        ]);

        // Only member 1 is originally registered
        SessionParticipant::create(['session_id' => $this->session->id, 'user_id' => $this->member->id, 'status' => 'registered']);

        $service = new AttendanceService();

        // Member 2 attends as walk-in
        $walkInAtt = $service->recordWalkIn($this->session, $member2, $this->admin);
        $this->assertTrue($walkInAtt->is_walk_in);

        // Member 1 has NOT attended yet
        $summary = $service->getSessionSummary($this->session);
        // Total registered = 1, Present = 1 (walkin), Registered Present = 0.
        // Rate should be 0% because walk-in does not count in numerator/denominator of registered attendance rate!
        $this->assertEquals(1, $summary['total_registered']);
        $this->assertEquals(1, $summary['present']);
        $this->assertEquals(1, $summary['absent']);
        $this->assertEquals(0.0, $summary['attendance_rate']);

        // Check reconciled list filter
        $walkInList = $service->getReconciledAttendanceList($this->session, 'walk-in');
        $this->assertCount(1, $walkInList);
        $this->assertEquals('Walk-in', $walkInList[0]->status);
        $this->assertEquals($member2->id, $walkInList[0]->user->id);
    }

    /**
     * Test approved permission member who still attends is not double counted
     */
    public function test_approved_permission_who_attends_is_not_double_counted(): void
    {
        SessionParticipant::create(['session_id' => $this->session->id, 'user_id' => $this->member->id, 'status' => 'registered']);

        // Member has approved permission
        PermissionRequest::create([
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'reason' => 'Ada acara',
            'status' => 'approved',
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        // But member actually attends!
        $service = new AttendanceService();
        $service->confirmAttendance($this->session, $this->member, $this->admin);

        $summary = $service->getSessionSummary($this->session);

        // Since member is present, permission count should be 0 (not counted as approved permission),
        // registered = 1, present = 1, absent = 0, rate = 100%
        $this->assertEquals(1, $summary['total_registered']);
        $this->assertEquals(1, $summary['present']);
        $this->assertEquals(0, $summary['permission']);
        $this->assertEquals(0, $summary['absent']);
        $this->assertEquals(100.0, $summary['attendance_rate']);
    }

    /**
     * Test permission submission validations, resubmit rejected permission, review note, and cancellation
     */
    public function test_permission_lifecycle_validations_resubmission_and_cancellation(): void
    {
        $permService = new PermissionService();

        // 1. Member not registered in session should be rejected
        try {
            $permService->submitPermission($this->member, [
                'session_id' => $this->session->id,
                'reason' => 'Sakit perut',
            ]);
            $this->fail('Expected validation exception for unregistered member');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('session_id', $e->errors());
        }

        // Register member
        SessionParticipant::create(['session_id' => $this->session->id, 'user_id' => $this->member->id, 'status' => 'registered']);

        // 2. Member can submit permission
        $perm = $permService->submitPermission($this->member, [
            'session_id' => $this->session->id,
            'reason' => 'Sakit perut',
        ]);
        $this->assertEquals('pending', $perm->status);

        // 3. Duplicate pending permission cannot be submitted
        try {
            $permService->submitPermission($this->member, [
                'session_id' => $this->session->id,
                'reason' => 'Sakit lagi',
            ]);
            $this->fail('Expected validation exception for duplicate pending permission');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('session_id', $e->errors());
        }

        // 4. Admin rejects permission with review note
        $permService->reviewPermission($perm, 'rejected', $this->admin, 'Surat dokter tidak dilampirkan');
        $perm->refresh();
        $this->assertEquals('rejected', $perm->status);
        $this->assertEquals('Surat dokter tidak dilampirkan', $perm->review_note);

        // 5. Eligible sessions includes this session again because permission was rejected
        $eligibleData = $permService->getMemberPermissionData($this->member);
        $this->assertTrue($eligibleData['eligibleSessions']->contains('id', $this->session->id));

        // 6. Member can re-submit rejected permission
        $resubmitted = $permService->submitPermission($this->member, [
            'session_id' => $this->session->id,
            'reason' => 'Sakit perut + sudah ada surat',
        ]);
        $this->assertEquals($perm->id, $resubmitted->id);
        $this->assertEquals('pending', $resubmitted->status);
        $this->assertNull($resubmitted->review_note);

        // 7. Member can cancel pending permission
        $cancelled = $permService->cancelPendingPermission($resubmitted, $this->member);
        $this->assertTrue($cancelled);
        $this->assertDatabaseMissing('permission_requests', ['id' => $perm->id]);
    }

    /**
     * Test ReportService filters and CSV export sanitize formula injection
     */
    public function test_report_service_filters_and_csv_export_sanitization(): void
    {
        $reportService = new \App\Services\ReportService();

        SessionParticipant::create(['session_id' => $this->session->id, 'user_id' => $this->member->id, 'status' => 'registered']);

        $attService = new AttendanceService();
        $att = $attService->confirmAttendance($this->session, $this->member, $this->admin, method: 'manual', notes: '=SUM(1,2)');

        // Test Report Data filter by member_id
        $data = $reportService->getReportData([
            'member_id' => $this->member->id,
            'method' => 'manual',
        ]);

        $this->assertEquals(1, $data['totalRegistered']);
        $this->assertEquals(1, $data['totalPresent']);
        $this->assertEquals(0, $data['totalAbsent']);
        $this->assertEquals(100.0, $data['attendanceRate']);

        // Test Export CSV
        $response = $reportService->exportCsv([
            'member_id' => $this->member->id,
            'method' => 'manual',
        ]);

        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        // Check BOM
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csvContent);

        // Check sanitized formula: '=SUM(1,2)' should become ''=SUM(1,2)'
        $this->assertStringContainsString("'=SUM(1,2)", $csvContent);
    }

    /**
     * Test session status guards, state machine transitions, and auto-reject on cancellation
     */
    public function test_session_status_guard_transitions_and_cancellation_cascade(): void
    {
        $sessionService = new \App\Services\SessionService();
        $attService = new AttendanceService();

        // 1. canAcceptAttendance is false if date is not today
        $futureSession = SportSession::create([
            'sport_id' => $this->sport->id,
            'location_id' => $this->location->id,
            'date' => now('Asia/Jakarta')->addDays(2)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'status' => 'open',
        ]);
        $this->assertFalse($futureSession->canAcceptAttendance());

        // 2. Invalid state machine transition: open -> draft should be rejected
        try {
            $sessionService->updateStatus($this->session, 'draft', $this->admin);
            $this->fail('Expected validation exception for invalid transition');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('status', $e->errors());
        }

        // 3. Valid transition: open -> ongoing
        $sessionService->updateStatus($this->session, 'ongoing', $this->admin);
        $this->assertEquals('ongoing', $this->session->fresh()->status);

        // 4. Register member and submit permission
        SessionParticipant::create(['session_id' => $this->session->id, 'user_id' => $this->member->id, 'status' => 'registered']);
        $perm = PermissionRequest::create([
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'reason' => 'Ada lembur kerja',
            'status' => 'pending',
        ]);

        // 5. Cancel session: pending permissions must be auto-rejected with review note
        $sessionService->updateStatus($this->session, 'cancelled', $this->admin);
        $perm->refresh();
        $this->assertEquals('rejected', $perm->status);
        $this->assertEquals('Sesi olahraga dibatalkan oleh admin.', $perm->review_note);

        // 6. closeSession cannot run on cancelled session
        try {
            $attService->closeSession($this->session, $this->admin);
            $this->fail('Expected exception when closing cancelled session');
        } catch (\Exception $e) {
            $this->assertStringContainsString('tidak dapat diselesaikan lagi', $e->getMessage());
        }
    }

    /**
     * Test manual store walk-in confirmation guard and unique attendance handling
     */
    public function test_scanner_and_manual_store_walk_in_guards_and_unique_handling(): void
    {
        // 1. Manual store without allow_walk_in when member is unregistered should fail with error
        $response = $this->actingAs($this->admin)->post(route('admin.sessions.attendance.manual', $this->session), [
            'identifier' => $this->member->member_id,
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseMissing('attendances', [
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
        ]);

        // 2. Manual store WITH allow_walk_in succeeds as walk-in
        $response = $this->actingAs($this->admin)->post(route('admin.sessions.attendance.manual', $this->session), [
            'identifier' => $this->member->member_id,
            'allow_walk_in' => 1,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('attendances', [
            'session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'is_walk_in' => true,
            'status' => 'present',
        ]);

        // 3. QrScannerController confirm returns clean "Member sudah hadir" message when already attended
        $scanResponse = $this->actingAs($this->admin)->postJson(route('admin.sessions.scan.confirm', $this->session), [
            'member_id' => $this->member->id,
            'is_walk_in' => true,
        ]);

        $scanResponse->assertStatus(422);
        $this->assertFalse($scanResponse->json('success'));
        $this->assertEquals('Member sudah hadir sebelumnya.', $scanResponse->json('message'));
    }

    /**
     * Test numeric MAX member ID generation ignoring non-numeric prefixes and retry mechanism
     */
    public function test_generate_next_member_id_and_creation(): void
    {
        // Insert custom user with non-standard member_id (e.g. GST-ADM01, GST-SPECIAL)
        User::create([
            'member_id' => 'GST-ADM01',
            'name' => 'Custom Admin User',
            'email' => 'customadmin@gaston.test',
            'password' => bcrypt('password'),
            'qr_token' => 'GST-MBR-CUSTOMADM',
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Insert a member with numeric id GST-00045
        User::create([
            'member_id' => 'GST-00045',
            'name' => 'Existing High ID User',
            'email' => 'highid@gaston.test',
            'password' => bcrypt('password'),
            'qr_token' => 'GST-MBR-HIGH00045',
            'role' => 'member',
            'status' => 'active',
        ]);

        // Next member ID must be GST-00046, ignoring GST-ADM01
        $nextId = User::generateNextMemberId();
        $this->assertEquals('GST-00046', $nextId);

        // Member creation via MemberService should successfully assign GST-00046
        $memberService = new \App\Services\MemberService();
        $createdMember = $memberService->createMember([
            'name' => 'New Retried Member',
            'email' => 'newretried@gaston.test',
            'status' => 'active',
        ]);

        $this->assertEquals('GST-00046', $createdMember->member_id);
    }
}
