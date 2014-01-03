<?php

namespace Prenomea\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Etymology
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Prenomea\SiteBundle\Entity\EtymologyRepository")
 */
class Etymology
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="etymology", type="text")
     */
    private $etymology;


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
     * Set name
     *
     * @param string $name
     * @return Etymology
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set etymology
     *
     * @param string $etymology
     * @return Etymology
     */
    public function setEtymology($etymology)
    {
        $this->etymology = $etymology;
    
        return $this;
    }

    /**
     * Get etymology
     *
     * @return string 
     */
    public function getEtymology()
    {
        return $this->etymology;
    }
}
