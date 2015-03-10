<?php
namespace Consult\Bundle\FrontendBundle\Listener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserLogin
{
   protected $doctrine;
   protected $container;
   public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine, $container )
   {
      $this->doctrine = $doctrine;
      $this->container = $container;
   }
   public function onLogin(InteractiveLoginEvent $event)
   {
      $entityManager = $this->doctrine->getManager();
      $user = $event->getAuthenticationToken()->getUser();;
      $user->setLastLogin(new \DateTime());
      $entityManager->flush();
   }
}
