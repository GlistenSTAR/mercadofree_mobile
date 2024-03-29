<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../app/autoload.php';
include_once __DIR__.'/../app/bootstrap.php.cache';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

// Request::setTrustedProxies(
//     // trust *all* requests
//     ['127.0.0.1', $request->server->get('REMOTE_ADDR')],

//     // only trust X-Forwarded-Port/-Proto, not -Host
//     Request::HEADER_X_FORWARDED_AWS_ELB
// );