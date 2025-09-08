<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_success_returns_token_and_user(): void
    {
        $payload = [
            'name' => 'Jane Tester',
            'email' => 'jane@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $res = $this->postJson('/api/register', $payload);
        $res->assertCreated()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']])
            ->assertJsonPath('user.email', 'jane@example.com');

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function test_register_fails_with_invalid_email(): void
    {
        $payload = [
            'name' => 'Bad Email',
            'email' => 'not-an-email',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $res = $this->postJson('/api/register', $payload);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_with_short_password(): void
    {
        $payload = [
            'name' => 'Short Pwd',
            'email' => 'short@example.com',
            'password' => 'short',                 // 5 char
            'password_confirmation' => 'short',
        ];

        $res = $this->postJson('/api/register', $payload);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_success_returns_token_and_user(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $res = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'secret123',
        ]);

        $res->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']])
            ->assertJsonPath('user.id', $user->id);
    }

    public function test_login_fails_with_wrong_credentials(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $res = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-pass',
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
