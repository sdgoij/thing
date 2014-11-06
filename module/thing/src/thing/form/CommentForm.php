<?php
namespace thing\form;

class CommentForm extends \Zend\Form\Form {
	public function __construct($name = '') {
		parent::__construct('comment-submit');
		$this->add([
				'name' => 'link',
				'type' => 'Hidden',
			]
		);
		$this->add([
				'name' => 'parent',
				'type' => 'Hidden',
			]
		);
		$this->add([
				'name' => 'message',
				'type' => 'Textarea',
				'options' => [
					'label' => 'Message',
				],
			]
		);
		$this->add([
				'name' => 'submit',
				'type' => 'Submit',
				'attributes' => [
					'id' => 'btn-comment-submit',
					'value' => 'Submit',
				],
			]
		);
	}
}
