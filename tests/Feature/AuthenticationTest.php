<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test users with different roles
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        User::factory()->create([
            'name' => 'Admin Sekolah',
            'email' => 'adminsekolah@example.test',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN_SEKOLAH,
            'active' => true,
        ]);

        // Create guru user
        User::factory()->create([
            'name' => 'Guru Demo',
            'email' => 'guru@example.test',
            'password' => bcrypt('password'),
            'role' => UserRole::GURU,
            'active' => true,
        ]);

        // Create siswa user
        User::factory()->create([
            'name' => 'Siswa Demo',
            'email' => 'siswa@example.test',
            'password' => bcrypt('password'),
            'role' => UserRole::SISWA,
            'active' => true,
        ]);

        // Create inactive user
        User::factory()->create([
            'name' => 'Inactive User',
            'email' => 'inactive@example.test',
            'password' => bcrypt('password'),
            'role' => UserRole::SISWA,
            'active' => false,
        ]);
    }

    /**
     * ✅ TEST 1: Admin can login and redirects to dashboard
     */
    public function test_admin_login_redirects_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'adminsekolah@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs(User::where('email', 'adminsekolah@example.test')->first());
    }

    /**
     * ✅ TEST 2: Guru can login and redirects to attendance scan
     */
    public function test_guru_login_redirects_to_scan(): void
    {
        $response = $this->post('/login', [
            'email' => 'guru@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/scan');
        $this->assertAuthenticatedAs(User::where('email', 'guru@example.test')->first());
    }

    /**
     * ✅ TEST 3: Siswa can login and redirects to attendance scan
     */
    public function test_siswa_login_redirects_to_scan(): void
    {
        $response = $this->post('/login', [
            'email' => 'siswa@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/scan');
        $this->assertAuthenticatedAs(User::where('email', 'siswa@example.test')->first());
    }

    /**
     * ✅ TEST 4: Inactive user cannot login
     */
    public function test_inactive_user_cannot_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'inactive@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * ✅ TEST 5: Invalid credentials rejected
     */
    public function test_invalid_credentials_rejected(): void
    {
        $response = $this->post('/login', [
            'email' => 'adminsekolah@example.test',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * ✅ TEST 6: Siswa cannot access admin dashboard
     */
    public function test_siswa_cannot_access_admin_dashboard(): void
    {
        $siswa = User::where('email', 'siswa@example.test')->first();
        
        $response = $this->actingAs($siswa)->get('/dashboard');

        $response->assertStatus(403);
    }

    /**
     * ✅ TEST 7: Guru cannot access admin dashboard
     */
    public function test_guru_cannot_access_admin_dashboard(): void
    {
        $guru = User::where('email', 'guru@example.test')->first();
        
        $response = $this->actingAs($guru)->get('/dashboard');

        $response->assertStatus(403);
    }

    /**
     * ✅ TEST 8: Admin can access dashboard
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::where('email', 'adminsekolah@example.test')->first();
        
        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /**
     * ✅ TEST 9: Guru can access scan page
     */
    public function test_guru_can_access_scan_page(): void
    {
        $guru = User::where('email', 'guru@example.test')->first();
        
        $response = $this->actingAs($guru)->get('/scan');

        $response->assertStatus(200);
        $response->assertViewIs('attendance.scan');
    }

    /**
     * ✅ TEST 10: Siswa can access scan page
     */
    public function test_siswa_can_access_scan_page(): void
    {
        $siswa = User::where('email', 'siswa@example.test')->first();
        
        $response = $this->actingAs($siswa)->get('/scan');

        $response->assertStatus(200);
        $response->assertViewIs('attendance.scan');
    }

    /**
     * ✅ TEST 11: Unauthenticated user cannot access protected routes
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * ✅ TEST 12: Session is regenerated after login
     */
    public function test_session_regenerated_after_login(): void
    {
        $sessionBefore = session()->getId();

        $this->post('/login', [
            'email' => 'adminsekolah@example.test',
            'password' => 'password',
        ]);

        $sessionAfter = session()->getId();

        $this->assertNotEquals($sessionBefore, $sessionAfter);
    }

    /**
     * ✅ TEST 13: User can logout
     */
    public function test_user_can_logout(): void
    {
        $admin = User::where('email', 'adminsekolah@example.test')->first();
        
        $this->actingAs($admin);
        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * ✅ TEST 14: Login requires email
     */
    public function test_login_requires_email(): void
    {
        $response = $this->post('/login', [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * ✅ TEST 15: Login requires password
     */
    public function test_login_requires_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'adminsekolah@example.test',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * ✅ TEST 16: Email must be valid format
     */
    public function test_login_requires_valid_email_format(): void
    {
        $response = $this->post('/login', [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * ✅ TEST 17: Login page accessible to guests
     */
    public function test_login_page_accessible_to_guests(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * ✅ TEST 18: Authenticated user redirected from login page
     */
    public function test_authenticated_user_redirected_from_login(): void
    {
        $admin = User::where('email', 'adminsekolah@example.test')->first();
        
        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect('/dashboard');
    }
}
