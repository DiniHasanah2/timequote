<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\InternalSummary;

class EnsureVersionUnlocked
{
    public function handle($request, Closure $next)
    {
        $versionParam = $request->route('version'); 
        $versionId = is_object($versionParam) ? $versionParam->getKey() : $versionParam;

        $locked = InternalSummary::where('version_id', $versionId)
            ->where('is_logged', true)
            ->exists();

        if ($locked) {
            return redirect()
                ->route('versions.internal_summary.show', $versionId)
                ->with('error', 'This version is committed and read-only.');
        }

        return $next($request);
    }
}
