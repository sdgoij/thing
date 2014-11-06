<?php
namespace thing\controller;

use thing\entity\Comment;
use thing\entity\Link;
use thing\entity\User;

use Zend\Mvc\Controller\AbstractActionController;

class ImportController extends AbstractActionController {
	public function hnAction() {
		$request = $this->getRequest();
		$response = $this->getResponse();

		if ($request->getServer()->get('REMOTE_ADDR') != '127.0.0.1') {
			$this->redirect()->toRoute('home');
			return $response;
		}

		if (!$request->isPost()) {
			$response->setStatusCode(405);
			$response->setContent('Method Not Allowed');
			return $response;
		}

		try {
			$data = json_decode($request->getContent(), true);
			$link = $this->getEntityManager()->getRepository(
				'thing\entity\Link'
			)->findOneBy([
					'title' => $data['title'],
					'url' => $data['url'],
				]
			);
			if ($link) {
				$response->setStatusCode(409);
				$response->setContent('Duplicate Entry');
				return $response;
			}

			$resp = new \StdClass();
			$resp->linkId = 0;
			$resp->comments = [];
			$resp->users = [];
			$link = new Link();
			$link
				->setCreated(new \DateTime($data['created_at']))
				->setPoster($this->findUserByName($data['author'], $resp))
				->setTitle($data['title'])
				->setUrl($data['url']);
			foreach ((array)$data['children'] as $reply) {
				$link->addComment($this->thread($reply, $resp));
			}

			$em = $this->getEntityManager();
			$em->persist($link);
			$em->flush();

			$resp->linkId = $link->getId();
			$response->getHeaders()->addHeader(
				new \Zend\Http\Header\ContentType('application/json')
			);
			$response->setStatusCode(200);
			$response->setContent(json_encode($resp));
		} catch (\Exception $e) {
			$response->setStatusCode(500);
			$response->setContent($e->getMessage());
		}
		return $response;
	}

	private function getEntityManager() {
		return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
	}

	private function findUserByName($name, \StdClass $re) {
		if (!$user = @$this->users[$name]) {
			$em = $this->getEntityManager();
			$user = $em->getRepository('thing\entity\User')
				->findOneBy(['username' => $name]);
			if (!$user) {
				$user = new User();
				$user
					->setCreated(new \DateTime())
					->setUsername($name)
					->setPassword(microtime())
					->setEmail('');
				$em->persist($user);
			}
			$re->users[] = $user->getUsername();
			$this->users[$name] = $user;
		}

		return $user;
	}

	private $users = [];

	private function thread(array $reply, \StdClass $re) {
		$comm = new Comment();
		$comm
			->setCreated(new \DateTime($reply['created_at']))
			->setUser($this->findUserByName($reply['author'], $re))
			->setMessage($reply['text']);
		foreach ((array)$reply['children'] as $child) {
			$comm->addReply($this->thread($child, $re));
		}
		$this->getEntityManager()->persist($comm);
		$re->comments[] = $comm->getId();
		return $comm;
	}
}
