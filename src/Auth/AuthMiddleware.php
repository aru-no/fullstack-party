<?php
namespace Auth;

use \Slim\Http\Request;
use \Slim\Http\Response;

/**
 * Class AuthMiddleware
 * @package Middleware
 */
class AuthMiddleware
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function checkToken(Request $request, Response $response, callable $next) {
        if (empty(AuthService::getToken())) {
            return $response->withStatus(403);
        }

        return $next($request, $response);
    }
}