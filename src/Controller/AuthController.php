<?php
namespace Controller;

use \Slim\Http\Request;
use \Slim\Http\Response;
use \Slim\Router;
use Auth\AuthService;

/**
 * Class AuthController
 * @package Controller
 */
class AuthController
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var Router
     */
    private $router;

    /**
     * AuthController constructor.
     * @param array $config
     * @param AuthService $authService
     * @param Router $router
     */
    public function __construct(array $config, AuthService $authService, Router $router)
    {
        $this->config = $config;
        $this->authService = $authService;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function authAction(Request $request, Response $response, array $args)
    {
        /** @var array $queryParams */
        $queryParams = $request->getQueryParams();
        try {
            $this->authService->authorize($queryParams);
        } catch (\Exception $e) {
            $this->authService->logout();
        }

        // TODO: redirect to issue list
        return $response->withRedirect($this->router->pathFor('index'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function logoutAction(Request $request, Response $response, array $args)
    {
        $this->authService->logout();

        // TODO: redirect to login
        return $response->withRedirect($this->router->pathFor('index'));
    }

}