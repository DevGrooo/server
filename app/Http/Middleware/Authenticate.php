<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Response;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            throw new ApiException('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }
        $current_action = $this->_getCurrentAction($request);
        if ($current_action) {
            // check role api of user
            if (!$this->_checkRoleApi($request->user(), $current_action)) {
                throw new ApiException('Access denied', Response::HTTP_FORBIDDEN);
            }            
        } else {
            throw new ApiException('Request not found', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $next($request);
    }

    /**
     * @param \App\Models\User $user
     * @param string $current_action
     * @return boolean
     */
    private function _checkRoleApi(\App\Models\User $user, $current_action)
    {
        return true;
        // $api_codes = $user->getApiCodes();
        // if (array_key_exists($current_action, $api_codes)) {
        //     return true;
        // }
        // return false;
    }

    /**
     * Get current action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function _getCurrentAction($request)
    {
        $current_route = $request->route();
        if (isset($current_route[1]['uses']) && preg_match('/[A-Za-z0-9]+@[A-Za-z0-9_]+$/', $current_route[1]['uses'], $match)) {
            return $match[0];
        }
        return null;
    }
}
