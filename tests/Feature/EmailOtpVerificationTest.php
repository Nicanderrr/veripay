<?php

namespace Tests\Feature;

use App\Mail\EmailVerificationOtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailOtpVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_email_otp_verification(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'New Customer',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('verification.otp.notice'));
        $this->assertGuest();

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);

        Mail::assertSent(EmailVerificationOtpMail::class, fn ($mail) => $mail->user->is($user));
    }

    public function test_user_can_verify_email_with_valid_otp(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => User::ROLE_CUSTOMER,
        ]);

        DB::table('email_verification_otps')->insert([
            'user_id' => $user->id,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->withSession(['pending_verification_user_id' => $user->id])
            ->post(route('verification.otp.verify'), [
                'code' => '123456',
            ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
