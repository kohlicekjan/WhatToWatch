<?php

class Form {

    private $method;
    private $action;
    private $fields = [];
    private $buttons = [];
    private $nameForm;
    private $currentField = null;
    public $isError = false;

    public function __construct($method, $action, $nameForm) {
        $this->method = $method;
        $this->action = $action;
        $this->nameForm = $nameForm;
    }

    public function getHTML() {
        $formHtml = '<form action="' . $this->action . '" method="' . $this->method . '">';

        foreach ($this->fields as $name => $field) {
            if ($field['type'] == 'hidden') {
                $formHtml.='<input type="' . $field['type'] . '" name="' . $this->nameForm . '[' . $name . ']" value="' . $field['value'] . '">';
                continue;
            }

            $formHtml.='<div class="formField">';
            $formHtml.='<label for="' . $name . '">' . $field['label'] . ($field['required'] ? '' : ' (nepovinné)') . '</label>';

            if ($field['type'] == 'select') {
                $formHtml.='<select name="' . $this->nameForm . '[' . $name . ']"';
                $formHtml.=($field['required'] ? ' required' : '');
                $formHtml.= $field['supplement'] . '>';

                foreach ($field['options'] as $key => $value) {
                    $formHtml.='<option';
                    $formHtml.=($field['isKey'] ? ' value="' . $key . '"' : '');
                    $formHtml.=((!$field['isKey'] and $value == $field['value'] or ( $field['isKey'] and $key == $field['value'])) ? ' selected' : '');
                    $formHtml.='>' . $value . '</option>';
                }

                $formHtml.='</select>';
            } elseif ($field['type'] == 'textarea') {
                $formHtml.='<textarea name="' . $this->nameForm . '[' . $name . ']"';
                $formHtml.= (empty($field['max']) ? '' : ' ');
                $formHtml.= ($field['required'] ? ' required' : '');
                $formHtml.= $field['supplement'] . '>' . $field['value'] . '</textarea>';
            } else {
                $formHtml.='<input type="' . $field['type'] . '" name="' . $this->nameForm . '[' . $name . ']" id="' . $name . '"';
                $formHtml.= (empty($field['value']) ? '' : ' value="' . $field['value'] . '"');
                $formHtml.= (empty($field['min']) ? '' : ' min="' . $field['min'] . '"');
                $formHtml.= (empty($field['maxLength']) ? '' : ' maxlength="' . $field['maxLength'] . '"');
                $formHtml.= (empty($field['max']) ? '' : ' max="' . $field['max'] . '"');
                $formHtml.= (empty($field['pattern']) ? '' : ' pattern="' . $field['pattern'] . '"');
                $formHtml.= ($field['required'] ? ' required' : '');
                $formHtml.= $field['supplement'] . '>';
            }

            $formHtml.=empty($field['error']) ? '' : '<span>' . $field['error'] . '</span>';
            $formHtml.='</div>';
        }

        $formHtml.='<div class="formButton">';
        foreach ($this->buttons as $name => $button) {
            if ($button['supplement'] == 'link') {
                $formHtml.='<a href="' . $name . '">' . $button['text'] . '</a>';
                continue;
            }
            
            $formHtml.='<input type="submit"';
            $formHtml.=(empty($button['name']) ? '' : ' name="' . $this->nameForm . '[' . $button['name'] . ']"');
            $formHtml.= ' value="' . $button['text'] . '"' . $button['supplement'] . '>';
        }
        $formHtml.='</div>';
        $formHtml.='</form>';

        return $formHtml;
    }

    public function addField($label, $type, $name, $value = null, $supplement = '') {
        $this->fields[$name] = ['label' => $label, 'type' => $type, 'value' => $value, 'required' => false, 'supplement' => $supplement, 'error' => null];
        $this->currentField = $name;
        return $this;
    }

    public function addSelect($label, $name, $options, $value = null, $isKey = true, $supplement = '') {
        $this->fields[$name] = ['label' => $label, 'type' => 'select', 'options' => $options, 'value' => $value, 'isKey' => $isKey, 'required' => false, 'supplement' => $supplement, 'error' => null];
        $this->currentField = $name;
        return $this;
    }

    public function addButton($text, $name = null, $supplement = '') {
        $this->buttons[$name] = ['text' => $text, 'supplement' => $supplement];
        return $this;
    }

    public function setRequired($required) {
        $this->fields[$this->currentField]['required'] = $required;
        return $this;
    }

    public function setMin($min) {
        $this->fields[$this->currentField]['min'] = $min;
        return $this;
    }

    public function setMax($max) {
        $this->fields[$this->currentField]['max'] = $max;
        return $this;
    }

    public function setMaxLength($maxLength) {
        $this->fields[$this->currentField]['maxLength'] = $maxLength;
        return $this;
    }

    public function setMinLength($minLength) {
        $this->fields[$this->currentField]['minLength'] = $minLength;
        return $this;
    }

    public function setPattern($pattern) {
        $this->fields[$this->currentField]['pattern'] = $pattern;
        return $this;
    }

    public function setError($name, $error) {
        $this->fields[$name]['error'] = $error;
    }

    public function control() {

        if ($this->method == 'post' and ! empty($_POST[$this->nameForm])) {
            $data = $_POST[$this->nameForm];
        } elseif ($this->method == 'get' and ! empty($_GET[$this->nameForm])) {
            $data = $_GET[$this->nameForm];
        } else {
            return;
        }

        foreach ($this->fields as $name => $field) {

            if ($field['required'] and empty($data[$name])) {
                $this->fields[$name]['error'] = 'Tento údaj je povinný.';
                continue;
            }

            $this->fields[$name]['value'] = $data[$name];

            if (!empty($data[$name]) and $field['type'] == 'select') {
                if (!(!$field['isKey'] and in_array($field['value'], $field['options'])) and ! ($field['isKey'] and array_key_exists($field['value'], $field['options']))) {
                    $this->fields[$name]['error'] = 'Poslaná hodnota neexistuje.';
                    $this->isError = true;
                }
            } else {
                if (!empty($data[$name])and ( (!empty($field['minLength']) and $field['minLength'] > strlen($data[$name])) or ( !empty($field['maxLength']) and $field['maxLength'] < strlen($data[$name])))) {
                    $this->fields[$name]['error'] = 'Musíte zdat v rosahu' . (!empty($field['minLength']) ? ' od ' . $field['minLength'] : '') . (!empty($field['maxLength']) ? ' do ' . $field['maxLength'] : '') . ' znaků.';
                    $this->isError = true;
                    continue;
                } elseif (!empty($data[$name]) and ( (!empty($field['min']) and $data[$name] < $field['min']) or ( !empty($field['max']) and $data[$name] > $field['max']))) {
                    $this->fields[$name]['error'] = 'Musíte zdat v rosahu' . (!empty($field['min']) ? ' od ' . $field['min'] : '') . (!empty($field['max']) ? ' do ' . $field['max'] : '') . '.';
                    $this->isError = true;
                    continue;
                }

                if (!empty($data[$name]) and ! empty($field['pattern']) and ! preg_match('/^(' . $field['pattern'] . ')$/', $data[$name])) {
                    $this->fields[$name]['error'] = 'Musíte zadat požadovaný formát.';
                    $this->isError = true;
                    continue;
                }
            }
        }
    }

    public function getData() {
        if ($this->isError) {
            return false;
        }

        $data = [];

        foreach ($this->fields as $field) {
            $data[$field['name']] = $field['value'];
        }

        return $data;
    }

    function token() {
        
    }

}
