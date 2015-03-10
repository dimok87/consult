<?php

namespace Consult\Bundle\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 */
class Message
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
    private $message;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messages")
     * @ORM\JoinColumn(name="consultant_id", referencedColumnName="id")
     */
    private $consultant;
    
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="messages")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="messages")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;
    
    /**
     * @ORM\Column(name="is_to_client", type="boolean")
     */
    private $isToClient;

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
     * Set message
     *
     * @param string $message
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Message
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
     * Set consultant
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\User $consultant
     * @return Message
     */
    public function setConsultant(\Consult\Bundle\BusinessBundle\Entity\User $consultant = null)
    {
        $this->consultant = $consultant;

        return $this;
    }

    /**
     * Get consultant
     *
     * @return \Consult\Bundle\BusinessBundle\Entity\User 
     */
    public function getConsultant()
    {
        return $this->consultant;
    }

    /**
     * Set site
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Site $site
     * @return Message
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
     * Set isToClient
     *
     * @param boolean $isToClient
     * @return Message
     */
    public function setIsToClient($isToClient)
    {
        $this->isToClient = $isToClient;

        return $this;
    }

    /**
     * Get isToClient
     *
     * @return boolean 
     */
    public function getIsToClient()
    {
        return $this->isToClient;
    }

    /**
     * Set client
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Client $client
     * @return Message
     */
    public function setClient(\Consult\Bundle\BusinessBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Consult\Bundle\BusinessBundle\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }
}
