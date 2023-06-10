<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_register(): void
    {
        Mail::fake();

        $password = $this->faker->password(8);
        $userData = [
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'country' => $this->faker->country(),
            'email' => $this->faker->unique()->email(),
            'password' => $password,
            'password_confirmation' => $password,
        ];
        $res = $this->postJson(route('register'), $userData)
        ->assertCreated()
        ->assertJsonStructure(['message','data']);

        Mail::assertNothingSent();
        $this->assertNotEmpty($res['data']);

        $user = User::where('email', $res['data']['email'])->first();

        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['username'], $user->username);
        $this->assertFalse($user->hasVerifiedEmail());
    }
    
    public function test_cannot_register_if_some_field_or_all_empty(): void
    {
        $this->postJson(route('register'), [
            'name' => '',
            'username' => '',
            'country' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ])->assertBadRequest();
    }

    public function test_cannot_register_if_email_invalid_format(): void
    {
        $password = $this->faker->password(8);
        $this->postJson(route('register'), [
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'country' => $this->faker->country(),
            'email' => 'johndoe',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertBadRequest();
    }

    public function test_cannot_register_if_password_not_match(): void
    {
        $password = $this->faker->password(8);
        $this->postJson(route('register'), [
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'country' => $this->faker->country(),
            'email' => $this->faker->unique()->email(),
            'password' => $password,
            'password_confirmation' => 'password1',
        ])
        ->assertBadRequest();
    }

    public function test_can_login(): void
    {
        $user = User::factory()->create();

        $res = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password', //default password on factory UserFactory.php
        ]);

        $res->assertOk();
        $res->assertJsonStructure(['message','data', 'success']);
        $this->assertNotEmpty($res['data']);
    }

    public function test_cannot_login_if_some_field_empty(): void
    {
        $this->postJson(route('login'), [
            'email' => '',
            'password' => '',
        ])->assertBadRequest();
    }

    public function test_cannot_login_if_email_or_password_not_match(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password1',
        ])->assertUnauthorized();
    }
    
    public function test_cannot_create_new_token_if_already_login_with_same_agent(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk();
    }

    public function test_cannot_login_if_request_more_than_10_times_in_1_minute(): void
    {
        $user = User::factory()->create();

        for ($i=0; $i < 10; $i++) {
            $this->postJson(route('login'), [
                'email' => $user->email,
                'password' => 'password1',
            ])->assertUnauthorized();
        }

        $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password1',
        ])->assertTooManyRequests();
    }

    public function test_can_logout(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(route('logout'))->assertOk();
    }

    public function test_cannot_logout_if_not_authenticated(): void
    {
        $this->postJson(route('logout'))->assertUnauthorized();
    }

    public function test_can_send_or_accepted_verify_email_link(): void
    {
        $user = User::factory()->create();
        $user->email_verified_at = null;
        $user->save();
        $notification = new CustomVerifyEmail();

        $uri = $notification->toMail($user)->actionUrl;
        Sanctum::actingAs($user);
        $this->actingAs($user)->get($uri)->assertOk();

        $user = User::find($user->id);
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_cannot_send_verify_email_link_if_email_already_verified(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson(route('verification.resend'))
        ->assertOk()
        ->assertJsonStructure(['message','data'])
        ->assertJson(['message' => 'Email already verified.']);
    }

    public function test_cannot_accept_verify_email_link_if_invalid_signature(): void
    {
        $user = User::factory()->create();
        $user->email_verified_at = null;
        $user->save();
        $notification = new CustomVerifyEmail();

        $uri = $notification->toMail($user)->actionUrl;
        $uri = str_replace('signature=', 'signature=invalid', $uri);
        Sanctum::actingAs($user);
        $this->actingAs($user)->get($uri)->assertForbidden();
    }

    public function test_can_send_reset_password_link(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $this->postJson(route('custom.resetpassword.email'), [
            'email' => $user->email,
        ])->assertOk();
    }

    public function test_cannot_send_reset_password_link_if_email_not_found(): void
    {
        $this->postJson(route('custom.resetpassword.email'), [
            'email' => $this->faker->email,
        ])->assertBadRequest();
    }

    public function test_can_accept_reset_password_link(): void
    {
        $user = User::factory()->create();
        $this->assertEmpty($user->passwordResetTokens()->get()->toArray());
        $token = Password::createToken($user);
        $this->assertNotEmpty($user->passwordResetTokens()->get()->toArray());
        Mail::fake();
        $this->postJson(route('custom.resetpassword.reset'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password2',
            'password_confirmation' => 'password2',
        ])->assertOk();

        $user = User::find($user->id);
        $this->assertTrue(Hash::check('password2', $user->password));
        $this->assertEmpty($user->passwordResetTokens()->get()->toArray());
    }

    public function test_cannot_accept_reset_password_link_if_token_invalid_or_expired_or_user_not_found(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        $user->delete();
        $this->postJson(route('custom.resetpassword.reset'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password2',
            'password_confirmation' => 'password2',
        ])->assertServerError();
    }
    
    public function test_can_get_user_profile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $res = $this->getJson(route('user.me'))
        ->assertOk()
        ->assertJsonStructure(['message','data', 'success']);

        $this->assertNotEmpty($res['data']);
    }

    public function test_cannot_get_user_profile_if_not_authenticated(): void
    {
        $this->getJson(route('user.me'))->assertUnauthorized();
    }
}
