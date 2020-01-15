<?php
namespace SimpleEvent;

use App\FormValidator;

class EventPostValidator extends FormValidator
{
    /**
     * Undocumented function
     *
     * @param array $data
     * @return array|bool
     */
    public function validate(array $data) {
        parent::validate($data);
        $this->check('name','minLength',4);
        $this->check('date','date');
        $this->check('start_time','isBefore','end_time');
        return $this->errors;
    }

    
}