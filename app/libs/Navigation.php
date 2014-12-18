<?php

class Navigation {

    private $url = '';

    public function __construct() {

        if (!empty($_GET['url'])) {
            $this->url = $_GET['url'];
            if ($this->url{strlen($this->url) - 1} != '/') {
                $this->url.='/';
            }
        }
    }

    private $items = [];

    public function addItem($url, $name, $role = null) {
        $this->items[] = ['url' => $url, 'name' => $name, 'role' => $role, 'subItem' => ''];
    }

    public function setSubItem($html) {
        $this->items[count($this->items) - 1]['subItem'] = $html;
    }

    public function generateHTML($isSubNav = false) {

        $html = $isSubNav ? '' : '<nav>';
        $html.='<ul>';

        foreach ($this->items as $item) {
            if (!empty($item['role']) and ! in_array($item['role'], Session::get('user')['role'])) {
                continue;
            }

            $class = '';
            if (strtolower(URL . $item['url']) == strtolower(URL . $this->url)) {
                $class = ' class="active"';
            }
            $html.='<li><a href="' . URL . $item['url'] . '"' . $class . '>' . $item['name'] . '</a>' . $item['subItem'] . '</li>';
        }

        $html.='</ul>';
        $html.=$isSubNav ? '' : '</nav>';

        return $html;
    }

}
