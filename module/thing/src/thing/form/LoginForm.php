<?php
namespace thing\form;

class LoginForm extends \Zend\Form\Form {
	public function __construct() {
		parent::__construct('user-login');
		$this->add([
				'name' => 'username',
				'type' => 'Text',
				'options' => [
					'label' => 'Username',
				],
			]
		);
		$this->add([
				'name' => 'password',
				'type' => 'Password',
				'options' => [
					'label' => 'Password',
				],
			]
		);
		$this->add([
				'name' => 'submit',
				'type' => 'Submit',
				'attributes' => [
					'id' => 'btn-user-login',
					'value' => 'Submit',
				],
			]
		);
	}
}
