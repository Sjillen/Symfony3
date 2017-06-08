<?php

namespace OC\PlatformBundle\BigBrother;

use Symfony\Component\Security\Core\User\UserInterface;

class MessageNotificator
{
	protected $mailer;

	public function __construct(\Swift_Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	// Methode pour notifier par e-mail un administrateur
	public function notifyByEmail($message, UserInterface $user)
	{
		$message = \Swift_Message::newInstance()
			->setSubject("Nouveau message d'un utilisateur surveille")
			->setFrom('admin@votresite.com')
			->setTo('admin@votresite.com')
			->setBody("L'utilisateur surveille '".$user->getUsername()."' a poste le message suivant '".$message. "'")
		;

		$this->mailer->send($message);
	}
}