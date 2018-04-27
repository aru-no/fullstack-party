<?php
namespace Issue;

use Psr\Container\ContainerInterface;
use GitHubClient;
use GitHubClientBase;
use Milo\Github\Api as GitHubApi;
use Milo\Github\OAuth\Token;
use Auth\AuthService;

/**
 * Class IssueService
 * @package Issue
 */
class IssueService
{
    const REPOSITORY_PAGE = 1;
    const REPOSITORY_PAGE_SIZE = 10;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var GitHubClient
     */
    private $client;

    /**
     * @var GitHubApi
     */
    private $gitHubApi;

    /**
     * IssueService constructor.
     * @param ContainerInterface $container
     * @param GitHubClient $client
     * @param GitHubApi $gitHubApi
     */
    public function __construct(ContainerInterface $container, GitHubClient $client, GitHubApi $gitHubApi = null)
    {
        $this->container = $container;
        $this->client = $client;

        $this->gitHubApi = $gitHubApi;
    }

    /**
     * @param string $user
     * @param string $repository
     * @param int $number
     * @return \GitHubIssue
     * @throws \GitHubClientException
     */
    public function getIssue(string $user, string $repository, int $number): \GitHubIssue
    {
        /** @var \GitHubIssue $gitHubIssue*/
        $gitHubIssue = $this->client->issues->getIssue($user, $repository, $number);
        /** @var IssueEntity $issueEntity */
        $issueEntity = IssueEntityFactory::createFromGitHubIssue(
            $gitHubIssue,
            $this->container->get('router'),
            $this->container->get('request')
        );

        return $issueEntity;
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \GitHubClientException
     */
    public function getAllIssueCollection(int $page, int $pageSize): array
    {
        $this->client->setPage(self::REPOSITORY_PAGE);
        $this->client->setPageSize(self::REPOSITORY_PAGE_SIZE);
        /** @var array $gitHubReposCollection */
        $gitHubReposCollection = $this->client->repos->listYourRepositories(
            'all',
            'updated',
            'desc'
        );

        $allIssueCollection = [];
        /** @var \GitHubRepo $gitHubRepo */
        foreach ($gitHubReposCollection as $gitHubRepo) {
            /** @var array repoIssueCollection */
            $repoIssueCollection = $this->client->issues->listIssues(
                $gitHubRepo->getOwner()->getLogin(),
                $gitHubRepo->getName(),
                null,
                null,
                null,
                null,
                null,
                null,
                'updated'
            );
            $allIssueCollection = array_merge(
                $allIssueCollection,
                $repoIssueCollection
            );
        }

        // convert to JsonSerializable entities
        $issueCollection = [];

        $totalIssueCount = count($allIssueCollection);
        $openIssueCount = 0;

        $i = 0;
        /** @var \GitHubIssue $gitHubIssue */
        foreach ($allIssueCollection as $gitHubIssue) {
            if ('open' === $gitHubIssue->getState()) {
                $openIssueCount++;
            }
            // paging and converting to JsonSerializable entities
            if (($page-1) * $pageSize <= $i && $i < $page * $pageSize) {
                $issueCollection[] = IssueEntityFactory::createFromGitHubIssue(
                    $gitHubIssue,
                    $this->container->get('router'),
                    $this->container->get('request')
                );
            }
            $i++;
        }

        return [
            'totals' => [
                'total' => $totalIssueCount,
                'open' => $openIssueCount,
                'closed' => $totalIssueCount - $openIssueCount,
            ],
            'list' => $issueCollection,
        ];
    }
}