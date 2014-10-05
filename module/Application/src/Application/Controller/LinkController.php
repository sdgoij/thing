<?php
namespace Application\Controller;

use Application\Form\LinkForm;
use Doctrine\Common\Collections\Criteria;
use DoctrineModule\Paginator\Adapter\Selectable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class LinkController extends AbstractActionController {

    public function listAction() {
        $pg = new Paginator(new Selectable(
            $this->getEntityManager()->getRepository('Application\Entity\Link'),
            Criteria::create()->orderBy(['id' => Criteria::DESC])));
        $pg->setCurrentPageNumber((int)$this->params()->fromRoute('p', 1));
        $pg->setDefaultItemCountPerPage(30);
        return ['pg' => $pg];
    }

    public function submitAction() {
        $form = new LinkForm();
        $req = $this->getRequest();

        if ($req->isPost() && $form->setData($req->getPost())->isValid()) {
            $link = new \Application\Entity\Link();
            $link->setTitle($form->get('title')->getValue());
            $link->setUrl($form->get('url')->getValue());
            $link->setCreated(new \DateTime());

            $em = $this->getEntityManager();
            $em->persist($link);
            $em->flush();

            return $this->redirect()->toRoute('home');
        }

        return ['form' => $form];
    }

    public function gotoAction() {
        $id = (int)$this->params()->fromRoute('id');
        $vm = new ViewModel(['id' => $id]);
        try {
            $link = $this->getEntityManager()->find(
                'Application\Entity\Link', $id);
            if ($link !== null) {
                return $this->redirect()->toUrl(
                    $link->getUrl());
            }
        } catch(\Doctrine\DBAL\DBALException $_) {};
        $vm->setTemplate('application/link/error.phtml');
        return $vm;
    }

    private function getEntityManager() {
        return $this->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');
    }
}
