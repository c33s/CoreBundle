<?php


namespace c33s\CoreBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

//https://stackoverflow.com/questions/13957652/login-in-controller-with-fosuserbundle

class SecurityHelper
{
    protected $firewall = 'main';
    protected $securityContext;
    protected $eventDispatcher;
    protected $request;
    protected $router;
    //protected $

    public function __construct($securityContext, $eventDispatcher, $request, $router, $logger)
    {
        $this->securityContext = $securityContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->request         = $request;
        $this->router          = $router;
        $this->logger          = $logger;
    }

    public function setFirewall($firewall)
    {
        $this->firewall = $firewall;
    }
    public function getFirewall()
    {
        return $this->firewall;
    }

    //https://stackoverflow.com/questions/14512664/how-to-programmatically-authenticate-a-user
    public function autoLogin($user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $this->firewall, $user->getRoles());
        $this->securityContext->setToken($token);

        $event = new InteractiveLoginEvent($this->request, $token);
        $this->eventDispatcher->dispatch('security.interactive_login', $event);
    }

    public function redirectRole($roleRrouteSets, $defaultUrl = '/', $status = 302)
    {
//        $roles = $this->securityContext->getToken()->getRoles();
//        $roles_ = '';
//        $this->logger->debug('c33s.security_helper.redirect_role: Required Role for redirect: "'.$role.'".');
//        $this->logger->debug('c33s.security_helper.redirect_role: User Roles: "'.$roles_.'".');

        $url = $defaultUrl;

        foreach ($roleRrouteSets as $role => $route)
        {
            if ($this->securityContext->isGranted($role))
            {
                $url = $this->router->generate($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH);
    //            $this->logger->debug('c33s.security_helper.redirect_role: Redirecting to "'.$route.'"');
                return new RedirectResponse($url, $status);
            }
        }

        return new RedirectResponse($url, $status);
            //'IS_AUTHENTICATED_REMEMBERED'
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
//        $this->logger->debug('c33s.security_helper.redirect_role: No redirect.');
    }
    // UPSF2.4 Prepeare for upgrade prepeare for symfony 2.4 upgrade
//    public function setRequest(RequestStack $request_stack)
//    {
//        $this->request = $request_stack->getCurrentRequest();
//    }
}