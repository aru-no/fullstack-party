<?php
namespace Controller;

use \Slim\Http\Request;
use \Slim\Http\Response;
use Issue\IssueService;

class IssueController
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var IssueService
     */
    private $issueService;

    /**
     * IssueController constructor.
     * @param array $config
     * @param IssueService $issueService
     */
    public function __construct(array $config, IssueService $issueService)
    {
        $this->config = $config;
        $this->issueService = $issueService;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function issueAction (Request $request, Response $response, array $args)
    {
        try {
            /** @var \GitHubIssue $issue */
            $issue = $this->issueService->getIssue($args['user'], $args['repo'], (int)$args['number']);
        } catch (\GitHubClientException $e) {
            if ($e->getCode() == \GitHubClientException::INVALID_HTTP_CODE) {
                return $response
                    ->withStatus(403)
                    ->withJson(['error' => 'Bad credentials']);
            }

            return $response
                    ->withStatus(500)
                    ->withJson(['error' => $e->getMessage()]);
        }

        return $response->withJson($issue);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function issueListAction (Request $request, Response $response, array $args) {
        $page = (int) $request->getParam('page');
        if (empty($page)) {
            $page = 1;
        }

        $pageSize = (int) $request->getParam('page_size');
        if (empty($pageSize)) {
            $pageSize = $this->config['issue_list_page_size'];
        }

        try {
            /** @var array $issueCollection */
            $issueCollection = $this->issueService->getAllIssueCollection($page, $pageSize);
        } catch (\GitHubClientException $e) {
            if (\GitHubClientException::INVALID_HTTP_CODE == $e->getCode()) {
                return $response->withStatus(403);
            } else {
                return $response->withStatus(500);
            }
        }

        return $response->withJson($issueCollection);
    }
}