<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Slim\Http\Request;
use \Slim\Http\Response;


class AppTest extends TestCase
{
    /**
     * @var \Slim\App
     */
    protected $app;

    public function setUp()
    {
        $config = [];
        $configFile = dirname(__FILE__) . '/../../config/config.php';
        if (is_readable($configFile)) {
            require($configFile);
        } else {
            exit('Config not found');
        }

        $this->app = (new \App\App($config))->getApp();
    }

    public function testCreated()
    {
        $this->assertInstanceOf(\Slim\App::class, $this->app);
    }

    public function testNotFoundJson()
    {
        $env = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/v1/nonexisting',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
            'CONTENT_TYPE' => 'application/json;charset=utf-8',
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        $response = $this->app->run(true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/json;charset=utf-8', $response->getHeaderLine('Content-type'));

    }

    public function testNotFoundHtml()
    {
        $env = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/nonexisting',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
            'CONTENT_TYPE' => 'text/html;charset=utf-8',
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        $response = $this->app->run(true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringStartsWith('text/html', $response->getHeaderLine('Content-type'));
    }

    public function testAuthControllerAuthActionCalled()
    {
        $env = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/auth',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        /** @var \Slim\Container $container */
        $container = $this->app->getContainer();

        /** @var GitHubIssues $gitHubIssues */
        $authController = $this->getMockBuilder(\Controller\AuthController::class)
            ->setConstructorArgs([
                $container->get('config'),
                $container->get(\Auth\AuthService::class),
                $container->get('router')
            ])
            ->setMethods(['authAction'])
            ->getMock();
        $authController->expects($this->once())
            ->method('authAction');

        $container[\Controller\AuthController::class] = function($container) use ($authController) {
            return $authController;
        };

        $this->app->run();
    }

    public function testAuthControllerLogoutActionCalled()
    {
        $env = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/logout',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        /** @var \Slim\Container $container */
        $container = $this->app->getContainer();

        /** @var GitHubIssues $gitHubIssues */
        $authController = $this->getMockBuilder(\Controller\AuthController::class)
            ->setConstructorArgs([
                $container->get('config'),
                $container->get(\Auth\AuthService::class),
                $container->get('router')
            ])
            ->setMethods(['logoutAction'])
            ->getMock();
        $authController->expects($this->once())
            ->method('logoutAction');

        $container[\Controller\AuthController::class] = function($container) use ($authController) {
            return $authController;
        };

        $this->app->run();
    }

    public function testIssueControllerIssueActionCalled()
    {
        $env = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/v1/issues/repos/aru-no/test2/number/1',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        \Auth\AuthService::setToken('token');

        /** @var \Slim\Container $container */
        $container = $this->app->getContainer();

        $container[\Issue\IssueService::class] = function($container) {
            $client = new \GitHubClient();

            return new \Issue\IssueService($container, $client);
        };

        /** @var GitHubIssues $gitHubIssues */
        $issueController = $this->getMockBuilder(\Controller\IssueController::class)
            ->setConstructorArgs([
                $container->get('config'),
                $container->get(\Issue\IssueService::class),
            ])
            ->setMethods(['issueAction'])
            ->getMock();
        $issueController->expects($this->once())
            ->method('issueAction');

        $container[\Controller\IssueController::class] = function($container) use ($issueController) {
            return $issueController;
        };

        $this->app->run(true);
    }

    public function testIssueControllerIssueListActionCalled()
    {
        $env = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/v1/issues',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        \Auth\AuthService::setToken('token');

        /** @var \Slim\Container $container */
        $container = $this->app->getContainer();

        $container[\Issue\IssueService::class] = function($container) {
            $client = new \GitHubClient();

            return new \Issue\IssueService($container, $client);
        };

        /** @var GitHubIssues $gitHubIssues */
        $issueController = $this->getMockBuilder(\Controller\IssueController::class)
            ->setConstructorArgs([
                $container->get('config'),
                $container->get(\Issue\IssueService::class),
            ])
            ->setMethods(['issueListAction'])
            ->getMock();
        $issueController->expects($this->once())
            ->method('issueListAction');

        $container[\Controller\IssueController::class] = function($container) use ($issueController) {
            return $issueController;
        };

        $this->app->run(true);
    }
}
