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
        if (!$this->getServiceLocator()->get('auth')->hasIdentity()) {
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
        $this->redirect()->toRoute('home');
        return $this->getResponse();
    }

    public function loginAction() {
        $auth = $this->getServiceLocator()->get('auth');
        if (!$auth->hasIdentity()) {
            return $this->post(new LoginForm(),
                function(Form $form) use ($auth) {
                    $data = $form->getData();
                    $result = $auth->getAdapter()
                        ->setCredential($data['password'])
                        ->setIdentity($data['username'])
                        ->authenticate();
                    if ($result->isValid()) {
                        $auth->getStorage()->write($result->getIdentity());
                        $this->redirect()->toRoute('home');
                    }
                    return [
                        'errmsg' => 'Invalid username or password',
                        'form' => $form,
                    ];
                }
            );
        }
        $this->redirect()->toRoute('home');
        return $this->getResponse();
    }

    public function logoutAction() {
        $auth = $this->getServiceLocator()->get('auth');
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
        }
        $this->redirect()->toRoute('home');
        return $this->getResponse();
    }
}
