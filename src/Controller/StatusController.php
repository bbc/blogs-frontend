<?php
declare(strict_types = 1);
namespace App\Controller;

use Cake\Chronos\Chronos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        // If the load balancer is pinging us then give them a plain OK
        if ($request->headers->get('User-Agent') == 'ELB-HealthChecker/1.0') {
            return new Response('OK', Response::HTTP_OK, ['content-type' => 'text/plain']);
        }

        // Other people get a better info screen
        return $this->render('status/status.html.twig', [
            'now' => Chronos::now(),
        ]);
    }
}
