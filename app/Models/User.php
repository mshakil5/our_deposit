<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'surname',
        'is_type',
        'password',
        'street_name',
        'house_number',
        'town',
        'country',
        'postcode',
        'photo',
        'phone',
        'status',
        'about',
        'facebook',
        'twitter',
        'google',
        'linkedin',
        'google_id',
        'facebook_id',
        'updated_by',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected function type(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ["0", "1", "2"][$value],
        );
    }

    
    public function transactions()
    {
        return $this->hasMany(Transaction::class)->where('status', 1);
    }

    public function sendPasswordResetNotification($token)
    {
        // Optional: If you want to send a custom API email template, 
        // you can create a custom Notification class here. 
        // But to keep it simple and just show the token in default email:
        
        $notification = new ResetPasswordNotification($token);
        $notification->createUrlUsing(function ($token, $user) {
            // This changes the link in the email to just show the token
            // Or you can point it to your frontend URL:
            // return env('FRONTEND_URL') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
            
            return "Your password reset token is: " . $token;
        });

        $this->notify($notification);
    }

}
