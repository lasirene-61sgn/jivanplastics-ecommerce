<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'model_type',
        'model_id',
        'ip_address',
        'details'
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            $request = request();
            $userAgent = $request->userAgent();
            $deviceType = 'Unknown';
            $browser = 'Unknown';

            if ($userAgent) {
                // Determine Device Type
                if (preg_match('/mobile/i', $userAgent)) {
                    $deviceType = 'Mobile';
                } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
                    $deviceType = 'Tablet';
                } else {
                    $deviceType = 'PC';
                }

                // Determine Browser
                if (preg_match('/MSIE/i', $userAgent) || preg_match('/Trident/i', $userAgent)) {
                    $browser = 'Internet Explorer';
                } elseif (preg_match('/Edge/i', $userAgent)) {
                    $browser = 'Edge';
                } elseif (preg_match('/Chrome/i', $userAgent)) {
                    $browser = 'Chrome';
                } elseif (preg_match('/Safari/i', $userAgent)) {
                    $browser = 'Safari';
                } elseif (preg_match('/Firefox/i', $userAgent)) {
                    $browser = 'Firefox';
                } elseif (preg_match('/Opera/i', $userAgent)) {
                    $browser = 'Opera';
                }
            }

            $details = $log->details ?? [];
            
            // Get real IP
            $ip = $request->header('CF-Connecting-IP') 
                ?? $request->header('X-Forwarded-For') 
                ?? $request->header('X-Real-IP')
                ?? $request->ip();

            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            
            // Override IP in log
            $log->ip_address = $ip;

            // Attempt to get PC Name (Hostname)
            $hostname = @gethostbyaddr($ip);
            if ($hostname && $hostname !== $ip) {
                $details['pc_name'] = $hostname;
            }

            $details['device'] = $deviceType;
            $details['browser'] = $browser;
            $log->details = $details;
        });
    }
}
