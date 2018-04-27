<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class IssueEntityTest extends TestCase
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

    public function testCanBeCreated()
    {
        $gitHubIssue = new \GitHubIssue(json_decode(implode("\n",[
            file_get_contents(dirname(__FILE__ ). '/assets/github_issue.json')
        ])));

        $request = $this->app->getContainer()->get('request');

        $this->assertInstanceOf(
            \Issue\IssueEntity::class,
            \Issue\IssueEntityFactory::createFromGitHubIssue(
                $gitHubIssue,
                $this->app->getContainer()->get('router'),
                $this->app->getContainer()->get('request')
            )
        );
    }
}