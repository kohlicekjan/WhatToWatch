<?php

class Form {

    private $action;
    private $nameForm;
    private $isPost;
    private $isToken;
    private $fields = [];
    private $buttons = [];
    private $currentField = null;
    public $isError = true;

    public function __construct($nameForm, $action = null, $isPost = true, $isToken = true) {
        $this->nameForm = $nameForm;
        $this->action = $action == null ? Redirect::currentUrl() : URL . $action;
        $this->isPost = $isPost;
        $this->isToken = ($isToken and $isPost);
        if ($this->isToken) {
            $this->addHidden('token', Auth::generateToken());
        }
    }

    public function generateHTML() {
        $formHTML = '<form action="' . $this->action . '" method="' . ($this->isPost ? 'post' : 'get') . '">';

        foreach ($this->fields as $name => $field) {
            if ($field['type'] == 'hidden') {
                $formHTML.='<input type="' . $field['type'] . '" name="' . $this->nameForm . '[' . $name . ']" value="' . h($field['value']) . '">';
                continue;
            }

            $formHTML.='<div class="formField">';
            $formHTML.='<label>' . $field['label'] . ((empty($field['required']) or $field['required']) ? '' : ' (nepovinné)') . '</label>';

            if ($field['type'] == 'info') {
                $formHTML.='<div>' . h($field['value']) . '</div>';
            } elseif ($field['type'] == 'select' or $field['type'] == 'multiple') {
                $formHTML.='<select name="' . $this->nameForm . '[' . $name . ']';
                $formHTML.= ($field['type'] == 'multiple' ? '[]" multiple="multiple"' : '"');
                $formHTML.= ($field['required'] ? ' required' : '');
                $formHTML.= $field['supplement'] . '>';

                foreach ($field['options'] as $key => $value) {
                    $formHTML.='<option';
                    $formHTML.=($field['isKey'] ? ' value="' . h($key) . '"' : '');
                    if (is_array($field['value'])) {
                        $formHTML.= in_array(($field['isKey'] ? $key : $value), $field['value']) ? ' selected' : '';
                    } else {
                        $formHTML.= (($field['isKey'] ? $key : $value) == $field['value']) ? ' selected' : '';
                    }
                    $formHTML.='>' . h($value) . '</option>';
                }

                $formHTML.='</select>';
            } elseif ($field['type'] == 'textarea') {
                $formHTML.='<textarea name="' . $this->nameForm . '[' . $name . ']"';
                $formHTML.= (empty($field['max']) ? '' : ' ');
                $formHTML.= ($field['required'] ? ' required' : '');
                $formHTML.= $field['supplement'] . '>' . h($field['value']) . '</textarea>';
            } else {
                $formHTML.='<input type="' . $field['type'] . '" name="' . $this->nameForm . '[' . $name . ']"';
                $formHTML.= ((empty($field['value']) or $field['type'] == 'password') ? '' : ' value="' . h($field['value']) . '"');
                $formHTML.= (empty($field['min']) ? '' : ' min="' . $field['min'] . '"');
                $formHTML.= (empty($field['maxLength']) ? '' : ' maxlength="' . $field['maxLength'] . '"');
                $formHTML.= (empty($field['max']) ? '' : ' max="' . $field['max'] . '"');
                $formHTML.= (empty($field['pattern']) ? '' : ' pattern="' . $field['pattern'] . '"');
                $formHTML.= ($field['required'] ? ' required' : '');
                $formHTML.= $field['supplement'] . '>';
            }

            $formHTML.=empty($field['error']) ? '' : '<span>' . $field['error'] . '</span>';
            $formHTML.='</div>';
        }

        $formHTML.='<div class="formButton">';
        foreach ($this->buttons as $name => $button) {
            if ($button['supplement'] == 'link') {
                $formHTML.='<a href="' . $name . '">' . $button['text'] . '</a>';
                continue;
            }

            $formHTML.='<input type="submit"';
            $formHTML.=(empty($name) ? '' : ' name="' . $this->nameForm . '[' . $name . ']"');
            $formHTML.= ' value="' . $button['text'] . '"' . $button['supplement'] . '>';
        }
        $formHTML.='</div>';
        $formHTML.='</form>';

        return $formHTML;
    }

    public function addHidden($name, $value) {
        $this->fields[$name] = ['type' => 'hidden', 'value' => $value];
        $this->currentField = null;
    }

    public function addField($label, $type, $name, $value = null, $supplement = '') {
        $this->fields[$name] = ['label' => $label, 'type' => $type, 'value' => $value, 'required' => false, 'supplement' => $supplement, 'error' => null];
        $this->currentField = $name;
        return $this;
    }

    public function addInfo($label, $name, $value) {
        $this->fields[$name] = ['label' => $label, 'type' => 'info', 'value' => $value];
    }

    public function addSelect($label, $name, $options, $value = null, $isKey = true, $supplement = '') {
        $this->fields[$name] = ['label' => $label, 'type' => 'select', 'options' => $options, 'value' => $value, 'isKey' => $isKey, 'required' => false, 'supplement' => $supplement, 'error' => null];
        $this->currentField = $name;
        return $this;
    }

    public function addMultiple($label, $name, $options, $value = null, $isKey = true, $supplement = '') {
        $this->fields[$name] = ['label' => $label, 'type' => 'multiple', 'options' => $options, 'value' => $value, 'isKey' => $isKey, 'required' => false, 'supplement' => $supplement, 'error' => null];
        $this->currentField = $name;
        return $this;
    }

    public static function optionsLoadDB($table, $key = 'id', $value = 'name') {
        $options = [];
        foreach ($table as $row) {
            $options[$row[$key]] = $row[$value];
        }

        return $options;
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
        $this->isError = true;
    }

    public function check() {

        if ($this->isPost and ! empty($_POST[$this->nameForm])) {
            $data = $_POST[$this->nameForm];
        } elseif (!$this->isPost and ! empty($_GET[$this->nameForm])) {
            $data = $_GET[$this->nameForm];
        } else {
            return;
        }
        $this->isError = false;

        if ($this->isToken) {
            if (empty($data['token']) or ! Auth::checkToken($data['token'])) {
                $this->isError = true;
                Message::addError('Token se neschoduje.');
            }
        }

        foreach ($this->fields as $name => $field) {

            if ($field['type'] == 'hidden' or $field['type'] == 'info') {
                continue;
            } elseif (!isset($data[$name]) or $data[$name] == '') {
                if ($field['required']) {
                    $this->fields[$name]['error'] = 'Tento údaj je povinný.';
                    $this->isError = true;
                }
                continue;
            }

            $this->fields[$name]['value'] = $data[$name];

            if ($field['type'] == 'select') {
                if ((!$field['isKey'] and ! in_array($data[$name], $field['options'])) or ( $field['isKey'] and ! array_key_exists($data[$name], $field['options']))) {
                    $this->fields[$name]['error'] = 'Poslaná hodnota neexistuje.';
                    $this->isError = true;
                }
            } elseif ($field['type'] == 'multiple') {

                if (!is_array($data[$name]) or count($data[$name]) <= 0) {
                    $this->fields[$name]['error'] = 'Poslaná hodnota neexistuje.';
                    $this->isError = true;
                    continue;
                }
                foreach ($data[$name] as $value) {
                    if ((!$field['isKey'] and ! in_array($value, $field['options'])) or ( $field['isKey'] and ! array_key_exists($value, $field['options']))) {
                        $this->fields[$name]['error'] = 'Poslaná hodnota neexistuje.';
                        $this->isError = true;
                        break;
                    }
                }
            } else {
                if ((!empty($field['minLength']) and $field['minLength'] > strlen($data[$name])) or ( !empty($field['maxLength']) and $field['maxLength'] < strlen($data[$name]))) {
                    $this->fields[$name]['error'] = 'Musíte zdat v rosahu' . (!empty($field['minLength']) ? ' od ' . $field['minLength'] : '') . (!empty($field['maxLength']) ? ' do ' . $field['maxLength'] : '') . ' znaků.';
                    $this->isError = true;
                    continue;
                } elseif ((!empty($field['min']) and $data[$name] < $field['min']) or ( !empty($field['max']) and $data[$name] > $field['max'])) {
                    $this->fields[$name]['error'] = 'Musíte zdat v rosahu' . (!empty($field['min']) ? ' od ' . $field['min'] : '') . (!empty($field['max']) ? ' do ' . $field['max'] : '') . '.';
                    $this->isError = true;
                    continue;
                }

                if ($field['type'] == 'email' and ! filter_var($data[$name], FILTER_VALIDATE_EMAIL)) {
                    $this->fields[$name]['error'] = 'Zadejte email.';
                    $this->isError = true;
                    continue;
                } elseif (!empty($field['pattern']) and ! preg_match('/^(' . $field['pattern'] . ')$/', $data[$name])) {
                    $this->fields[$name]['error'] = 'Musíte zadat požadovaný formát.';
                    $this->isError = true;
                    continue;
                }
            }
        }
    }

    private function s() {
        
    }

    public function isButton($name) {
        if ($this->isPost and ! empty($_POST[$this->nameForm])) {
            return !empty($_POST[$this->nameForm][$name]);
        } elseif (!$this->isPost and ! empty($_GET[$this->nameForm])) {
            return !empty($_GET[$this->nameForm][$name]);
        }

        return false;
    }

    public function getData() {
        if ($this->isError) {
            return false;
        }

        $data = [];

        foreach ($this->fields as $key => $field) {
            if ($key !== 'token')
                $data[$key] = $field['value'];
        }

        return $data;
    }

}
