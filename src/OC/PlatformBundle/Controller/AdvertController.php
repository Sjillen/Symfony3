<?php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\Entity\AdvertSkill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AdvertController extends Controller
{
public function indexAction($page)
  {
    // ...

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
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }

public function viewAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    //On recupere l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    //On recupere la liste des candidatures de cette annonce
    $listApplications = $em 
      ->getRepository('OCPlatformBundle:Application')
      ->findBy(array('advert' => $advert))
    ;

    $listAdvertSkills = $em
        ->getRepository('OCPlatformBundle:AdvertSkill')
        ->findBy(array('advert' => $advert))
    ;

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert'           => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills
      ));
  }

	public function addAction(Request $request)
	{
        //On recupere l'EntityManager
    $em = $this->getDoctrine()->getManager();

    // Creation de l'entite
    $advert = new Advert();
    $advert->setTitle('Recherche developpeur Symfony.');
    $advert->setAuthor('Alexandre');
    $advert->setContent("Nous recherchons un developpeur Symfony debutant sur Lyon. Blabla...");
    //On ne peut pas definir ni la date ni la publication,
    // car ces attributs sont definis automatiquement dans le constructeur

    // Creation de l'entite Image
    $image = new Image();
    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
    $image->setAlt('Job de reve');

    //On lie l'image a l'annonce
    $advert->setImage($image);

    //Creation de la premier candidature
    $application1 = new Application();
    $application1->setAuthor('Marine');
    $application1->setContent("J'ai toutes les qualités requises.");

    //Creation d'une deuxieme candidature par exemple
    $application2 = new Application();
    $application2->setAuthor('Pierre');
    $application2->setContent("Je suis très motivé.");

    //On lie les candidature a l'annonce
    $application1->setAdvert($advert);
    $application2->setAdvert($advert);

    $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();

    foreach ($listSkills as $skill) {
      $advertSkill = new AdvertSkill();
      $advertSkill->setAdvert($advert);
      $advertSkill->setSkill($skill);

      $advertSkill->setLevel('Expert');

      $em->persist($advertSkill);
    }



    // Etape 1: On "persiste" l'Entite
    $em->persist($advert);

    //Etape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
    // definie dans l'entite Application et non Advert. On doit donc tout persister a la main ici
    $em->persist($application1);
    $em->persist($application2);

    //Etape 2: on "flush" tout ce qui a ete persiste avant
    $em->flush();

    //Reste de la methode qu'on avait deja ecrite
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      // Pui on redirige verts la page de visualisation de cette annonce
      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getid()));
    }

    //Si ce n'est pas en POST, alors on affiche le formulaire
    return $this->render('OCPlatformBundle:Advert:add.html.twig', array('advert' => $advert));
	}

  public function editAction($id, Request $request)
  {
   $em = $this->getDoctrine()->getManager();

   //On recupere l'annonce $id
   $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

   if ( null === $advert) {
    throw new NotFoundHttpException("L'annonce d'id".$id." n'existe pas.");
   }

   //La methode findAll retourn toutes les categories de la bdd
   $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

   // On boucle sur les categories pour les lier a l'annonce
   foreach ( $listCategories as $category) {
    $advert->addCategory($category);
   }

   $em->flush();

    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert
    ));
  }

	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

    //On recupere l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if ( null === $advert ) {
      throw new HttpNotFoundException("L'annonce d'id".$id." n'existe pas.");
    }

    //On boucle sur les categories de l'annonce pour les suppimer
    foreach ($advert->getCategories() as $category) {
      $advert->removeCategory($category);
    }

    $em->flush();

    return $this->render('OCPlatformBundle:Advert:delete.html.twig');
	}

	public function menuAction()
	{
		//On fixe en dur une liste ici, bien entendu par la suite 
		//on la recuperera en BDD !
		$listAdverts = array(
			array('id' => 2, 'title' => 'Recherche Developpeur Symfony'),
			array('id' => 5, 'title' => 'Mission de webmaster'),
			array('id' => 9, 'title' => 'Offre de stage webdeisgner')
			);

		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
			//Tout l'interet est ici: le controleur pass
			//les variables necessaires au template !
			'listAdverts' => $listAdverts
			));
	}
}