<?php

namespace App\Services;

use App\Models\ServiceAuditLog;
use Illuminate\Support\Facades\Auth;

class ServiceAuditService
{
    /**
     * Log changes to service fields
     *
     * @param string $serviceId
     * @param array $changes
     * @return void
     */
    public static function logChanges($serviceId, array $changes)
    {
        $user = Auth::user();
        
        foreach ($changes as $field => $values) {
            ServiceAuditLog::create([
                'service_id' => $serviceId,
                'field_name' => $field,
                'old_value' => $values['old'],
                'new_value' => $values['new'],
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'update',
            ]);
        }
    }

    /**
     * Get audit logs for a specific service
     *
     * @param string $serviceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getServiceLogs($serviceId)
    {
        return ServiceAuditLog::where('service_id', $serviceId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if values are different and return change array
     *
     * @param mixed $oldValue
     * @param mixed $newValue
     * @return array|null
     */
    public static function getChange($oldValue, $newValue)
    {
        if ($oldValue != $newValue) {
            return [
                'old' => $oldValue,
                'new' => $newValue
            ];
        }
        return null;
    }
}
