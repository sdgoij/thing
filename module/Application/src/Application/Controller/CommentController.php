<?php
namespace Application\Controller;

use Application\Form\CommentForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CommentController extends AbstractActionController {
    public function discussionAction() {
        $link = $this->getEntityManager()->find(
            'Application\Entity\Link',
            $this->params()->fromRoute('link'));
        $vm = new ViewModel();
        if ($link === null) {
            $vm->setTemplate('application/link/error.phtml');
            $vm->id = $this->params()->fromRoute('link');
        } else {
            $form = new CommentForm();
            $form->get('link')->setValue($link->getId());
            $form->setAttribute('action', $this->url()->fromRoute(
                'reply', ['link' => $link->getId()]));
            $vm->form = $form;
            $vm->link = $link;
        }
        return $vm;
    }

    public function replyAction() {
        $auth = $this->getServiceLocator()->get('auth');
        if ($auth->hasIdentity()) {
            $em = $this->getEntityManager();
            $vm = new ViewModel([
                'header' => 'partial/link.phtml',
                'comment' => null,
                'link' => null,
            ]);

            $link = $em->find(
                'Application\Entity\Link',
                (int)$this->params()->fromRoute('link'));

            if ($link === null) {
                $vm->setTemplate('application/link/error.phtml');
                $vm->id = $this->params()->fromRoute('link');
                return $vm;
            }

            $req  = $this->getRequest();
            $form = new CommentForm();
            $form->setData(['link' => $link->getId()]);

            $parent = $this->findParentComment($link);
            if ($parent !== null) {
                $form->get('parent')->setValue($parent->getId());
                $vm->header = 'partial/comment.phtml';
                $vm->comment = $parent;
            }

            if ($req->isPost() && $form->setData($req->getPost())->isValid()) {
                $comm = new \Application\Entity\Comment();
                $comm->setMessage($form->get('message')->getValue());
                $comm->setCreated(new \DateTime());
                $comm->setUser($em->merge($auth->getIdentity()));
                $link->addComment($comm, $parent);

                $em->persist($comm);
                $em->flush();

                return $this->redirect()->toRoute('discussion', [
                    'link' => $link->getId(),
                ]);
            }
            $vm->form = $form;
            $vm->link = $link;
            return $vm;
        }
        $vm = new ViewModel();
        $vm->setTemplate('error/unauthorized.phtml');
        return $vm;
    }

    private function findParentComment(\Application\Entity\Link $link) {
        if ($parent = $this->params()->fromRoute('parent')) {
            $comm = $this->getEntityManager()->find(
                'Application\Entity\Comment', $parent);
            if ($comm && $comm->getLink() !== $link) {
                $comm = null;
            }
            return $comm;
        }
        return null;
    }

    private function getEntityManager() {
        return $this->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');
    }
}
