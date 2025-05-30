<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_COMMERCIAL_ROUTIER = 'commercial_routier';
    const ROLE_EXPLOITATION_ROUTIER = 'exploitation_routier';
    const ROLE_COMMERCIAL_MARITIME = 'commercial_maritime';
    const ROLE_EXPLOITATION_MARITIME = 'exploitation_maritime';
    const ROLE_COMMERCIAL_AERIEN = 'commercial_aerien';
    const ROLE_EXPLOITATION_AERIEN = 'exploitation_aerien';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    public function assignedComplaints()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN => 'Administrateur',
            self::ROLE_COMMERCIAL_ROUTIER => 'Commercial Routier',
            self::ROLE_EXPLOITATION_ROUTIER => 'Exploitation Routier',
            self::ROLE_COMMERCIAL_MARITIME => 'Commercial Maritime',
            self::ROLE_EXPLOITATION_MARITIME => 'Exploitation Maritime',
            self::ROLE_COMMERCIAL_AERIEN => 'Commercial Aérien',
            self::ROLE_EXPLOITATION_AERIEN => 'Exploitation Aérien',
        ];
    }

    public function canViewComplaint(Complaint $complaint)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $complaint->assigned_to === $this->id;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    /**
     * Get messages sent by this user
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the number of unread messages
     */
    public function unreadMessagesCount()
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }
}
