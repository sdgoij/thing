<?php
namespace thing\authz;

use Doctrine\ORM\EntityManager;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;

class AuthAdapter extends AbstractAdapter {
	/**
	 * @var EntityManager $em
	 */
	private $em;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return Result
	 */
	public function authenticate() {
		$username = $this->getIdentity();
		$user = $this->em->getRepository('thing\entity\User')
			->findOneBy(['username' => $username]);
		$result = new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $username);
		if ($user instanceof \thing\entity\User) {
			$result = (!$user->isValidPassword($this->getCredential()))
				? new Result(Result::FAILURE_CREDENTIAL_INVALID, $username)
				: new Result(Result::SUCCESS, $user);
		}
		return $result;
	}
}
