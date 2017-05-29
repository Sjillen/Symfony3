<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
	//Dans l'argument de la method load, l'objet $manager est l'EntityManager
	public function load(ObjectManager $manager)
	{
		// Liste des noms de categorie a ajouter
		$names = array(
	      'Développement web',
	      'Développement mobile',
	      'Graphisme',
	      'Intégration',
	      'Réseau'
	    );

	    foreach ($names as $name) {
	    	// On cree la categorie
	    	$category = new Category();
	    	$category->setName($name);

	    	//On la persiste
	    	$manager->persist($category);
	    }

	    // ON declenche l'enregistrement de toutes les categories
	    $manager->flush();
	}
}