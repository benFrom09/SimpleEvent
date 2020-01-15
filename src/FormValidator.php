<?php
namespace App;

use DateTime;

class FormValidator
{
    protected $data;

    protected $errors = [];


    /**
     * validate form entry
     *
     * @param array $data
     * @return array|bool
     */
    public function validate(array $data) {
        $this->errors = [];
        $this->data = $data;
    }

    public function check(string $field,string $method,...$params) {
        if(!array_key_exists($field,$this->data)){
            $this->errors[$field] = "Le champs $field est obligatoire!";
        } else {
            call_user_func([$this,$method],$field,...$params);
        }
    }

    public function minLength(string $field,int $len) {
        if(strlen($this->data[$field]) < $len) {
            $this->errors[$field] = "Le champs $field doit contenir au minimum $len caractères ";
            return false;
        }
        return true;
    }

    public function date(string $field) {
        if(DateTime::createFromFormat('Y-m-d',$this->data[$field]) === false) {
            $this->errors[$field] = "Le format de la date est invalide!";
            return false;
        }
        return true;
    }

    public function time(string $field) {
        if(DateTime::createFromFormat('H:i',$this->data[$field]) === false) {
            $this->errors[$field] = "Le format de l'heure est invalide!";
            return false;
        }
        return true;
    }

    public function isBefore(string $start,string $end) {
        if($this->time($start) && $this->time($end)) {
            $start = DateTime::createFromFormat('H:i',$this->data[$start]);
            $end = DateTime::createFromFormat('H:i',$this->data[$end]);
            if($start->getTimestamp() > $end->getTimestamp()) {
                $this->errors[$start] = "L'heure de début doit être inférieure à l'heure d'arrivée!";
                return false;
            }
            return true;
        }
        $this->errors[$start] = "Un des champs $start ou $end est incorrect!";
        return false;
    }
}