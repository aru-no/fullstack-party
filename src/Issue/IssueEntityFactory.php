<?php
namespace Issue;

use \Slim\Router;
use \Slim\Http\Request;
use \GitHubIssue;
use stdClass;

class IssueEntityFactory
{
    /**
     * @param GitHubIssue $gitHubIssue
     * @param Router $router
     * @param Request $request
     * @return IssueEntity
     * @throws \GitHubClientException
     */
    public static function createFromGitHubIssue(
        GitHubIssue $gitHubIssue,
        Router $router,
        Request $request
    ): IssueEntity
    {
        $issueEntity = new IssueEntity(new stdClass());
        $issueEntity->loadFromGitHubIssue($gitHubIssue, $router, $request);

        return $issueEntity;
    }
}