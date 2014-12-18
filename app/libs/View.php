<?php

class View {

    public $statusHTTP = '200 OK';

    public function generateDownload() {
        
    }

    public function generateJSON($data = []) {
        $this->HeaderHTTP("application/json");

        ob_start("ob_gzhandler", 4096);

        echo json_encode($data);

        ob_end_flush();
        exit;
    }

    public function generatePage($name, $template = 'default') {
        $this->name = $name;
        $this->HeaderHTTP();

        ob_start("ob_gzhandler", 4096);
        ob_start('View::compressPage', 4096);

        $this->pageHeader($template);
        require_once PATH_VIEWS . $name . '.php';
        $this->pageFooter($template);

        ob_end_flush();
        exit;
    }

    private function pageHeader($template) {
        if (!empty($template)) {
            $this->messages = Message::getMessages($this->name);
            $this->pageTitle = (empty($this->title) ? '' : $this->title . ' - ') . PAGE_TITLE;
            require_once PATH_TEMPLATE . $template . '/header.php';
        }
    }

    private function pageFooter($template) {
        if (!empty($template)) {
            require_once PATH_TEMPLATE . $template . '/footer.php';
        }
    }

    public function setNavigation($name) {
        require_once PATH_NAVIGATION . '/' . $name . '.php';

        $this->{'nav' . ucfirst($name)} = $nav;
    }

    private function HeaderHTTP($contentType = 'text/html') {
        header('Version: HTTP/1.1');
        header('Cache-Control: private, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: -1');
        header('X-Frame-Options: Deny');
        header('X-XSS-Protection: 0');
        header('Content-Type: ' . $contentType . '; charset=utf-8');
        header('HTTP/1.1 ' . $this->statusHTTP);
        header('Status: ' . $this->statusHTTP);
    }

    public static function compressPage($buffer) {
        return trim(str_replace(array("> <", "\r\n", "\r", "\n", "\t", '  '), array('><', ''), $buffer));
    }

}
