<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    public function indexAction()
    {
	    // Notre liste d'annonce en dur
	    $listAdverts = array(
	      array(
	        'title'   => 'Recherche développpeur Symfony',
	        'id'      => 1,
	        'author'  => 'Alexandre',
	        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
	        'date'    => new \Datetime()),
	      array(
	        'title'   => 'Mission de webmaster',
	        'id'      => 2,
	        'author'  => 'Hugo',
	        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
	        'date'    => new \Datetime()),
	      array(
	        'title'   => 'Offre de stage webdesigner',
	        'id'      => 3,
	        'author'  => 'Mathieu',
	        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
	        'date'    => new \Datetime())
	    );

	    // Et modifiez le 2nd argument pour injecter notre liste
	    return $this->render('OCCoreBundle::index.html.twig', array(
	      'listAdverts' => $listAdverts
	    ));
	  

        return $this->render('OCCoreBundle::index.html.twig');
    }

    public function contactAction(Request $request)
    {
    	
	    	$request->getSession()->getFlashbag()->add('notice', 'La page Contact n\'est pas encore disponible. Merci de revenir plus tard.');
	    	return $this->redirectToRoute('oc_core_homepage');
		
    }
}


