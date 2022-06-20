<?php

namespace App\Http\Middleware\Admin;

use App\Models\Admin\Admin;
use App\Models\Admin\Log;
use Illuminate\Http\Request;

class LogOperation
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($this->shouldLogOperation($request) || Admin::user()->isAdministrator()) {
            $log = [
                'user_id' => Admin::user()->id,
                'path' => substr($request->path(), 0, 255),
                'method' => $request->method(),
                'ip' => $request->getClientIp(),
                'user_agent' => $request->header('User-Agent'),
                'input' => json_encode($request->input()),
            ];

            try {
                Log::create($log);
            } catch (\Exception $exception) {
                // pass
            }
        }

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function shouldLogOperation(Request $request)
    {
        $storeId = Admin::user()->stores()->first()->id ?? 0;
        return lc_config_global('ADMIN_LOG',$storeId)
        && !$this->inExceptArray($request,$storeId)
        && Admin::user();
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function inExceptArray($request,$storeId)
    {
        foreach (explode(',', lc_config_global('ADMIN_LOG_EXP',$storeId)) as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }
            if ($request->path() == $except) {
                return true;
            }
        }

        return false;
    }
}
