<?php

namespace Consult\Bundle\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements UserInterface, \Serializable, EquatableInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Поле не может быть пустым")
     */
    private $username;
    
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    private $passwordConfirmation;
    
    /**
     * @Assert\NotBlank(message="Поле не может быть пустым")
     * @Assert\Length(
     *      min = 6,
     *      max = 50,
     *      minMessage = "Пароль должен быть не менее {{ limit }} символов",
     *      maxMessage = "Пароль должен быть не более {{ limit }} символов"
     * )
     */
    private $plainPassword;
    
    /**
     * Password encoder
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @ORM\Column(type="string", length=60, unique=true, nullable=true)
     * @Assert\Email(
     *     message = "'{{ value }}' неверный e-mail адрес",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Поле не может быть пустым")
     */
    private $name;
    
    /**
     * @ORM\Column(name="is_online", type="boolean", nullable=true)
     */
    private $isOnline;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $position;
    
    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $lastLogin;
    
    /**
     * @ORM\Column(type="float")
     */
    private $balance;
    
    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="consultants", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="parent")
     */
    protected $consultants;
    
    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="consultant")
     */
    protected $messages;
    
    /**
     * @ORM\OneToMany(targetEntity="Site", mappedBy="user")
     */
    protected $sites;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $avatar;
    
    /**
     * @ORM\ManyToMany(targetEntity="Department", inversedBy="consultants", cascade={"persist"})
     *
     */
    private $departments;
    
    /**
     * @ORM\ManyToMany(targetEntity="Line", inversedBy="consultants", cascade={"persist"})
     *
     */
    private $lines;
    
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->sites = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->consultants = new ArrayCollection();
        $this->lines = new ArrayCollection();
    }
    
    public function getRoles()
    {
        return array($this->role);
    }
    
    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    public function isEqualTo(UserInterface $user) {
       return false;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->getUsername(),
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * @param \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $passwordEncoder
     */
    public function setPasswordEncoder(PasswordEncoderInterface $passwordEncoder = null)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    public function getPasswordEncoder()
    {
        return $this->passwordEncoder;
    }

    /**
     * @param mixed $passwordConfirmation
     */
    public function setPasswordConfirmation($passwordConfirmation)
    {
        $this->passwordConfirmation = $passwordConfirmation;
    }

    /**
     * @return mixed
     */
    public function getPasswordConfirmation()
    {
        return $this->passwordConfirmation;
    }

    /**
     * {@inheritDoc}
     */
    public function isModified()
    {
        if ($this->getPlainPassword()) {
            return true;
        }

        return parent::isModified();
    }

    /**
     * Encode plain-text password using encoder
     */
    public function encodePassword()
    {
        if ($this->getPasswordEncoder() && $this->getPlainPassword()) {
            $this->setSalt(md5(uniqid(null, true)));
            $password = $this->getPasswordEncoder()->encodePassword($this->getPlainPassword(), $this->getSalt());
            $this->setPassword($password);
        }
    }

    public function generatePassword()
    {
        $characters = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z'), array('@', '$', '%', '*', '!', '?'));
        $password = '';
        for ($i = 1; $i <= 8; $i++) {
            $password .= $characters[array_rand($characters)];
        }
        $this->setPlainPassword($password);
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = trim($plainPassword);
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
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
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set balance
     *
     * @param float $balance
     * @return User
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return float 
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return User
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/images/' . $this->username;
    }
    
    /**
     * Sets avatar.
     *
     * @param UploadedFile $avatar
     */
    public function setAvatar(UploadedFile $avatar = null)
    {
        $this->avatar = $avatar;
    }

    /**
     * Get avatar.
     *
     * @return UploadedFile
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
    
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getAvatar()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getAvatar()->move(
            $this->getUploadRootDir(),
            $this->getAvatar()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getAvatar()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->avatar = null;
   }
   
   /**
     * Set path
     *
     * @param string $path
     * @return User
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set role
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Role $role
     * @return User
     */
    public function setRole(\Consult\Bundle\BusinessBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Consult\Bundle\BusinessBundle\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set parent
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\User $parent
     * @return User
     */
    public function setParent(\Consult\Bundle\BusinessBundle\Entity\User $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Consult\Bundle\BusinessBundle\Entity\User 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add consultants
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\User $consultants
     * @return User
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

    /**
     * Add sites
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Site $sites
     * @return User
     */
    public function addSite(\Consult\Bundle\BusinessBundle\Entity\Site $sites)
    {
        $this->sites[] = $sites;

        return $this;
    }

    /**
     * Remove sites
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Site $sites
     */
    public function removeSite(\Consult\Bundle\BusinessBundle\Entity\Site $sites)
    {
        $this->sites->removeElement($sites);
    }

    /**
     * Get sites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * Add messages
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Message $messages
     * @return User
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
     * Set isOnline
     *
     * @param boolean $isOnline
     * @return User
     */
    public function setIsOnline($isOnline)
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    /**
     * Get isOnline
     *
     * @return boolean 
     */
    public function getIsOnline()
    {
        return $this->isOnline;
    }

    /**
     * Add departments
     *
     * @param \Consult\Bundle\BusinessBundle\Entity\Department $departments
     * @return User
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
     * @return User
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
}
