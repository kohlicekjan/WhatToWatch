<?php

class App {

    private $controller = DEFAULT_CONTROLLER;
    private $method = DEFAULT_METHOD;
    private $params = [];

    public function __construct() {
        spl_autoload_register(array($this, 'loader'));

        $url = $this->getUrl();
        $this->loadController($url);
    }

    private function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        } else {
            return null;
        }
    }

    private function loadController($url) {

        if (!empty($url[0]) and file_exists(PATH_CONTROLLERS . $url[0] . '.php')) {
            $this->controller = $url[0];
            unset($url[0]);
        } elseif (!empty($url[0])) {
            App::Error(404);
        }

        $this->controller = new $this->controller;

        if (!empty($url[1]) and method_exists($this->controller, $url[1])) {
            $reflection = new ReflectionMethod($this->controller, $url[1]);
            if ($reflection->isPublic()) {
                $this->method = $url[1];
                unset($url[1]);
            }else{
                App::Error(404);
            }
        }

        if ($url) {
            $this->params = array_values($url);
        }

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public static function Error($error = 404) {
        call_user_func_array([new Error(), 'index'], [$error]);
        exit;
    }

    private function loader($className) {

        $pathContorllers = PATH_CONTROLLERS . $className . '.php';
        $pathLibs = PATH_LIBS . $className . '.php';
        $pathModels = PATH_MODELS . $className . '.php';

        if (file_exists($pathContorllers)) {
            require_once $pathContorllers;
        } elseif (file_exists($pathLibs)) {
            require_once $pathLibs;
        } elseif (file_exists($pathModels)) {
            require_once $pathModels;
        }
    }

}
