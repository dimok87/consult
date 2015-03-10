<?php

namespace Consult\Bundle\FrontendBundle\Component;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Consult\Bundle\BusinessBundle\Entity\User;

class Controller extends BaseController{

    /**
     * Get password encoder for user
     *
     * @param  User $user
     * @return PasswordEncoderInterface
     */
    protected function getPasswordEncoder($user)
    {
        $factory = $this->get('security.encoder_factory');

        return $factory->getEncoder($user);
    }

    /**
     * Notify user
     * @param string $message
     */
    protected function notify($message)
    {
        $this->get('session')->getFlashBag()->add('notifications', $message);
    }

    /**
     * automatic log in user.
     *
     * @param User $user
     */
    public function authenticateUser($user)
    {
        $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
        $this->get('security.context')->setToken($token);
    }
    
    /**
     * generate GUID.
     *
     */
    public function guid()
    {
        if (function_exists('com_create_guid')){
            return com_create_guid();
        } else {
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                    .substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12)
                    .chr(125);// "}"
            return $uuid;
        }
    }
    
    protected function getRole( $role )
    {
        return $this->getDoctrine()->getRepository('ConsultBusinessBundle:ROLE')->findOneBy(array('role' => $role));
    }
} 