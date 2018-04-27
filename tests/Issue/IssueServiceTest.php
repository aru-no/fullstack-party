<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Slim\Http\Request;
use \Slim\Http\Response;


class IssueServiceTest extends TestCase
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
            require_once($configFile);
        } else {
            exit('Config not found');
        }
        $this->app = (new \App\App($config))->getApp();
    }

    public function testGetIssue()
    {
        $env = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/v1/issues/repos/aru-no/test1/number/1',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
            'CONTENT_TYPE' => 'application/json;charset=utf8',
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        \Auth\AuthService::setToken('token');

        $gitHubIssue = new \GitHubIssue(json_decode(implode("\n",[
            file_get_contents(dirname(__FILE__ ). '/assets/github_issue.json')
        ])));

        /** @var GitHubIssues $gitHubIssues */
        $gitHubIssues = $this->getMockBuilder(\GitHubIssues::class)
                            ->setConstructorArgs([new \GitHubClient])
                            ->setMethods(['getIssue'])
                            ->getMock();
        $gitHubIssues->expects($this->once())
            ->method('getIssue')
            ->with(
                $this->equalTo('aru-no'),
                $this->equalTo('test1'),
                $this->equalTo(1)
            )
            ->willReturn($gitHubIssue);

        $gitHubClient = new \GitHubClient();
        $gitHubClient->setAuthType(\GitHubClientBase::GITHUB_AUTH_TYPE_OAUTH_BASIC);
        $gitHubClient->setOauthKey('token');
        $gitHubClient->issues = $gitHubIssues;

        $issueService = new \Issue\IssueService($this->app->getContainer(), $gitHubClient);

        $this->assertEquals(
            $issueService->getIssue('aru-no', 'test1', 1),
            \Issue\IssueEntityFactory::createFromGitHubIssue(
                $gitHubIssue,
                $this->app->getContainer()->get('router'),
                $this->app->getContainer()->get('request')
            )
        );
    }

    public function testGetIssueList()
    {
        $env = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/v1/issues',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
            'CONTENT_TYPE' => 'application/json;charset=utf8',
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        \Auth\AuthService::setToken('token');

        $gitHubIssue = new \GitHubIssue(json_decode(implode("\n",[
            file_get_contents(dirname(__FILE__ ). '/assets/github_issue.json')
        ])));

        /** @var GitHubIssues $gitHubIssues */
        $gitHubIssues = $this->getMockBuilder(\GitHubIssues::class)
            ->setConstructorArgs([new \GitHubClient])
            ->setMethods(['getIssue'])
            ->getMock();
        $gitHubIssues->expects($this->once())
            ->method('getIssue')
            ->with(
                $this->equalTo('aru-no'),
                $this->equalTo('test1'),
                $this->equalTo(1)
            )
            ->willReturn($gitHubIssue);

        $gitHubClient = new \GitHubClient();
        $gitHubClient->setAuthType(\GitHubClientBase::GITHUB_AUTH_TYPE_OAUTH_BASIC);
        $gitHubClient->setOauthKey('token');
        $gitHubClient->issues = $gitHubIssues;

        $issueService = new \Issue\IssueService($this->app->getContainer(), $gitHubClient);

        $this->assertEquals(
            $issueService->getIssue('aru-no', 'test1', 1),
            \Issue\IssueEntityFactory::createFromGitHubIssue(
                $gitHubIssue,
                $this->app->getContainer()->get('router'),
                $this->app->getContainer()->get('request')
            )
        );
    }
}
