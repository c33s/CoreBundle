<?php
/*
 *  orginal code from jon at sitewizard dot ca http://php.net/phpinfo
 */
namespace C33s\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use C33s\CoreBundle\Tools\Phpinfo;

/**
 * @Route("/phpinfo")
 * @Template()
 */
class PhpinfoController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $phpinfo = new Phpinfo();

        return array('phpinfo' => $phpinfo->get());
    }
}
