<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\EventDispatcher\Event;
use Goteo\Application\App;
use Goteo\Application\View;
use Goteo\Core\Traits\LoggerTrait;

abstract class Controller {
    use LoggerTrait;

    /**
     * Handy method to send a response from a view
     */
    public function viewResponse($view, $vars = [], $status = 200) {
        return new Response(View::render($view, $vars), $status);
    }

    /**
     * Handy method to send a response any string
     */
    public function rawResponse($string, $contentType = 'text/plain' , $status = 200, $file_name = '') {
        $response = new Response($string, $status, ['Content-Type' => $contentType]);

        if($file_name) {
            $d = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file_name
            );
            $response->headers->set('Content-Disposition', $d);
        }
        return $response;
    }

    /**
     * **Experimental** method to send a response in json, vars only
     */
    public function jsonResponse($vars = []) {
        $resp =  new JsonResponse($vars);
        if(App::debug()) $resp->setEncodingOptions(JSON_PRETTY_PRINT);
        return $resp;
    }

    /**
     * Handy method to return a redirect response
     */
    public function redirect($path, $status = 302) {
        return new RedirectResponse($path, $status);
    }

    /**
     * Handy method to obtain the view engine object
     */
    public function getViewEngine() {
        return View::getEngine();
    }

    /**
     * Handy method to add context vars to all view
     */
    public function contextVars(array $vars = [], $view_path_context = null) {
        if($view_path_context) {
            View::getEngine()->useContext($view_path_context, $vars);
        } else {
            View::getEngine()->useData($vars);
        }
    }

    /**
     * Handy method to get the service container object
     */
    public function getContainer() {
        return App::getServiceContainer();
    }

    /**
     * Handy method to get the getService function
     */
    public function getService($service) {
        return App::getService($service);
    }

    /**
     * Handy method to get the dispatch function
     */
    public function dispatch($eventName, Event $event = null) {
        return App::dispatch($eventName, $event);
    }
}
