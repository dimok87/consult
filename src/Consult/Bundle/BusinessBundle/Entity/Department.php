<?php

namespace Consult\Bundle\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="department")
 */
class Department
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Поле не может быть пустым")
     */
    private $title;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;
    
    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="departments", cascade={"persist"})
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;
    
    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="departments", cascade={"persist"})
     *
     */
    private $consultants;
    
    public function __construct()
    {
        $this->consultants = new ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     * @return Department
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Department
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set site
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Site $site
     * @return Department
     */
    public function setSite(\Consult\Bundle\BusinessBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \Consult\Bundle\BusinessBundle\Entity\Site 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Add consultants
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\User $consultants
     * @return Department
     */
    public function addConsultant(\Consult\Bundle\BusinessBundle\Entity\User $consultants)
    {
        $this->consultants[] = $consultants;

        return $this;
    }

    /**
     * Remove consultants
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\User $consultants
     */
    public function removeConsultant(\Consult\Bundle\BusinessBundle\Entity\User $consultants)
    {
        $this->consultants->removeElement($consultants);
    }

    /**
     * Get consultants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConsultants()
    {
        return $this->consultants;
    }
}
