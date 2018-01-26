<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractFeedController extends AbstractController
{
    protected function generateResponse(string $feedData): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setPublic()->setMaxAge(1800);
        $response->setContent($feedData);
        return $response;
    }
}
