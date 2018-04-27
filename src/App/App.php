<?php
namespace App;

use \Slim\Http\Request;
use \Slim\Http\Response;
use \Controller\AuthController;
use \Controller\IssueController;
use \Controller\IndexController;
use \Auth\AuthMiddleware;

class App
{
    const VERSION = 'v1';

    /**
     * @var \Slim\App
     */
    private $app;

    public function __construct($config)
    {
        $container = new \Slim\Container();
        $container['config'] = $config;
        $container['notFoundHandler'] = function ($c) {
            return function (Request $request, Response $response) use ($c) {

                $isAPI = (bool)preg_match('|^/api/v.*$|', $request->getServerParams()['REQUEST_URI']);

                if (true === $isAPI) {
                    return $response
                        ->withStatus(404)
                        ->withJson([
                            'code' => 404,
                            'message' => 'Not found'
                        ]);
                } else {
                    return $response
                        ->withStatus(404)
                        ->write('Page not found');
                }
            };
        };

        $container = DependencyInjector::inject($container);

        $app = new \Slim\App($container);

        // GET authorize via GitHub
        $app->get('/auth', AuthController::class . ':authAction')->setName('auth');

        // GET logout
        $app->get('/logout', AuthController::class . ':logoutAction')->setName('logout');

        // API group
        $app->group('/api', function () use ($app) {

            // Version group
            $app->group('/' . self::VERSION, function () use ($app) {

                // GET issue
                $app->get(
                    '/issues/repos/{user}/{repo}/number/{number}',
                    IssueController::class . ':issueAction'
                )->setName('issue');

                // GET issues collection
                $app->get(
                    '/issues[/]',
                    IssueController::class . ':issueListAction'
                )->setName('issues');

            });
        })->add(AuthMiddleware::class . ':checkToken');

        $app->get('/', IndexController::class . ':indexAction')->setName('index');

        $this->app = $app;
    }

    /**
     * @return \Slim\App
     */
    public function getApp()
    {
        return $this->app;
    }
}