<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Users;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'hostel_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // public function hostel()
    // {
    //     return $this->hasOne(Hostel::class);
    // }

     public function managedHostel()
    {
        return $this->hasOne(Hostel::class, 'manager_id');
    }

    /**
     * If a manager can manage multiple hostels, use hasMany instead
     */
    public function managedHostels()
    {
        return $this->hasMany(Hostel::class, 'manager_id');
    }

    /**
     * Check if user has been assigned to a hostel
     */
    public function hasAssignedHostel()
    {
        return $this->managedHostel()->exists();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isHostelManager()
    {
        return $this->role === 'hostel_manager';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    protected function attributes(): array
    {
        return [
            'is_active' => true,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
