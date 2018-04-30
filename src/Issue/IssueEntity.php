<?php
namespace Issue;

use \Slim\Router;
use \Slim\Http\Request;
use \GitHubIssue;
use stdClass;

/**
 * Class IssueEntity
 * @package Issue
 */
class IssueEntity extends GitHubIssue implements \JsonSerializable
{
    /**
     * @var string
     */
    private $repository;

    /**
     * @param GitHubIssue $gitHubIssue
     * @param Router $router
     * @param Request $request
     */
    public function loadFromGitHubIssue(GitHubIssue $gitHubIssue, Router $router, Request $request)
    {
        $objValues = get_object_vars($gitHubIssue);
        foreach($objValues AS $key => $value)
        {
            $this->$key = $value;
        }

        // get repo from url
        $this->repository = explode('/', parse_url($this->getUrl())['path'])[3];

        $this->url =
            $request->getUri()->getBaseUrl()
            . $router->pathFor('issue', [
                'user' => $this->getUser()->getLogin(),
                'repo' => $this->getRepository(),
                'number' => $this->getNumber()
            ]);
    }

    /**
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'url' => $this->getUrl(),
            'number' => $this->getNumber(),
			'state' => $this->getState(),
			'title' => $this->getTitle(),
			'body' => $this->getBody(),
			'user' => $this->getUser()->getLogin(),
			'assignee' => $this->getAssignee()->getLogin(),
			'comments' => $this->getComments(),
			'created_at' => $this->getCreatedAt(),
        ];
    }
}