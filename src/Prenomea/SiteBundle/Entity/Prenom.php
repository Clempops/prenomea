<?php

namespace Prenomea\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prenom
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Prenomea\SiteBundle\Entity\PrenomRepository")
 */
class Prenom
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="anne", type="integer")
     */
    private $anne;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var integer
     *
     * @ORM\Column(name="nombre", type="integer")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="sexe", type="string", length=255)
     */
    private $sexe;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set anne
     *
     * @param integer $anne
     * @return Prenom
     */
    public function setAnne($anne)
    {
        $this->anne = $anne;
    
        return $this;
    }

    /**
     * Get anne
     *
     * @return integer 
     */
    public function getAnne()
    {
        return $this->anne;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    
        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nombre
     *
     * @param integer $nombre
     * @return Prenom
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return integer 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     * @return Prenom
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;
    
        return $this;
    }

    /**
     * Get sexe
     *
     * @return string 
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Prenom
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
