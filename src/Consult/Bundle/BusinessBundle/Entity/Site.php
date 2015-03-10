<?php

namespace Consult\Bundle\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="site")
 * @UniqueEntity("title")
 * @UniqueEntity("url")
 */
class Site
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
     * @ORM\Column(type="string", length=38)
     */
    private $guid;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Поле не может быть пустым")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Поле не может быть пустым")
     * @Assert\Url(message="Неверный адрес")
     */
    private $url;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sites")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\Column(name="allow_mark", type="boolean", nullable=true)
     */
    private $allowMark;
    
    /**
     * @ORM\Column(name="offline_email", type="string", length=60, nullable=true)
     * @Assert\Email(
     *     message = "'{{ value }}' неверный e-mail адрес",
     *     checkMX = true
     * )
     */
    private $offlineEmail;
    
    /**
     * @var string
     *
     * @ORM\Column(name="offline_text", type="text", nullable=true)
     */
    private $offlineText;
    
    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="site", cascade={"all"})
     */
    protected $messages;
    
    /**
     * @ORM\OneToMany(targetEntity="OfflineMessage", mappedBy="site", cascade={"all"})
     */
    protected $offlineMessages;
    
    /**
     * @ORM\OneToMany(targetEntity="Department", mappedBy="site", cascade={"all"})
     */
    protected $departments;
    
    /**
     * @ORM\OneToMany(targetEntity="Client", mappedBy="site", cascade={"all"})
     */
    protected $clients;
    
    /**
     * @ORM\OneToMany(targetEntity="Line", mappedBy="site", cascade={"all"})
     */
    protected $lines;
    
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->offlineMessages = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->lines = new ArrayCollection();
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
     * @return Site
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
     * Set url
     *
     * @param string $url
     * @return Site
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Site
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
     * Set user
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\User $user
     * @return Site
     */
    public function setUser(\Consult\Bundle\BusinessBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Consult\Bundle\BusinessBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set allowMark
     *
     * @param boolean $allowMark
     * @return Site
     */
    public function setAllowMark($allowMark)
    {
        $this->allowMark = $allowMark;

        return $this;
    }

    /**
     * Get allowMark
     *
     * @return boolean 
     */
    public function getAllowMark()
    {
        return $this->allowMark;
    }

    /**
     * Set offlineEmail
     *
     * @param string $offlineEmail
     * @return Site
     */
    public function setOfflineEmail($offlineEmail)
    {
        $this->offlineEmail = $offlineEmail;

        return $this;
    }

    /**
     * Get offlineEmail
     *
     * @return string 
     */
    public function getOfflineEmail()
    {
        return $this->offlineEmail;
    }

    /**
     * Set offlineText
     *
     * @param string $offlineText
     * @return Site
     */
    public function setOfflineText($offlineText)
    {
        $this->offlineText = $offlineText;

        return $this;
    }

    /**
     * Get offlineText
     *
     * @return string 
     */
    public function getOfflineText()
    {
        return $this->offlineText;
    }

    /**
     * Set guid
     *
     * @param string $guid
     * @return Site
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Get guid
     *
     * @return string 
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Add messages
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Message $messages
     * @return Site
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
     * @return Site
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
     * Add departments
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Department $departments
     * @return Site
     */
    public function addDepartment(\Consult\Bundle\BusinessBundle\Entity\Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Department $departments
     */
    public function removeDepartment(\Consult\Bundle\BusinessBundle\Entity\Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Add lines
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Line $lines
     * @return Site
     */
    public function addLine(\Consult\Bundle\BusinessBundle\Entity\Line $lines)
    {
        $this->lines[] = $lines;

        return $this;
    }

    /**
     * Remove lines
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Line $lines
     */
    public function removeLine(\Consult\Bundle\BusinessBundle\Entity\Line $lines)
    {
        $this->lines->removeElement($lines);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Add clients
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Client $clients
     * @return Site
     */
    public function addClient(\Consult\Bundle\BusinessBundle\Entity\Client $clients)
    {
        $this->clients[] = $clients;

        return $this;
    }

    /**
     * Remove clients
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Client $clients
     */
    public function removeClient(\Consult\Bundle\BusinessBundle\Entity\Client $clients)
    {
        $this->clients->removeElement($clients);
    }

    /**
     * Get clients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClients()
    {
        return $this->clients;
    }
}
