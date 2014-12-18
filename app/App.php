<?php
require_once PATH_LIBS .'functions.php';

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
        $index=0;
        if (!empty($url[$index]) and file_exists(PATH_CONTROLLERS . $url[$index] . '.php')) {
            $this->controller = $url[$index];
            unset($url[$index]);
            $index++;
        }

        $this->controller = new $this->controller;

        if (!empty($url[$index]) and method_exists($this->controller, $url[$index])) {
            $reflection = new ReflectionMethod($this->controller, $url[$index]);
            if ($reflection->isPublic()) {
                $this->method = $url[$index];
                unset($url[$index]);
            }else{
                App::Error(404);
            }
        }elseif(!method_exists($this->controller, $this->method)){
            App::Error(404);
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
