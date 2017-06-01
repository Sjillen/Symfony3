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
    if ($page < 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // Ici je fixe le nombre d'annonces par page à 3
    // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
    $nbPerPage = 3;

    // On récupère notre objet Paginator
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts) / $nbPerPage);

    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page,
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

    //On ne sait toujours pas gerer le formulaire

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

   if ($request->isMethod('POST')) {
    $request->getSessions()->getFlashBag()->add('notice', 'Annonce biem modifiee.');
   }

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

	public function menuAction($limit)
	{
		$em = $this->getDoctrine()->getManager();

    $listAdverts = $em->getREpository('OCPlatformBundle:Advert')->findBy(
      array(),                  //Pas de critere
      array('date' => 'desc'),  //On trie par date decroissante
      $limit,                   //On selectionne $limit annonces
      0                         //A partir du premier fichier
    );                        

		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
			//Tout l'interet est ici: le controleur passe
			//les variables necessaires au template !
			'listAdverts' => $listAdverts
			));
	}
}