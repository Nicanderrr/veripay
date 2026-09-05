<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($user)
            ->post(route('password.change.update'), [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_forgot_password_sends_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertSessionHas('success');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_admin_can_reset_customer_password(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.customers.password', $customer), [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password', $customer->fresh()->password));
    }
}
