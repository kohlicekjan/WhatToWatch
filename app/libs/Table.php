<?php

class Table {

    private $data;
    private $columns = [];
    private $page = 1;
    private $paginationRows;
    public $paginationMaxRows = DEFAULT_ROWS_TABLE;

    public function __construct($page = 'page') {

        if (is_numeric($page)) {
            $this->page = $page;
        } elseif (!empty($_GET[$page]) and is_numeric($_GET[$page])) {
            $this->page = $_GET[$page];
        }
    }

    public function loadDB($model, $method, $parameters = []) {
        $parameters['start'] = ($this->page - 1) * $this->paginationMaxRows;
        $parameters['limit'] = $this->paginationMaxRows;
        $this->data = $model->$method($parameters);
        $this->paginationRows = $model->found;
    }

    public function loadArray($data) {
        $this->paginationRows = count($data);
        $this->data = array_slice($data, ($this->page - 1) * $this->paginationMaxRows, $this->paginationMaxRows);
    }

    public function addColumn($data,$title = '', $format = null) {
        $this->columns[] = ['data' => $data, 'format' => $format, 'title' => $title];
    }

    public function generateHTML($isPaging = true) {

        if ($this->paginationRows == 0 or count($this->data) == 0) {
            return 'Nenalezeny žádné záznamy.';
        }

        $html = '<table>';
        $html.='<tr>'; //header
        foreach ($this->columns as $column) {
            $html.='<th>';
            $html.=$column['title'];
            $html.='</th>';
        }
        $html.='</tr>'; //header

        foreach ($this->data as $row) {

            $html.='<tr>';
            foreach ($this->columns as $column) {
                $html.='<td>';
                $cell = $column['format'];
                if (is_array($column['data'])) {
                    foreach ($column['data'] as $key => $value) {
                        $cell = str_replace('{' . $key . '}', h($row[$value]), $cell);
                    }
                } elseif (!empty($column['format'])) {
                    $cell = str_replace('{0}', h($row[$column['data']]), $cell);
                } else {
                    $cell = h($row[$column['data']]);
                }
                $html.=$cell;
                $html.='</td>';
            }
            $html.='</tr>';
        }

        $html.='</table>';

        if ($isPaging) {
            $html.=$this->generatePaging();
        }

        return $html;
    }

    private function generatePaging() {

        $last = ceil($this->paginationRows / $this->paginationMaxRows);

        if ($last == 1) {
            return '';
        }

        $start = max($this->page - 2, 1);
        $end = min($this->page + 2, $last);

        $html = '<div class="paging">';

        if ($this->page != 1) {
            $html .= '<a style="prev" href="?page=' . ( $this->page - 1 ) . '">&lt; Předchozí</a>';
        }

        if ($start > 2) {
            $html .= '<a href="?page=1">1</a>';
            $html .= '<span>...</span>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $html.= '<a' . ($this->page == $i ? ' class="current"' : '') . ' href="?page=' . $i . '">' . $i . '</a>';
        }

        if (($end+1) < $last) {
            $html .= '<span>...</span>';
            $html .= '<a href="?page=' . $last . '">' . $last . '</a>';
        }

        if ($this->page != $last) {
            $html .= '<a style="next" href="?page=' . ($this->page + 1 ) . '">Další &gt;</a>';
        }

        $html .= '</div>';

        $html.='<div>';
        $html.='<a href="?page=">Zobrazit další</a>';
        $html.='</div>';

        return $html;
    }

}
