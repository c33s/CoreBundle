<?php


namespace C33s\CoreBundle\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

//https://stackoverflow.com/questions/13957652/login-in-controller-with-fosuserbundle

class SecurityHelper
{
    protected $firewall = 'main';
    protected $securityContext;
    protected $eventDispatcher;
    protected $request;
    protected $router;

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
    public function autoLogin(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $this->firewall, $user->getRoles());
        $this->securityContext->setToken($token);

        $event = new InteractiveLoginEvent($this->request, $token);
        $this->eventDispatcher->dispatch('security.interactive_login', $event);
    }

    public function redirectRole($roleRrouteSets, $defaultUrl = '/', $status = 302)
    {
        $url = $defaultUrl;

        foreach ($roleRrouteSets as $role => $route)
        {
            if ($this->securityContext->isGranted($role))
            {
                $url = $this->router->generate($route, array(), UrlGeneratorInterface::ABSOLUTE_PATH);

                return new RedirectResponse($url, $status);
            }
        }

        return new RedirectResponse($url, $status);
    }
    // UPSF2.4 Prepeare for upgrade prepeare for symfony 2.4 upgrade
//    public function setRequest(RequestStack $request_stack)
//    {
//        $this->request = $request_stack->getCurrentRequest();
//    }
}
