<?php
namespace Application\Form;

class LinkForm extends \Zend\Form\Form {
    public function __construct($name = '') {
        parent::__construct('link-submit');
        $this->add([
             'name' => 'title',
             'type' => 'Text',
             'options' => [
                 'label' => 'Title',
             ],
         ]);
         $this->add([
             'name' => 'url',
             'type' => 'Url',
             'options' => [
                 'label' => 'URL',
             ],
         ]);
         $this->add([
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => [
                 'id' => 'btn-link-submit',
                 'value' => 'Submit',
             ],
         ]);
    }
}
