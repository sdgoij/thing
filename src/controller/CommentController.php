<?php
namespace thing\controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use thing\entity\Comment;
use thing\entity\Post;

final class CommentController extends BaseController {
	/**
	 * @param $id
	 * @return Response
	 */
	public function discussion($id) {
		/** @var Post $post */
		if (!$post = $this['orm.em']->find(Post::class, $id)) {
			return $this->render('not-found.twig', ['id' => $id]);
		}
		return $this->render('discussion.twig', [
			'form' => $this->form(['post' => $id], ['action' => $this->path('post_reply')])->getForm()->createView(),
			'threads' => $post->getCommentThreads(),
			'post' => $post,
		]);
	}

	/**
	 * @param Request $request
	 * @param int|null $id
	 * @param int|null $parent
	 * @return Response
	 */
	public function reply(Request $request, $id = null, $parent = null) {
		$form = $this->form(null, ['action' => $this->path('post_reply')])->getForm();
		$form->handleRequest($request);
		/**
		 * @var int $id
		 * @var EntityManagerInterface $em
		 */
		$id = $id ?: $form->get('post')->getData();
		$em = $this->getEntityManager();

		/** @var Post $post */
		if (!$post = $em->find(Post::class, $id)) {
			return $this->render('not-found', ['post' => $id]);
		}

		if ($form->isValid()) {
			if ($parent = $parent ?: $form->get('parent')->getData()) {
				/** @var Comment|null $parent */
				$parent = $em->find(Comment::class, $parent);
			}
			$comment = new Comment();
			$comment->setCreated(new \DateTime());
			$comment->setMessage($form->get('message')->getData());
			$comment->setUser($this['user']->getEntity());
			$post->addComment($comment, $parent);
			$em->persist($comment);
			$em->flush();

			return new RedirectResponse($this->path('discussion', ['id' => $id]));
		}

		$form->get('parent')->setData($parent);
		$form->get('post')->setData($id);

		return $this->render('reply.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * Creates and returns a form builder instance.
	 *
	 * @param mixed $data The initial data for the form
	 * @param array $options Options for the form
	 *
	 * @return FormBuilder
	 */
	public function form($data = null, array $options = []) {
		return parent::form($data, $options)
			->add('post', 'hidden')
			->add('parent', 'hidden')
			->add('message', 'textarea', ['label' => ''])
			->add('submit', 'submit', ['label' => 'Add comment']);
	}
}
