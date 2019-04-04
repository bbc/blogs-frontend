<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Image;
use App\ValueObject\MetaContext;
use BBC\BrandingClient\BrandingClient;
use BBC\BrandingClient\BrandingException;
use BBC\BrandingClient\OrbitClient;
use BBC\BrandingClient\OrbitException;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig_Environment;

class ExceptionController extends BaseExceptionController
{
    /**
     * @var BrandingClient
     */
    private $brandingClient;

    /**
     * @var OrbitClient
     */
    private $orbitClient;

    public function __construct(Twig_Environment $twig, BrandingClient $brandingClient, OrbitClient $orbitClient, $debug)
    {
        parent::__construct($twig, $debug);
        $this->brandingClient = $brandingClient;
        $this->orbitClient = $orbitClient;
    }

    public function __invoke(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        /** @var int $phpObLevel */
        $phpObLevel = $request->headers->get('X-Php-Ob-Level', -1);
        $currentContent = $this->getAndCleanOutputBuffering($phpObLevel);
        $showException = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC
        // base.html.twig requires the ORB and Branding. If either of those fail
        // we still need to be able to render an exception rather than just blow
        // up with an empty response. There is a separate base template that does not
        // require either of those things. It looks hideous, but meh.
        $errorBaseTemplate = 'base.html.twig';

        $code = $exception->getStatusCode();
        $orb = $branding = null; //No need for Orb or Branding when developing locally

        $headers = [
            'Content-Type' => $request->getMimeType($request->getRequestFormat()) ?: 'text/html',
            // Blocks a request if the requested type is different from the MIME type
            'X-Content-Type-Options' => 'nosniff',
        ];

        if (!$request->query->get('preview')) {
            // The page can only be displayed in a frame on the same origin as the page itself.
            $headers['X-Frame-Options'] = 'SAMEORIGIN';
        }

        if (!$showException) {
            try {
                $branding = $this->brandingClient->getContent('br-07918');
                $orb = $this->orbitClient->getContent([
                    'variant' => $branding->getOrbitVariant(),
                    'language' => 'en_GB',
                ], [
                    'searchScope' => $branding->getOrbitSearchScope(),
                    'skipLinkTarget' => 'blogs-content',
                ]);

                // For testing the no Orbit or Branding template
                if ($request->query->has('noorb')) {
                    throw new OrbitException();
                }
            } catch (BrandingException | OrbitException $e) {
                // Ignore exceptions from branding and ORB and override template
                // not to require them
                $errorBaseTemplate = '@Twig/Exception/base_no_orb.html.twig';
            }
        }

        // In production, cache 4xx error codes for a little while
        if (!$showException && $code >= 400 && $code <= 499) {
            $headers['Cache-Control'] = 'public, max-age=60';
        }

        // The 200 status here is a misnomer, it is a default and shall be
        // overridden later.
        return new Response($this->twig->render(
            $this->findTemplate($request, $request->getRequestFormat(), $code, $showException),
            [
                'base_template' => $errorBaseTemplate,
                'status_code' => $code,
                'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception' => $exception,
                'logger' => $logger,
                'currentContent' => $currentContent,
                'orb' => $orb,
                'branding' => $branding,
                'meta_context' => new MetaContext(false),
                'fallback_social_image' => new Image('p01tqv8z.png'),
                'istats_counter_name' => '',

            ]
        ), 200, $headers);
    }
}
