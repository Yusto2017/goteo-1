<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Foil\Extension;

use Symfony\Component\HttpFoundation\Request;
use Foil\Contracts\ExtensionInterface;
use Goteo\Application\Message;
use Goteo\Application\Cookie;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Model\User;
use Goteo\Library\Currency;

class GoteoCore implements ExtensionInterface
{

    private $args;
    private static $request;

    public static function setRequest(Request $request) {
        self::$request = $request;
    }

    public static function getRequest() {
        if(!self::$request)
            self::$request = Request::create();
        return self::$request;
    }

    public function setup(array $args = [])
    {
        $this->args = $args;
    }

    public function provideFilters()
    {
        return [];
    }

    public function provideFunctions()
    {
        return [
          'get_messages' => [$this, 'messages'],
          'get_errors' => [$this, 'errors'],
          'get_cookie' => [$this, 'get_cookie'],
          'get_session' => [$this, 'get_session'],
          'get_config' => [$this, 'get_config'],
          'get_user' => [$this, 'get_user'],
          'is_logged' => [$this, 'is_logged'],
          'has_role' => [$this, 'has_role'],
          'is_admin' => [$this, 'is_admin'],
          'is_module_admin' => [$this, 'is_module_admin'],
          'is_master_node' => [$this, 'is_master_node'],
          'get_query' => [$this, 'get_query'],
          'get_post' => [$this, 'get_post'],
          'get_uri' => [$this, 'get_uri'],
          'get_url' => [$this, 'get_url'],
          'get_pathinfo' => [$this, 'get_pathinfo'],
          'get_querystring' => [$this, 'get_querystring'],
          'is_ajax' => [$this, 'is_ajax'],
          'is_pronto' => [$this, 'is_pronto'],
          'get_currency' => [$this, 'get_currency'],
          'debug' => [$this, 'debug'],

        ];
    }

    public function debug()
    {
        return App::debug();
    }

    public function messages()
    {
        return Message::getMessages();
    }

    public function errors()
    {
        return Message::getErrors();
    }

    //Cookies
    public function get_cookie($var) {
        return Cookie::get($var);
    }

    //Session
    public function get_session($var) {
        return Session::get($var);
    }

    //returns if is a XmlHttpRequest (ajax) petition
    public function is_ajax() {
        return self::getRequest()->isXmlHttpRequest();
    }

    //returns if is a jquery.fs.pronto petition (ajax) petition
    // Pages using pronto must return code like:
    // json_encode(['title' => ...
    //              'content' => ... ]);
    public function is_pronto() {
        if(App::debug()) {
            return self::getRequest()->query->has('pronto');
        }
        return self::getRequest()->isXmlHttpRequest() && self::getRequest()->query->has('pronto');
    }

    //Request (_GET) var
    public function get_query($var = null) {
        if($var) return self::getRequest()->query->get($var);
        return self::getRequest()->query->all();
    }

    //Request (_POST) var
    public function get_post($var = null) {
        if($var) return self::getRequest()->request->get($var);
        return self::getRequest()->request->all();
    }

    //pathinfo
    public function get_uri() {
        return self::getRequest()->getUri();
    }

    //pathinfo
    public function get_pathinfo() {
        return self::getRequest()->getPathInfo();
    }

    //querystring
    public function get_querystring() {
        return self::getRequest()->getQueryString();
    }

    //Config
    public function get_config($var) {
        return Config::get($var);
    }

    //URL
    public function get_url($lang = null) {
        return Config::getUrl($lang);
    }

    //User
    public function get_user() {
        return Session::getUser();
    }

    //Currency
    public function get_currency() {
        return Currency::current('html');
    }

    // Checks user role
    public function has_role($role, $node = null, User $user = null) {
        if(empty($user)) $user = Session::getUser();
        if(empty($node)) $node = Config::get('current_node');
        if(Session::isLogged()) {
            if(!is_array($role)) $role = [$role];
            return $user->hasRoleInNode($node, $role);
        }
        return false;
    }

    // Returns if the user can admin anything or not
    public function is_admin() {
        return Session::isAdmin();
    }

    // Returns if the user can admin some specific module
    public function is_module_admin($subcontroller, $node = null, User $user = null) {
        return Session::isModuleAdmin($subcontroller, $node, $user);
    }

    //is logged
    public function is_logged() {
        return Session::isLogged();
    }

    //is master node
    public function is_master_node($node = null) {
        return Config::isMasterNode($node);
    }
}
