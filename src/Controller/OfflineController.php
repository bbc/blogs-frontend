<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OfflineController extends BaseController
{
    public function __invoke(Request $request): Response
    {
        return $this->renderWithChrome('offline/offline.html.twig', [
        ]);
    }
}
