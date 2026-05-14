<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 * Represents a student account in the system.
 * Relationship: A user has many tasks (one-to-many).
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * These are the fields allowed in User::create([...]).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Prevents password and token from being exposed in JSON output.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     * Automatically converts password to hashed value.
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────

    /**
     * A user (student) has many tasks.
     * Eloquent one-to-many: tasks.user_id references users.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
