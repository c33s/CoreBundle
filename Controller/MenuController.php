<?php

namespace c33s\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use FOS\UserBundle\Propel\UserQuery;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

/**
 * @Route("/menu")
 * @Template()
 */
class MenuController extends Controller
{
    /**
     * @Route("/switchuser")
     * @Template()
     */
    public function switchUserAction()
    {
        $parameters = array();

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ALLOWED_TO_SWITCH') or $security->isGranted('ROLE_PREVIOUS_ADMIN'))
        {
            $parameters['users'] = UserQuery::create()->find();

            $parameters['original_user'] = $this->get('security.context')->getToken();
            $parameters['current_user'] = $this->get('security.context')->getToken();

            foreach ($security->getToken()->getRoles() as $role)
            {
                if ($role instanceof SwitchUserRole)
                {
                    $parameters['original_user'] = $role->getSource();
                    break;
                }
            }
        }

        return array('parameters' => $parameters);
    }
}
