<?php

namespace App\Models;

use App\Notifications\CustomReceiveResetPassword;
use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar_url',
        'country',
        'phone',
        'desc'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
        'password' => 'hashed',
        'isSeller' => 'boolean'
    ];

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     * @override
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPassword($token, $this->email));
    }

    /**
     * Send the password reset notification.
     *
     * @return void
     */
    public function sendReceiveResetPasswordNotification(): void
    {
        $this->notify(new CustomReceiveResetPassword($this->email));
    }

    public function passwordResetTokens()
    {
        return $this->hasMany(PasswordResetToken::class, 'email', 'email');
    }
}
