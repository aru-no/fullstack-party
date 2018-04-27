<?php
namespace App;

use Psr\Container\ContainerInterface;

class DependencyInjector
{
    public static function inject(ContainerInterface $container)
    {
        $container[\Auth\AuthMiddleware::class] = function($container) {
            return new \Auth\AuthMiddleware();
        };

        $container[\Issue\IssueService::class] = function($container) {
            $client = new \GitHubClient();
            $client->setAuthType(\GitHubClientBase::GITHUB_AUTH_TYPE_OAUTH_BASIC);
            $client->setOauthKey(\Auth\AuthService::getToken());

            return new \Issue\IssueService($container, $client);
        };

        $container[\Auth\AuthService::class] = function($container) {
            return new \Auth\AuthService($container->get('config')['github_authorization']);
        };

        $container[\Controller\IndexController::class] = function($container) {
            return new \Controller\IndexController();
        };

        $container[\Controller\AuthController::class] = function($container) {
            return new \Controller\AuthController(
                $container->get('config'),
                $container->get(\Auth\AuthService::class),
                $container->get('router')
            );
        };

        $container[\Controller\IssueController::class] = function($container) {
            return new \Controller\IssueController(
                $container->get('config'),
                $container->get(\Issue\IssueService::class)
            );
        };

        return $container;
    }
}