<?php
namespace thing\controller;

use thing\form\LinkForm;
use thing\paginator\adapter\EntityRepository;
use Doctrine\Common\Collections\Criteria;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class LinkController extends AbstractActionController {

	public function listAction() {
		$pg = new Paginator(new EntityRepository(
			$this->getEntityManager()->getRepository('thing\entity\Link'),
			Criteria::create()->orderBy(['created' => Criteria::DESC])));
		$pg->setCurrentPageNumber((int)$this->params()->fromRoute('p', 1));
		$pg->setDefaultItemCountPerPage(30);
		return ['pg' => $pg];
	}

	public function submitAction() {
		$auth = $this->getServiceLocator()->get('auth');
		if ($auth->hasIdentity()) {
			$em = $this->getEntityManager();
			$user = $em->merge($auth->getIdentity());
			$req = $this->getRequest();
			$form = new LinkForm();

			if ($req->isPost() && $form->setData($req->getPost())->isValid()) {
				$link = new \thing\entity\Link();
				$link->setTitle($form->get('title')->getValue());
				$link->setUrl($form->get('url')->getValue());
				$link->setCreated(new \DateTime());
				$link->setPoster($user);

				$em->persist($link);
				$em->flush();

				return $this->redirect()->toRoute('home');
			}

			return ['form' => $form];
		}
		$vm = new ViewModel();
		$vm->setTemplate('error/unauthorized.phtml');
		return $vm;
	}

	public function gotoAction() {
		$id = (int)$this->params()->fromRoute('id');
		$vm = new ViewModel(['id' => $id]);
		try {
			$link = $this->getEntityManager()->find(
				'thing\entity\Link', $id);
			if ($link !== null) {
				return $this->redirect()->toUrl(
					$link->getUrl());
			}
		} catch (\Doctrine\DBAL\DBALException $_) {
		};
		$vm->setTemplate('thing/link/error.phtml');
		return $vm;
	}

	private function getEntityManager() {
		return $this->getServiceLocator()
			->get('Doctrine\ORM\EntityManager');
	}
}
