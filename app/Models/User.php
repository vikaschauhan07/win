<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'password',
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

    public static function newLogin($email,$ip_address,$status){
        $userLogin = new UserLogin();
        $userLogin->email = $email;
        $userLogin->ip_address = $ip_address;
        $userLogin->status = $status;
        $userLogin->save();
    }

    public static function guidv4($data = null){
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function createConfirmationToken($userid, $token)
    {
        $confirmationCode = new ConfirmationCodes();
        $confirmationCode->userid = $userid;
        $confirmationCode->token = $token;
        $confirmationCode->save();
    }

    public static function generateConfirmationToken($length = 32)
    {
        $bytes = random_bytes($length);
        return bin2hex($bytes);
    }
    public static function setResetCodeUsed($code, $userid){
        $resetToekn = new ResetToekns();
        $resetToekn->userid = $userid;
        $resetToekn->code = $code;
        $resetToekn->save();
    }

}
