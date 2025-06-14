<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class License extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'license_key',
        'user_id',
        'domain',
        'ip_address',
        'status',
        'activated_at',
        'expires_at',
        'max_domains',
        'used_domains',
        'features',
        'license_type',
        'is_trial',
        'notes',
    ];

    protected $casts = [
        'features' => 'array',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_trial' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canActivateDomain()
    {
        return $this->used_domains < $this->max_domains;
    }

    public function activateDomain($domain, $ipAddress)
    {
        if (!$this->canActivateDomain()) {
            return false;
        }

        $this->domain = $domain;
        $this->ip_address = $ipAddress;
        $this->used_domains++;
        $this->activated_at = now();
        $this->save();

        return true;
    }

    public static function generateLicenseKey()
    {
        do {
            $key = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (self::where('license_key', $key)->exists());

        return $key;
    }

    public function getRemainingDays()
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }
} 