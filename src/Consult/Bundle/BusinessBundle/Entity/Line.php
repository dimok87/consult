<?php

namespace Consult\Bundle\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="line")
 */
class Line
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;
    
    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="lines", cascade={"persist"})
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;
    
    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="lines", cascade={"persist"})
     *
     */
    private $consultants;
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $sort;
    
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
     * Set created
     *
     * @param \DateTime $created
     * @return Line
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
     * Set sort
     *
     * @param integer $sort
     * @return Line
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set site
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Site $site
     * @return Line
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
     * @return Line
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
