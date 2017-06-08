<?php

namespace OC\PlatformBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class MessagePostEvent extends Event
{
	protected $message;
	protected $user;

	public function __construct($message, UserInterface $user)
	{
		$this->message = $message;
		$this->user = $user;
	}

	// Le listener doit avoir acces au message
	public function getMessage()
	{
		return $this->message;
	}

	// Le listener doit pouvoir modifier le message
	public function setMessage($message)
	{
		return $this->message = $message;
	}

	// le listener doit avoir acces a l'utilisateur
	public function getUser()
	{
		return $this->user;
	}

	// Pas de setUset, les listeners ne peuvent pas modifier l'auteur du message !
}