<?php
namespace thing\controller;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use thing\entity\Post;
use thing\util\DoctrineEntityRepositorySelector;

final class PostController extends BaseController {
	/**
	 * @param $page
	 * @return Response
	 */
	public function index($page) {
		return $this->render('index.twig', [
			'posts' => $this['knp_paginator']->paginate(
				new DoctrineEntityRepositorySelector(
					$this->getEntityManager()->getRepository(Post::class),
					Criteria::create()->orderBy(['created' => Criteria::DESC])
				), $page, 30
			),
		]);
	}

	/**
	 * @param Request $req
	 * @return Response
	 */
	public function submit(Request $req) {
		$form = $this->form()
			->add('title', 'text', ['label' => 'Title'])
			->add('url', 'text', ['label' => 'URL'])
			->add('submit', 'submit', ['label' => 'Submit Post'])
			->getForm();
		$form->handleRequest($req);

		if ($form->isValid()) {
			$data = $form->getData();
			$post = new Post();
			$post->setPoster($this['user']->getEntity());
			$post->setTitle($data['title']);
			$post->setUrl($data['url']);
			$post->setCreated(new \DateTime());

			$em = $this->getEntityManager();
			$em->persist($post);
			$em->flush();

			return new RedirectResponse('/');
		}

		return $this->render('submit.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @param int $id
	 * @return Response
	 */
	public function r($id) {
		if (!$post = $this->getEntityManager()->find(Post::class, $id)) {
			return new Response("Post by ID '$post' not found", 404);
		}
		return new RedirectResponse($post->getUrl());
	}
}
