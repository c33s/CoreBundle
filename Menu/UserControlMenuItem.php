<?php

namespace C33s\CoreBundle\Menu;

use FOS\UserBundle\Propel\UserQuery;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use C33s\MenuBundle\Item\MenuItem;

/**
 * @author david
 *
 * Special MenuItem for single page layouts, where the menu selection is javascript-based
 */
class UserControlMenuItem extends MenuItem
{
    protected $userParams = array(
        'enabled' => true,
        'has_switched' => false,
        'allow_switch' => false,
        'original_user' => null,
        'current_user' => null,
        'use_bootstrap2' => false,
    );

    protected function initOptions()
    {
        $this->initUserOptions();

        parent::initOptions();
    }

    protected function initUserOptions()
    {
        if (!$this->getContainer()->isScopeActive('request') || null === $this->getSecurityContext()->getToken() || (!$this->isSecurityGranted('IS_AUTHENTICATED_REMEMBERED') && !$this->isSecurityGranted('IS_AUTHENTICATED_FULLY')))
        {
            $this->userParams['enabled'] = false;
            $this->options['title'] = '';

            return;
        }

        if ($this->hasOption('use_bootstrap2'))
        {
            $this->userParams['use_bootstrap2'] = (boolean) $this->getOption('use_bootstrap2');
        }

        $this->options['title'] = $this->getSecurityContext()->getToken()->getUsername();

        if (!$this->hasOption('item_template'))
        {
            $this->options['item_template'] = $this->getUserItemTemplate();
        }
        if (!$this->hasOption('bootstrap_icon'))
        {
            $this->options['bootstrap_icon'] = $this->isUsingBootstrap2() ? 'icon icon-user' : 'fa fa-user';
        }

        if ($this->isSecurityGranted('ROLE_ALLOWED_TO_SWITCH') || $this->isSecurityGranted('ROLE_PREVIOUS_ADMIN'))
        {
            $this->userParams['original_user'] = $this->getSecurityContext()->getToken();
            $this->userParams['current_user'] = $this->getSecurityContext()->getToken();

            foreach ($this->userParams['current_user']->getRoles() as $role)
            {
                if ($role instanceof SwitchUserRole)
                {
                    $this->userParams['original_user'] = $role->getSource();
                    $this->userParams['has_switched'] = true;

                    break;
                }
            }
        }
    }

    protected function getUserItemTemplate()
    {
        return 'C33sCoreBundle:Menu:bootstrap_user_control_item_renderer.html.twig';
    }

    protected function generateChildren()
    {
        parent::generateChildren();

        $this->generateUserControlChildren();
    }

    protected function generateUserControlChildren()
    {
        if (!$this->userParams['enabled'])
        {
            return;
        }

        $this->addChildByData('fos_user_security_logout', array(
            'title' => 'Logout',
            'bootstrap_icon' => $this->isUsingBootstrap2() ? 'icon icon-off' : 'fa fa-power-off',
            'item_class' => 'C33s\\MenuBundle\\Item\\MenuItem',
        ));

        $this->addChildByData('fos_user_change_password', array(
            'title' => 'Change password',
            'bootstrap_icon' => $this->isUsingBootstrap2() ? 'icon icon-lock' : 'fa fa-lock',
            'item_class' => 'C33s\\MenuBundle\\Item\\MenuItem',
        ));

        $currentUrl = $this->getRequest()->getUri();
        $users = UserQuery::create()->orderByUsername()->find();

        if ($this->userParams['has_switched'])
        {
            $this->addChildByData('fos_user_security_logout', array(
                'title' => 'Exit back to user '.$this->userParams['original_user']->getUsername(),
                'custom_url' => $currentUrl,
                'custom_url_icon' => null,
                'set_request_variables' => array('_switch_user' => '_exit'),
                'bootstrap_icon' => $this->isUsingBootstrap2() ? 'icon icon-remove' : 'fa fa-times',
                'item_class' => 'C33s\\MenuBundle\\Item\\MenuItem',
            ));

            return;
        }

        if (!$this->isSecurityGranted('ROLE_ALLOWED_TO_SWITCH'))
        {
            return;
        }

        $switchItem = $this->addChildByData('fos_user_security_logout', array(
            'title' => 'Switch user',
            'custom_url' => $currentUrl,
            'custom_url_icon' => null,
            'bootstrap_icon' => $this->isUsingBootstrap2() ? 'icon icon-group' : 'fa fa-users',
            'item_class' => 'C33s\\MenuBundle\\Item\\MenuItem',
        ));

        $username = $this->userParams['current_user']->getUsername();
        foreach ($users as $user)
        {
            if ($user->getUsername() == $username)
            {
                continue;
            }

            $switchItem->addChildByData('fos_user_security_logout', array(
                'title' => $user->getUsername(),
                'set_request_variables' => array('_switch_user' => $user->getUsername()),
                'bootstrap_icon' => $this->isUsingBootstrap2() ? 'icon icon-user' : 'fa fa-user',
                'custom_url' => $currentUrl,
                'custom_url_icon' => null,
                'item_class' => 'C33s\\MenuBundle\\Item\\MenuItem',
            ));
        }
    }

    public function isEnabled()
    {
        return $this->userParams['enabled'] && parent::isEnabled();
    }

    public function isUsingBootstrap2()
    {
        return $this->userParams['use_bootstrap2'];
    }
}
