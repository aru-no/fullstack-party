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
     * @var router
     */
    private $router;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $repository;

    /**
     * IssueEntity constructor.
     * @param stdClass $json
     * @param Router $router
     * @param Request $request
     * @throws \GitHubClientException
     */
    public function __construct(stdClass $json, Router $router, Request $request)
    {
        parent::__construct($json);

        $this->router = $router;
        $this->request = $request;
    }

    /**
     * @param GitHubIssue $gitHubIssue
     */
    public function loadFromGitHubIssue(GitHubIssue $gitHubIssue)
    {
        $objValues = get_object_vars($gitHubIssue);
        foreach($objValues AS $key => $value)
        {
            $this->$key = $value;
        }

        // get repo from url
        $this->repository = explode('/', parse_url($this->getUrl())['path'])[3];

        $this->url =
            $this->request->getUri()->getBaseUrl()
            . $this->router->pathFor('issue', [
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