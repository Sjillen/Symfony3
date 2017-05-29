<?php

namespace OC\PlatformBundle\Email;

use OC\PlatformeBundle\Entity\Application;

class ApplicationMailer
{
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	public function __construct(\Swift_Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	public function sendNewNotification(Application $application)
	{
		$message = new \Swift_Message(
			'Nouvelle candidature',
			'Vous avew recu une nouvelle candidature.'
		);

		$message
			->addTo($application->getAdvert()->getAuthor()) //Ici bien sur il faudrait un attribut "email", j'utilise "author" a la place
			->addFrom('admin@votresite.com')
		;

		$this->mailer->send($message);
	}
}