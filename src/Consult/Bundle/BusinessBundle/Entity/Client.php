<?php

namespace Consult\Bundle\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="client")
 */
class Client
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
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $name;
    
    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="clients", cascade={"persist"})
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=15)
     */
    private $ip;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $country;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $city;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $provider;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $os;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $browser;
    
    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="client")
     */
    protected $messages;
    
    /**
     * @ORM\OneToMany(targetEntity="OfflineMessage", mappedBy="client")
     */
    protected $offlineMessages;
    
    /**
     * @ORM\OneToMany(targetEntity="Visit", mappedBy="client")
     */
    protected $visits;
    
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->offlineMessages = new ArrayCollection();
        $this->visits = new ArrayCollection();
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
     * Set ip
     *
     * @param string $ip
     * @return Client
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Client
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
     * Set country
     *
     * @param string $country
     * @return Client
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Client
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set provider
     *
     * @param string $provider
     * @return Client
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string 
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set os
     *
     * @param string $os
     * @return Client
     */
    public function setOs($os)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * Get os
     *
     * @return string 
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * Set browser
     *
     * @param string $browser
     * @return Client
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;

        return $this;
    }

    /**
     * Get browser
     *
     * @return string 
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Add messages
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Message $messages
     * @return Client
     */
    public function addMessage(\Consult\Bundle\BusinessBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Message $messages
     */
    public function removeMessage(\Consult\Bundle\BusinessBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add offlineMessages
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\OfflineMessage $offlineMessages
     * @return Client
     */
    public function addOfflineMessage(\Consult\Bundle\BusinessBundle\Entity\OfflineMessage $offlineMessages)
    {
        $this->offlineMessages[] = $offlineMessages;

        return $this;
    }

    /**
     * Remove offlineMessages
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\OfflineMessage $offlineMessages
     */
    public function removeOfflineMessage(\Consult\Bundle\BusinessBundle\Entity\OfflineMessage $offlineMessages)
    {
        $this->offlineMessages->removeElement($offlineMessages);
    }

    /**
     * Get offlineMessages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOfflineMessages()
    {
        return $this->offlineMessages;
    }

    /**
     * Set site
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Site $site
     * @return Client
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
     * Set name
     *
     * @param string $name
     * @return Client
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
     * Add visits
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Visit $visits
     * @return Client
     */
    public function addVisit(\Consult\Bundle\BusinessBundle\Entity\Visit $visits)
    {
        $this->visits[] = $visits;

        return $this;
    }

    /**
     * Remove visits
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Visit $visits
     */
    public function removeVisit(\Consult\Bundle\BusinessBundle\Entity\Visit $visits)
    {
        $this->visits->removeElement($visits);
    }

    /**
     * Get visits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVisits()
    {
        return $this->visits;
    }
}
