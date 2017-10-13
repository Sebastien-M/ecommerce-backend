<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AcmeAssert;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UtilisateurRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="unique_user_submission", columns={"username", "email"})})
 */
class Utilisateur extends BaseUser {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @Assert\NotBlank()
     * 
     */
    protected $username;

    /**
     * @Assert\NotBlank()
     */
    protected $plainPassword;

    /**
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @ORM\OneToMany(targetEntity="Panier", mappedBy="user")
     */
    private $panier;


    /**
     * Add panier
     *
     * @param \AppBundle\Entity\Panier $panier
     *
     * @return Utilisateur
     */
    public function addPanier(\AppBundle\Entity\Panier $panier)
    {
        $this->panier[] = $panier;

        return $this;
    }

    /**
     * Remove panier
     *
     * @param \AppBundle\Entity\Panier $panier
     */
    public function removePanier(\AppBundle\Entity\Panier $panier)
    {
        $this->panier->removeElement($panier);
    }

    /**
     * Get panier
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPanier()
    {
        return $this->panier;
    }
}
