<?php
namespace Application\Controller;

use Application\Form\LoginForm;
use Application\Form\UserForm;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class ActionController extends AbstractActionController {
    protected function post(Form $form, \Closure $handler) {
        return ($this->getRequest()->isPost() && $form->setData($this->getRequest()->getPost())->isValid())
            ? $handler($form) : ['form' => $form];
    }
    protected function getEntityManager() {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }
}

class UserController extends ActionController {
    public function registerAction() {
        return $this->post(new UserForm(),
            function(Form $form) {
                $data = $form->getdata();
                if ($data['password'] == $data['password2']) {
                    $user = new \Application\Entity\User();
                    $user->setUsername($data['username']);
                    $user->setPassword($data['password']);
                    $user->setCreated(new \DateTime());
                    $user->setEmail($data['email']);

                    $em = $this->getEntityManager();
                    $em->persist($user);
                    $em->flush();

                    $this->redirect()->toRoute('home');
                }
                return ['form' => $form];
            }
        );
    }
}
