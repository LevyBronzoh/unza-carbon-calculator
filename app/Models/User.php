<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * User Model for UNZA Carbon Calculator
 *
 * This class handles user authentication and management for the carbon calculator
 * Extends Laravel's Authenticatable class (Polymorphism) - inherits authentication methods
 *
 * @author Levy Bronzoh, Climate Yanga
 * @version 1.0
 * @since 2025-07-12
 */
class User extends Authenticatable
{
    // Uses Laravel traits for additional functionality (Trait composition)
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Protects against mass assignment vulnerabilities
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',           // User's full name
        'email',          // User's email address (unique identifier)
        'phone',          // User's phone number (alternative contact)
        'password',       // Hashed password for authentication
        'user_type',      // Type of user (student, staff, faculty)
        'location',       // User's location within UNZA

    ];

    /**
     * The attributes that should be hidden for serialization.
     * Prevents sensitive data from being exposed in JSON responses
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',       // Never expose password in API responses
        'remember_token', // Laravel's remember token for "remember me" functionality
    ];

    /**
     * The attributes that should be cast.
     * Automatically converts database values to appropriate PHP types
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',  // Cast to Carbon datetime object
        'password' => 'hashed',             // Automatically hash passwords (Laravel 10+)
    ];

    /**
     * Define relationship with BaselineData model
     * One user can have multiple baseline data entries (One-to-Many relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
     public function baselineData(): HasMany
    {
        return $this->hasMany(BaselineData::class);
    }

    /**
     * Define relationship with ProjectData model
     * One user can have multiple project data entries (One-to-Many relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
     public function projectData(): HasMany
    {
        return $this->hasMany(ProjectData::class);
    }

    /**
     * Define relationship with WeeklyUpdate model
     * One user can have multiple weekly update entries (One-to-Many relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function weeklyUpdates(): HasMany
    {
        return $this->hasMany(WeeklyUpdate::class);
    }

    /**
     * Check if user is a student
     * Helper method for role-based access control
     *
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->user_type === 'student';
    }

    /**
     * Check if user is staff
     * Helper method for role-based access control
     *
     * @return bool
     */
    public function isStaff(): bool
    {
        return $this->user_type === 'staff';
    }

    /**
     * Check if user is faculty
     * Helper method for role-based access control
     *
     * @return bool
     */
    public function isFaculty(): bool
    {
        return $this->user_type === 'faculty';
    }

    /**
     * Get user's full display name with affiliation
     * Formats the user's name with their role for display purposes
     *
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ' (' . ucfirst($this->user_type) . ')';
    }
}
