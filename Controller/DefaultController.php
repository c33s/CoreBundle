<?php

namespace c33s\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('c33sCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
