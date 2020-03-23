<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;

use App\Helpers\SIARPSController;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * Renders error or exception pages from a given FlattenException.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class ErrorController extends SIARPSController {

    public function show(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null) {
        
        $template = '@Twig/Exception/error' . $exception->getStatusCode() . '.html.twig';
        try{
            return new Response($this->renderView($template,['status_code'=>$exception->getStatusCode()]),$exception->getStatusCode());
        } catch (\Twig\Error\LoaderError $ex) {
            $template = '@Twig/Exception/error.html.twig';
            return new Response($this->renderView($template,['status_code'=>$exception->getStatusCode()]),$exception->getStatusCode());
        }
    }

}
