<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var User
     */
    protected User $user;

    /**
     * @return void
     */
    function setUp(): void
    {
        parent::setUp();
        $this->artisan('cache:clear');
        // default password is 'password' on factory user
        $this->user = User::factory()->create([
            'avatar_url' => null,
        ]);
        Sanctum::actingAs($this->user);
    }

    public function test_can_update_profile(): void
    {
        $userData = [
            'name' => $this->faker->name(),
        ];
        $this->putJson(route('updateProfile'), $userData)
            ->assertOk()
            ->assertJsonStructure(['message','data']);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $userData['name'],
        ]);
    }

    public function test_can_update_password(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $newPassword = $this->faker->password(8);

        $this->putJson(route('updatePassword'), [
            'old_password' => 'password',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
            ->assertOk()
            ->assertJsonStructure(['message','data']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'updated_at' => now(),
        ]);

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    public function test_can_update_avatar(): void
    {
        Storage::fake('public');
 
        $this->postJson(route('updateAvatar'), [
            'avatar' => $file = UploadedFile::fake()->image('random.jpg'),
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'avatar_url' => User::FDIMAGE.'/' . $file->hashName(),
        ]);

        Storage::disk('public')->assertExists(User::FDIMAGE.'/' . $file->hashName());
    }
    
    public function test_can_twice_update_avatar(): void
    {
        $this->artisan('cache:clear');

        Storage::fake('public');

        $this->postJson(route('updateAvatar'), [
            'avatar' => $file1 = UploadedFile::fake()->image('random.jpg'),
        ])->assertOk();

        $this->postJson(route('updateAvatar'), [
            'avatar' => $file2 = UploadedFile::fake()->image('random.jpg'),
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'avatar_url' => User::FDIMAGE.'/' . $file2->hashName(),
        ]);

        Storage::disk('public')->assertExists(User::FDIMAGE.'/' . $file2->hashName());
        Storage::disk('public')->assertMissing(User::FDIMAGE.'/' . $file1->hashName());
    }

    public function test_can_delete_avatar(): void
    {
        Storage::fake('public');

        $this->postJson(route('updateAvatar'), [
            'avatar' => $file = UploadedFile::fake()->image('random.jpg'),
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'avatar_url' => User::FDIMAGE.'/' . $file->hashName(),
        ]);

        Storage::disk('public')->assertExists(User::FDIMAGE.'/' . $file->hashName());

        $this->deleteJson(route('deleteAvatar'))
            ->assertOk()
            ->assertJsonStructure(['message','data']);

        $this->assertDatabaseHas('users', [
            'avatar_url' => null,
        ]);

        Storage::disk('public')->assertMissing(User::FDIMAGE.'/' . $file->hashName());
    }

    public function test_nothing_happen_update_profile(): void
    {
        $this->putJson(route('updateProfile'), [
            'email'=> $this->faker->email(),
        ])->assertOk();

        // nothing changed, cuz only name, country, phone, desc on update profile request
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email' => $this->user->email,
        ]);
    }

    public function test_cannot_update_password(): void
    {
        // testing just max 5 character to trigger validation
        $this->putJson(route('updatePassword'), [
            'old_password' => 'password',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ])->assertStatus(Response::HTTP_BAD_REQUEST);

        // testing wrong password_confirmation to trigger validation
        $this->putJson(route('updatePassword'), [
            'old_password' => 'password',
            'password' => $this->faker->password(8),
            'password_confirmation' => $this->faker->password(8),
        ])->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'password' => $this->user->password,
        ]);
    }

    public function test_cannot_update_avatar(): void
    {
        $this->artisan('cache:clear');

        // testing wrong file type to trigger validation (another than jpg, jpeg, png)
        $this->postJson(route('updateAvatar'), [
            'avatar' => $file = UploadedFile::fake()->create('random.pdf'),
        ])->assertStatus(Response::HTTP_BAD_REQUEST);

        // testing max size to trigger validation (max request is 2MB)
        $this->postJson(route('updateAvatar'), [
            'avatar' => $file = UploadedFile::fake()->image('random.jpg')->size(6000),
        ])->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'avatar_url' => null,
        ]);
    }

    public function test_cannot_update_password_wrong_old_password(): void
    {
        $newPass = $this->faker->password(8);
        $this->putJson(route('updatePassword'), [
            'old_password' => $this->faker->password(8),
            'password' => $newPass,
            'password_confirmation' => $newPass,
        ])->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'password' => $this->user->password,
        ]);
    }

    public function test_cannot_delete_avatar(): void
    {
        $user = User::factory()->create([
            'avatar_url' => null,
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson(route('deleteAvatar'))
            ->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar_url' => null,
        ]);
    }

    public function test_cannot_update_avatar_too_many_request(): void
    {
        $this->artisan('cache:clear');

        Storage::fake('public');

        for ($i=0; $i < 5; $i++) { 
            $this->postJson(route('updateAvatar'), [
                'avatar' => $file = UploadedFile::fake()->image('random.jpg'),
            ])->assertOk();
        }

        $this->postJson(route('updateAvatar'), [
            'avatar' => UploadedFile::fake()->image('random.jpg'),
        ])->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'avatar_url' => User::FDIMAGE.'/' . $file->hashName(),
        ]);

        Storage::disk('public')->assertExists(User::FDIMAGE.'/' . $file->hashName());
    }
}
