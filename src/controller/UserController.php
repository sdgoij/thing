<?php
namespace thing\controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use thing\entity\User;

final class UserController extends BaseController {
	/**
	 * @param Request $request
	 * @return Response
	 */
	public function register(Request $request) {
		$form = $this->form()
			->add('username', 'text', ['label' => 'Username'])
			->add('email', 'text', ['label' => 'E-mail'])
			->add('password', 'password', ['label' => 'Password'])
			->add('password2', 'password', ['label' => 'Password (verify)'])
			->add('submit', 'submit', ['label' => 'Submit'])
			->getForm();
		$form->handleRequest($request);

		if ($form->isValid()) {
			/** @var PasswordEncoderInterface $encoder */
			$encoder = $this['security.encoder_factory']->getEncoder(UserInterface::class);
			$data = $form->getData();
			$user = new User();
			$user->setUsername($data['username']);
			$user->setPassword($encoder->encodePassword($data['password'], null));
			$user->setEmail($data['email']);
			$user->setCreated(new \DateTime());

			$this['orm.em']->persist($user);
			$this['orm.em']->flush();

			return new RedirectResponse('/');
		}

		return $this->render('register.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function login(Request $request) {
		return $this->render('login.twig', [
			'error' => $this['security.last_error']($request),
			'form' => $this['form.factory']->createNamedBuilder(null, 'form', null, ['action' => $this->path('login_check')])
				->add('_username', 'text', ['label' => 'Username', 'data' => $this['session']->get('_security.last_username')])
				->add('_password', 'password', ['label' => 'Password'])
				->add('submit', 'submit', ['label' => 'Login'])
				->getForm()->createView(),
		]);
	}
}
