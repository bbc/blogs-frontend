<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

// Loading config from Dotenv would go here, but we've purposefully decided to
// stick with using a parameters.yaml file instead of environment variables

$env = $_SERVER['APP_ENV'] ?? 'dev';
$debug = $_SERVER['APP_DEBUG'] ?? !in_array($env, ['prod', 'prod_int', 'prod_test', 'prod_int_fixture', 'prod_test_fixture']);

if ($debug) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}

/*
 * X-BBC-EDGE-* headers are added by Varnish, we need to convert them into forward headers that Symfony can act upon
 * https://confluence.dev.bbc.co.uk/display/ta/Request+and+response+HTTP+headers+-+standardised+format+for+BBC+traffic+management+services
 */
if (isset($_SERVER['HTTP_X_BBC_EDGE_CLIENT_IP'])) {
    $_SERVER['HTTP_X_FORWARDED_FOR'] = $_SERVER['HTTP_X_BBC_EDGE_CLIENT_IP'];
}
if (isset($_SERVER['HTTP_X_BBC_EDGE_HOST'])) {
    $_SERVER['HTTP_X_FORWARDED_HOST'] = $_SERVER['HTTP_X_BBC_EDGE_HOST'];
}
if (isset($_SERVER['HTTP_X_BBC_EDGE_SCHEME'])) {
    $_SERVER['HTTP_X_FORWARDED_PROTO'] = $_SERVER['HTTP_X_BBC_EDGE_SCHEME'];
    $_SERVER['HTTP_X_FORWARDED_PORT']  = $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 443 : 80;
} else {
    $_SERVER['HTTP_X_FORWARDED_PORT'] = $_SERVER['REQUEST_SCHEME'] == 'https' ? 443 : 80;
}

// Covers our subnets in dev and live
Request::setTrustedProxies(['10.0.0.0/8'], Request::HEADER_X_FORWARDED_ALL);

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
