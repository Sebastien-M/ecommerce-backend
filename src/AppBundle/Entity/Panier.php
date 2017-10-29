<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Panier
 *
 * @ORM\Table(name="panier")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PanierRepository")
 */
class Panier {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="articles", type="array")
     */
    private $articles;


    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set articles
     *
     * @param array $articles
     *
     * @return Panier
     */
    public function setArticles($articles) {
        $this->articles = $articles;

        return $this;
    }

    /**
     * Get articles
     *
     * @return array
     */
    public function getArticles() {
        return $this->articles;
    }


}
