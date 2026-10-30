<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'customer_id',
        'user_type', // 'admin', 'b2b', 'b2c'
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeForUserType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('user_type', $type);
        }
        return $query;
    }

    public function scopeForAction($query, $action)
    {
        if ($action && $action !== 'all') {
            return $query->where('action', $action);
        }
        return $query;
    }

    public function scopeForDevice($query, $device)
    {
        if ($device && $device !== 'all') {
            return $query->where('details->device', $device);
        }
        return $query;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            $request = request();
            $userAgent = $request ? $request->userAgent() : null;
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
            if (empty($log->ip_address) && $request) {
                $ip = $request->header('CF-Connecting-IP') 
                    ?? $request->header('X-Forwarded-For') 
                    ?? $request->header('X-Real-IP')
                    ?? $request->ip();

                if ($ip && strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip ?? '');
                if ($ip) {
                    $log->ip_address = $ip;
                }
            }

            // Attempt to get PC Name (Hostname)
            if (!isset($details['pc_name']) && !empty($log->ip_address)) {
                $hostname = @gethostbyaddr($log->ip_address);
                if ($hostname && $hostname !== $log->ip_address) {
                    $details['pc_name'] = $hostname;
                }
            }

            if (!isset($details['device']) && $deviceType !== 'Unknown') {
                $details['device'] = $deviceType;
            }
            if (!isset($details['browser']) && $browser !== 'Unknown') {
                $details['browser'] = $browser;
            }

            // Auto-assign user_type if missing
            if (empty($log->user_type)) {
                if (!empty($log->customer_id)) {
                    $customer = Customer::find($log->customer_id);
                    $log->user_type = ($customer && $customer->customer_type === 'dealer') ? 'b2b' : 'b2c';
                } elseif (!empty($log->admin_id)) {
                    $log->user_type = 'admin';
                } else {
                    $log->user_type = 'admin';
                }
            }

            $log->details = $details;
        });
    }
}
