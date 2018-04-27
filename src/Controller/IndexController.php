<?php
namespace Controller;

use \Slim\Http\Request;
use \Slim\Http\Response;

class IndexController
{
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function indexAction(Request $request, Response $response, array $args)
    {
        // TODO

        $response->getBody()->write('index');

        return $response;
    }

}