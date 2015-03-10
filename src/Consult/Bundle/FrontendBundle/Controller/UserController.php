<?php

namespace Consult\Bundle\FrontendBundle\Controller;

use Consult\Bundle\FrontendBundle\Component\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContextInterface;;
use Consult\Bundle\BusinessBundle\Entity\User;
use Consult\Bundle\BusinessBundle\Entity\Line;
use Consult\Bundle\BusinessBundle\Entity\Client;
use Consult\Bundle\BusinessBundle\Entity\Visit;
use Consult\Bundle\BusinessBundle\Entity\Site;
use Consult\Bundle\BusinessBundle\Entity\Department;
use Consult\Bundle\BusinessBundle\Entity\Message;
use Consult\Bundle\BusinessBundle\Entity\OfflineMessage;
use Consult\Bundle\BusinessBundle\Entity\Recover;
use Consult\Bundle\FrontendBundle\Form\RecoverType;
use Consult\Bundle\FrontendBundle\Form\RegistrationType;
use Consult\Bundle\FrontendBundle\Form\ConsultantType;
use Consult\Bundle\FrontendBundle\Form\MessageType;
use Consult\Bundle\FrontendBundle\Form\OfflineMessageType;
use Consult\Bundle\FrontendBundle\Form\EditConsultantType;
use Consult\Bundle\FrontendBundle\Form\EditClientType;
use Consult\Bundle\FrontendBundle\Form\PasswordType;
use Consult\Bundle\FrontendBundle\Form\PersonalType;
use Consult\Bundle\FrontendBundle\Form\UserpicType;
use Consult\Bundle\FrontendBundle\Form\SiteType;
use Consult\Bundle\FrontendBundle\Form\DepartmentType;
use Consult\Bundle\FrontendBundle\Form\EditSiteType;
use Symfony\Component\Form\FormError;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
   /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if (!is_null($this->getUser())) {
            throw $this->createNotFoundException('Вы уже в системе');
        }
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return array(
            'last_username' => $lastUsername,
            'target' => 'login',
            'error' => $error,
            'page' => 'login',
        );
    }
    
    /**
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction()
    {
       /*require_once(
          $this->container->getParameter( 'kernel.root_dir' )
        . '/../src/Consult/Bundle/BusinessBundle/XMPPHP/XMPP.php'
       );
         $conn = new \XMPPHP_XMPP('macbook-pro-dmitrij-3.local', 5222, 'dimok', 'd180787', 'xmpphp', '127.0.0.1', $printlog=true, $loglevel=\XMPPHP_Log::LEVEL_INFO);
         //$conn = new XMPPHP_BOSH('server.tld', 5280, 'user', 'password', 'xmpphp', 'server.tld', $printlog=true, $loglevel=XMPPHP_Log::LEVEL_INFO);
       

         try {
             $conn->connect();
             $conn->processUntil('session_start');
             $conn->presence();
             $conn->message('site@macbook-pro-dmitrij-3.local', 'This is a test message!');
             $conn->disconnect();
         } catch(XMPPHP_Exception $e) {
             error_log($e->getMessage());
         }
         echo(11);exit();*/
        return array('page' => 'index',);
    }
    
    /**
     * @Route("/consult", name="consult")
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function consultAction()
    {
        if (!$this->getUser()->getIsOnline()) {
           $this->getUser()->setIsOnline(true);
        }
        return array('page' => 'consult',);
    }
    
    /**
     * @Route("/login_check", name="security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
    
    /**
     * @Route("/recover", name="recover")
     * @Template()
     */
    public function recoverAction(Request $request)
    {
        if (!is_null($this->getUser())) {
            throw $this->createNotFoundException('Вы уже в системе');
        }
        $form = $this->createForm(new RecoverType());
        if ($request->isMethod('POST')) {
           $form->handleRequest($request);
           if ($form->isValid()) {
                $repository = $this->getDoctrine()->getRepository('ConsultBusinessBundle:User');
                $recover = $form->getData();
                $user = $repository->findBy(
                    array('email' => $recover->getEmail())
                );
                if (count($user) > 0) {
                   $user = $user[0]; 
                   // TODO send email with user data
                   $this->get('session')->getFlashBag()->add('notice', 'Новый пароль успешно отправлен на ваш e-mail'); 
                } else {
                   $form->get('email')->addError(new FormError('Пользователь с таким e-mail не найден'));
                }
           }
           
        }
        return array(
            'form' => $form->createView(),
            'page' => 'recover',
        );
    }
    
    /**
     * @Route("/registration", name="registration")
     * @Template()
     */
    public function registrationAction(Request $request)
    {
        $form = $this->createForm(new RegistrationType());
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            /** @var User $user */
            $user = $form->getData();
            if ($user->getEmail() == '') {
               $form->get('email')->addError(new FormError('Поле не может быть пустым'));
            }
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user->setPasswordEncoder($this->getPasswordEncoder($user));
                $user->encodePassword();
                $user->setCreatedAt(new \DateTime());
                $user->setLastLogin(new \DateTime());
                $user->setBalance(0);
                $user->setRole($this->getRole('ROLE_ADMIN'));
                $em->persist($user);
                $em->flush();
                /*$message = \Swift_Message::newInstance()
                  ->setSubject('Hello Email')
                  ->setFrom('dofon77@gmail.com')
                  ->setTo($user->getEmail())
                  ->setBody(
                      $this->renderView(
                           'FrontendBundle:Email:registration.txt.twig',
                           array('user' => $user)
                       )
                );
                $this->get('mailer')->send($message);
                 */
                $this->authenticateUser($user);
                return $this->redirect($this->generateUrl('index'));
            }
        }

        return array(
            'form' => $form->createView(),
            'page' => 'registration',
        );
    }
    
    /**
     * @Route("/settings", name="settings")
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function settingsAction(Request $request)
    {
        $user = $this->getUser();
        $user->generatePassword();
        $personalForm = $this->createForm(new PersonalType(), $user);
        $userpicForm = $this->createForm(new UserpicType(), $user);
        $passwordForm = $this->createForm(new PasswordType(), $user);
        if ($request->getMethod() == 'POST') {
            if ($request->request->has('personal')) {
                $personalForm->handleRequest($request);
                if ($personalForm->isValid()) {
                   $em = $this->getDoctrine()->getManager();
                   $em->flush();
                   return $this->redirect($this->generateUrl('settings'));
                }
            }

            if (!empty($_FILES)) {
                $userpicForm->handleRequest($request);
                if ($userpicForm->isValid()) {
                   $em = $this->getDoctrine()->getManager();
                   if (!empty($_FILES)) {
                      $user->upload();
                   }
                   $em->flush();
                   return $this->redirect($this->generateUrl('settings'));
                }
            }

            if ($request->request->has('password')) {
                $passwordForm->handleRequest($request);
                if ($passwordForm->isValid()) {
                   $em = $this->getDoctrine()->getManager();
                   $user->setPasswordEncoder($this->getPasswordEncoder($user));
                   $user->encodePassword();
                   $em->flush();
                   return $this->redirect($this->generateUrl('settings'));
                }
            }
        }
        
        return array(
            'user' => $user,
            'page' => 'settings',
            'personalForm' => $personalForm->createView(),
            'userpicForm' => $userpicForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        );
    }
    
    /**
     * @Route("/site", name="addsite")
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function siteAction(Request $request)
    {
        $form = $this->createForm(new SiteType());
        if ($request->isMethod('POST')) {
           $form->handleRequest($request);
           if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                /** @var User $user */
                $site = $form->getData();
                $site->setCreated(new \DateTime());
                $site->setUser($this->getUser());
                $site->setGuid($this->guid());
                //creating distribution line for created site on each user
                $line = new Line();
                $line->setCreated(new \DateTime());
                $line->setSite($site);
                $line->addConsultant($this->getUser());
                $line->setSort(0);
                $em->persist($line);
                $repository = $this->getDoctrine()->getRepository('ConsultBusinessBundle:User');
                $users = $repository->findBy(array('parent' => $this->getUser()));
                for ($i = 0; $i < count($users); $i++) {
                   $line = new Line();
                   $line->setCreated(new \DateTime());
                   $line->setSite($site);
                   $line->addConsultant($users[$i]);
                   $line->setSort($i+1);
                   $em->persist($line);
                }
                $em->persist($site);
                $em->flush();
                return $this->redirect($this->generateUrl('sites'));
            }
           
        }
        return array(
            'form' => $form->createView(),
            'page' => 'site',
        );
    }
    
    /**
     * @Route("/sites", name="sites")
     * @Template()
     * @Security("is_granted('ROLE_USER')")
     */
    public function sitesAction()
    {
        return array('sites' => $this->getUser()->getSites(), 'page' => 'site',);
    }
    
    /**
     * @Route("/site/{id}", name="editsite", requirements={"id" = "\d+"})
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function editsiteAction(Site $site, Request $request)
    {
        if ($site->getUser() != $this->getUser()) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
        }
        $form = $this->createForm(new EditSiteType(), $site);
        if ($request->isMethod('POST')) {
           $form->handleRequest($request);
           if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirect($this->generateUrl('sites'));
            }
           
        }
        $em = $this->getDoctrine()->getManager();
        $lines = $em->getRepository('ConsultBusinessBundle:Line')->findBy(array('site' => $site), array('sort' => 'ASC'));    
        return array(
            'form' => $form->createView(),
            'page' => 'site',
            'site' => $site,
            'lines' => $lines,
        );
    }
    
    /**
     * @Route("/reorder/{id}", name="reorder", requirements={"id" = "\d+"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function reorderAction(Site $site, Request $request)
    {
        if ($site->getUser() != $this->getUser()) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
        }
        $sort = $request->get('line');
        for ($i = 0; $i < count($sort); $i++) {
           // if sort order changed
           if (intval($i) !== intval($sort[$i])) {
               $em = $this->getDoctrine()->getManager();
               $line = $em->getRepository('ConsultBusinessBundle:Line')->findOneBy(array('sort' => $sort[$i], 'site' => $site));
               if ($line) {
                  $line->setSort($i);
               }
           }
        }
        $em->flush();
        return new Response(json_encode(0)); 
    }
    
    /**
     * @Route("/site/delete/{id}", name="deletesite", requirements={"id" = "\d+"})
     * @Security("has_role('ROLE_USER')")
    */
    public function deletesiteAction(Site $site, Request $request)
    {
       if ($site->getUser() != $this->getUser()) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
        }
       $em = $this->getDoctrine()->getManager();
       $em->remove($site);
       $em->flush();
       return $this->redirect($this->generateUrl('sites'));
    }
    
    /**
     * @Route("/site/{id}/department", name="department", requirements={"id" = "\d+"})
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function departmentAction(Site $site, Request $request)
    {
        if ($site->getUser() != $this->getUser()) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
        }
        $form = $this->createForm(new DepartmentType($site->getUser()->getId()));
        if ($request->isMethod('POST')) {
           $form->handleRequest($request);
           if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                /** @var User $user */
                $department = $form->getData();
                $department->setCreated(new \DateTime());
                $department->setSite($site);
                $em->persist($department);
                $em->flush();
                return $this->redirect($this->generateUrl('editsite', array('id' => $site->getId())));
            }
           
        }
        return array(
            'form' => $form->createView(),
            'page' => 'site',
        );
    }
    
    /**
     * @Route("/department/{id}", name="editdepartment", requirements={"id" = "\d+"})
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function editdepartmentAction(Department $department, Request $request)
    {
        if ($department->getSite()->getUser() != $this->getUser()) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
        }
        $form = $this->createForm(new DepartmentType($department->getSite()->getUser()->getId()), $department);
        if ($request->isMethod('POST')) {
           $form->handleRequest($request);
           if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirect($this->generateUrl('editsite', array('id' => $department->getSite()->getId())));
            }
           
        }
        return array(
            'form' => $form->createView(),
            'page' => 'site',
        );
    }
    
    /**
     * @Route("/department/delete/{id}", name="deletedepartment", requirements={"id" = "\d+"})
     * @Security("has_role('ROLE_USER')")
    */
    public function deletedepartmentAction(Department $department, Request $request)
    {
       if ($department->getSite()->getUser() != $this->getUser()) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
        }
       $em = $this->getDoctrine()->getManager();
       $em->remove($department);
       $em->flush();
       return $this->redirect($this->generateUrl('editsite', array('id' => $department->getSite()->getId())));
    }
    
    /**
     * @Route("/consultant", name="addconsultant")
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function consultantAction(Request $request)
    {
        $form = $this->createForm(new ConsultantType());
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                /** @var User $user */
                $user = $form->getData();
                $user->setPasswordEncoder($this->getPasswordEncoder($user));
                $user->encodePassword();
                $user->setCreatedAt(new \DateTime());
                $user->setLastLogin(new \DateTime());
                $user->setBalance(0);
                $user->setRole($this->getRole('ROLE_CONSULTANT'));
                $user->setParent($this->getUser());
                
                //creating distribution line for created consultant on each site
                $repository = $this->getDoctrine()->getRepository('ConsultBusinessBundle:Site');
                $sites = $repository->findBy(array('user' => $this->getUser()));
                for ($i = 0; $i < count($sites); $i++) {
                   // getting the last sort order number
                   $maxSortOrder = 0;
                   $lines = $sites[$i]->getLines();
                   for ($j = 0; $j < count($lines); $j++) {
                      if ($lines[$j]->getSort() > $maxSortOrder) {
                         $maxSortOrder = $lines[$j]->getSort();
                      }
                   }
                   $line = new Line();
                   $line->setCreated(new \DateTime());
                   $line->setSite($sites[$i]);
                   $line->addConsultant($user);
                   $line->setSort($maxSortOrder + 1);
                   $em->persist($line);
                }
                $em->persist($user);
                $em->flush();
                return $this->redirect($this->generateUrl('consultants'));
            }
        }

        return array(
            'form' => $form->createView(),
            'page' => 'consultant',
        );
    }
    
    /**
     * @Route("/consultant/{id}", name="editconsultant", requirements={"id" = "\d+"})
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function editconsultantAction(User $consultant, Request $request)
    {
        if ($consultant->getParent() != $this->getUser()) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
        }
        $consultant->generatePassword();
        $form = $this->createForm(new EditConsultantType(), $consultant);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                if (!empty($_FILES)) {
                    $consultant->upload();
                }
                $em->flush();
                return $this->redirect($this->generateUrl('consultants'));
            }
        }

        return array(
            'form' => $form->createView(),
            'page' => 'consultant',
        );
    }
    
    /**
     * @Route("/consultants", name="consultants")
     * @Template()
     * @Security("is_granted('ROLE_USER')")
     */
    public function consultantsAction()
    {
        return array('consultants' => $this->getUser()->getConsultants(), 'page' => 'consultant');
    }
    
    /**
     * @Route("/consultant/delete/{id}", name="deleteconsultant", requirements={"id" = "\d+"})
     * @Security("has_role('ROLE_USER')")
    */
    public function deleteconsultantAction(User $consultant, Request $request)
    {
       if ($consultant->getParent() != $this->getUser()) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
       }
       $em = $this->getDoctrine()->getManager();
       $em->remove($consultant);
       $em->flush();
       return $this->redirect($this->generateUrl('consultants'));
    }
    
    /**
      * @Route("/site/{guid}", name="client")
      */
    public function clientAction(Site $site, Request $request)
    {
        // checking if departments exist
        if (count($site->getDepartments()) > 0) {
           return $this->redirect($this->generateUrl('departmentchoose', array('guid' => $site->getGuid())));
        } else {
           return $this->redirect($this->generateUrl('message', array('guid' => $site->getGuid())));
        }
    }
    
    /**
      * @Route("/site/{guid}/departments", name="departmentchoose")
      * @Template()
      */
    public function departmentchooseAction(Site $site, Request $request)
    {
        return array('departments' => $site->getDepartments());
    }
    
    /**
      * @Route("/site/{guid}/message/{department}", name="message", requirements={"department" = "\d+"}, defaults={"department"=null})
      * @Template()
      */
    public function messageAction(Site $site, Request $request)
    {
        $session = $request->getSession();
        $client = $session->get('client');
        
        //$em = $this->getDoctrine()->getManager();
        //$client = $em->getRepository('ConsultBusinessBundle:Client')->find(14);
                
        if (!$client) {
           $client = new Client();
           $ip = $request->getClientIp();
           $ip = "185.6.25.107";
           $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
           if ($details) {
               $isp=$details->org;
               $city=$details->city;
               $country=$details->country;
               $client->setCountry($country);
               $client->setCity($city);
               $client->setProvider($isp);
           }
           $client->setCreated(new \DateTime());
           $client->setIp($ip);
           $ex=explode(' ',$_SERVER['HTTP_USER_AGENT']);
           $client->setBrowser($ex[0]);
           $client->setOs($ex[4].' '.$ex[5].' '.$ex[6]);
           $client->setSite($site);
           $em = $this->getDoctrine()->getManager();
           $em->persist($client);
           $em->flush();
           $session->set('client', $client);
        }
        
        $department = false;
        if ($request->get('department')) {
           $departmentId = $request->get('department');
           $repository = $this->getDoctrine()->getRepository('ConsultBusinessBundle:Department');
           $departments = $repository->findBy(array('id' => $departmentId, 'site' => $site));
           if ($departments) {
              $department = $departments[0];
           }
        }
        // checking if consultant is online
        $isOnline = false;
        $em = $this->getDoctrine()->getManager();
        $lines = $em->getRepository('ConsultBusinessBundle:Line')->findBy(array('site' => $site), array('sort' => 'ASC'));
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            $lineConsultants = $line->getConsultants();
            for ($j = 0; $j < count($lineConsultants); $j++) {
                $lineConsultant = $lineConsultants[$j];
                if ($department && !$department->getConsultants()->contains($lineConsultant)) {
                    continue;
                }
                if ($lineConsultant->getIsOnline()) {
                    $isOnline = true;
                    $siteConsultant = $lineConsultant;
                    break;
                }
            }
        }
        if ($isOnline) {
            /*$form = $this->createForm(new MessageType());
            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $message = $form->getData();
                    $message->setCreated(new \DateTime());
                    $message->setSite($site);
                    $message->setConsultant($siteConsultant);
                    $message->setClient($em->merge($client));
                    $message->setIsToClient(false);
                    $em->persist($message);
                    $em->flush();
                }
            }*/
           return array(
               'site' => $site,
               'consultant' => $siteConsultant,
               'isOnline' => $isOnline,
               'client' => $client,
           );
        } else {
            $form = $this->createForm(new OfflineMessageType());
            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    /** @var OfflineMessage $message */
                    $message = $form->getData();
                    $message->setCreated(new \DateTime());
                    $message->setSite($site);
                    $message->setClient($em->merge($client));
                    $em->persist($message);
                    $em->flush();
                }
            }
            return array(
               'form' => $form->createView(),
               'isOnline' => $isOnline
           );
        }

        
    }
    
    /**
      * @Route("/visit", name="visit")
      * @Template()
      */
    public function visitAction(Request $request)
    {
        $session = $request->getSession();
        $client = $session->get('client');
        if (!$client) {
           $client = new Client();
           $ip = $request->getClientIp();
           $ip = "185.6.25.107";
           $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
           if ($details) {
               $isp=$details->org;
               $city=$details->city;
               $country=$details->country;
               $client->setCountry($country);
               $client->setCity($city);
               $client->setProvider($isp);
           }
           $client->setCreated(new \DateTime());
           $client->setIp($ip);
           $ex=explode(' ',$_SERVER['HTTP_USER_AGENT']);
           $client->setBrowser($ex[0]);
           $client->setOs($ex[4].' '.$ex[5].' '.$ex[6]);
           $client->setSite($site);
           $em = $this->getDoctrine()->getManager();
           $em->persist($client);
           $em->flush();
           $session->set('client', $client);
        }
        $callback = $request->get('callback');
        $title = $request->get('title');
        $referer = $request->get('referer');
        
        if ($referer && $title) {
           $visit = new Visit();
           $visit->setCreated(new \DateTime());
           $visit->setTitle($title);
           $visit->setUrl($referer);
           $em = $this->getDoctrine()->getManager();
           $visit->setClient($em->merge($client));
           $em->persist($visit);
           $em->flush();
        }
        
        return new Response('consultsystemsVisit({vid:"", vcookie: ""})');
    }
    
    /**
      * @Route("/message/send", name="sendmessage")
      * @Security("is_granted('ROLE_USER')")
      */
    public function sendmessageAction(Request $request)
    {
        /** @var Message $message */
        $message = new Message();
        $message->setMessage($request->get('message'));
        $message->setCreated(new \DateTime());
        $message->setConsultant($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('ConsultBusinessBundle:Client')->find($request->get('client'));
        $message->setClient($client);
        $message->setSite($client->getSite());
        $message->setIsToClient(true);
        $em->persist($message);
        $em->flush();
        
        $messages = $client->getMessages();
        $messagesData = array();
        for ($i = 0; $i < count($messages); $i++) {
            $message = $messages[$i];
            $messagesData[] = array(
                'message' => $message->getMessage(),
                'created' => $message->getCreated()->format('d.m.Y H:i'),
                'isToClient' => $message->getIsToClient(),
            );
        }
        $clientData = array(
             'id' => $client->getId(),
             'messages' => $messagesData,
             'site' => $client->getSite()->getTitle(),
        );
        return new Response(json_encode($clientData));
    }
    
    /**
      * @Route("/consultations", name="consultations")
      * @Security("is_granted('ROLE_USER')")
      * @Template()
      */
    public function consultationsAction(Request $request)
    {
       $clientId = null;
       if (is_numeric($request->get('id'))) {
          $clientId = $request->get('id'); 
       }
       return array('page' => 'consultations', 'id' => $clientId);
    }
    
    /**
      * @Route("/clients", name="clients")
      * @Security("is_granted('ROLE_USER')")
      */
    public function сlientsAction()
    {
       $sites = $this->getUser()->getSites();
       $clients = array();
       for ($i = 0; $i < count($sites); $i++) {
          $site = $sites[$i];
          $siteClients = $site->getClients();
          for ($j = 0; $j < count($siteClients); $j++) {
             $client = $siteClients[$j];
             $messages = $client->getMessages();
             $messagesData = array();
             for ($k = 0; $k < count($messages); $k++) {
                $message = $messages[$k];
                $messagesData[] = array(
                    'message' => $message->getMessage(),
                    'created' => $message->getCreated()->format('d.m.Y H:i'),
                    'isToClient' => $message->getIsToClient(),
                );
             }
             $clientData = array(
                 'id' => $client->getId(),
                 'messages' => $messagesData,
                 'site' => $client->getSite()->getTitle(),
             );
             $clients[] = $clientData;
          }
       }
       return new Response(json_encode($clients));
    }
    
    /**
      * @Route("/visits/{id}", name="visits")
      * @Security("is_granted('ROLE_USER')")
      */
    public function visitsAction(Request $request, Client $client)
    {
       if (!$this->getUser()->getSites()->contains($client->getSite())) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
       }
       $visits = $client->getVisits();
       $pages = array();
       for ($i = 0; $i < count($visits); $i++) {
          $visit = $visits[$i];
          $visitData = array(
              'id' => $visit->getId(),
              'title' => $visit->getTitle(),
              'url' => $visit->getUrl(),
              'created' => $visit->getCreated()->format('d.m.Y H:i'),
          );
          $pages[] = $visitData;
       }
       return new Response(json_encode($pages));
    }
    
    /**
      * @Route("/visitors", name="visitors")
      * @Security("is_granted('ROLE_USER')")
      * @Template()
      */
    public function visitorsAction()
    {
       $sites = $this->getUser()->getSites();
       $clients = array();
       for ($i = 0; $i < count($sites); $i++) {
          $site = $sites[$i];
          $siteClients = $site->getClients();
          for ($j = 0; $j < count($siteClients); $j++) {
             $client = $siteClients[$j];
             $clients[] = $client;
          }
       }
       return array('page' => 'visitors', 'clients' => $clients);
    }
    
    /**
     * @Route("/client/{id}", name="viewclient", requirements={"id" = "\d+"})
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function viewclientAction(Client $client, Request $request)
    {
        if (!$this->getUser()->getSites()->contains($client->getSite())) {
           throw $this->createNotFoundException('Вы не можете просматривать данную страницу');
        }
        $form = $this->createForm(new EditClientType(), $client);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }
        }
        return array(
            'page' => 'visitors',
            'client' => $client,
            'form' => $form->createView(),
        );
    }
    
    /**
      * @Route("/site/{guid}/messages", name="messages")
      */
    public function messagesAction(Site $site, Request $request)
    {
         $em = $this->getDoctrine()->getManager();
         $client = $em->getRepository('ConsultBusinessBundle:Client')->find($request->get('client'));
         $messages = $client->getMessages();
         $messagesData = array();
         for ($i = 0; $i < count($messages); $i++) {
            $message = $messages[$i];
            $messagesData[] = array(
                'message' => $message->getMessage(),
                'created' => $message->getCreated()->format('d.m.Y H:i'),
                'isToClient' => $message->getIsToClient(),
            );
         }
         return new Response(json_encode($messagesData));
    }
    
    /**
      * @Route("site/{guid}/message/send", name="sendclientmessage")
      */
    public function sendclientmessageAction(Site $site, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('ConsultBusinessBundle:Client')->find($request->get('client'));
        $consultant = $em->getRepository('ConsultBusinessBundle:User')->find($request->get('consultant'));
        if ($client->getSite()->getId() != $site->getId() || (!$site->getUser()->getConsultants()->contains($consultant) && $site->getUser()->getId() != $consultant->getId())) {
           throw $this->createNotFoundException('Неверный клиент');
        }
        /** @var Message $message */
        $message = new Message();
        $message->setMessage($request->get('message'));
        $message->setCreated(new \DateTime());
        $message->setConsultant($consultant);
        $message->setClient($client);
        $message->setSite($site);
        $message->setIsToClient(false);
        $em->persist($message);
        $em->flush();
        
        $messages = $client->getMessages();
        $messagesData = array();
        for ($i = 0; $i < count($messages); $i++) {
            $message = $messages[$i];
            $messagesData[] = array(
                'message' => $message->getMessage(),
                'created' => $message->getCreated()->format('d.m.Y H:i'),
                'isToClient' => $message->getIsToClient(),
            );
        }
        return new Response(json_encode($messagesData));
    }
}
