<?php
namespace thing\form;

class UserForm extends \Zend\Form\Form {
	public function __construct() {
		parent::__construct('user');
		$this->add([
				'name' => 'username',
				'type' => 'Text',
				'options' => [
					'label' => 'Username',
				],
			]
		);
		$this->add([
				'name' => 'email',
				'type' => 'Text',
				'options' => [
					'label' => 'E-mail',
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
				'name' => 'password2',
				'type' => 'Password',
				'options' => [
					'label' => 'Password (verify)',
				],
			]
		);
		$this->add([
				'name' => 'submit',
				'type' => 'Submit',
				'attributes' => [
					'id' => 'btn-user-submit',
					'value' => 'Submit',
				],
			]
		);
	}
}
