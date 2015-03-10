<?php

namespace Consult\Bundle\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="offline_message")
 */
class OfflineMessage
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Поле не может быть пустым")
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;
    
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="Поле не может быть пустым")
     * @Assert\Email(
     *     message = "'{{ value }}' неверный e-mail адрес",
     *     checkMX = true
     * )
     */
    private $email;
    
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
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="offlineMessages")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="offlineMessages")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;

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
     * @return OfflineMessage
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
     * Set phone
     *
     * @param string $phone
     * @return OfflineMessage
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return OfflineMessage
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return OfflineMessage
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
     * @return OfflineMessage
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
     * Set client
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Client $client
     * @return OfflineMessage
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

    /**
     * Set site
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Site $site
     * @return OfflineMessage
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
}
