<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'prefixname',
        'middlename',
        'lastname',
        'suffixname',
        'username',
        'photo',
        'type',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(Detail::class);
    }

    /**
     * Retrieve the default photo from stoage.
     * Supply a base64 png image if the 'photo' attribute is null.
     * @return string
     */
    public function getAvatarAttribute()
    {
        if(!isset($this->photo)) {
            $accountImage = Storage::get('public/users/account.jpg');
        } else {
            $accountImage = Storage::get('public/users/' . $this->photo);
        }

        $base64Photo = 'data:image/png;base64,' . base64_encode($accountImage);

        return $base64Photo;
    }

    public function getImageUrl(): string{
        if (!isset($this->photo)) {
            $accountImage = Storage::url('public/users/account.jpg');
        } else {
            $accountImage = Storage::url('public/users/' . $this->photo);
        }

        $appUrl = env('APP_URL');
        $port = env('APP_PORT');

        return $appUrl . ':' . $port . $accountImage;
    }
    /**
     * Retrieve the user's full name in the format:
     *  [firstname][ mi?][ lastname]
     * Where:
     *  [ mi?] is the optional middle initial.
     *
     * @return string
     */
    public function getFullnameAttribute(): string
    {
        $firstname = $this->name;
        $lastname = $this->lastname;
        $middleInitial = $this->middlename;

        return $firstname . ' ' . $middleInitial . ' ' . $lastname;
    }

    public function getFirstLetterOfMiddleIntital(): string
    {
        $middleInitial = $this->middlename ? strtoupper(substr($this->middlename, 0, 1)) . '.' : '';
        return $middleInitial;
    }

    public function getGenderFromPrefix()
    {
        if (in_array($this->prefixname, ['mr'])) {
            return 'Male';
        } elseif (in_array($this->prefixname, ['ms', 'mrs'])) {
            return 'Female';
        }

        // Just in case it bypassed the UserRequest validation
        return 'Unknown';
    }
}
