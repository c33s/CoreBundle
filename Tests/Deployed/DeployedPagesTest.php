<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * DeploymentTest.
 *
 * @author Michael Hirschler <michael.vhirsch@gmail.com>
 */
class DeployedPagesTest extends WebTestCase
{
    protected static $projectDir = null;
    
    /**
     *
     * @dataProvider  staticPagesProvider
     */
    public function testCallPage($url, $elementToCheck)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        $this->assertTrue($crawler->filter($elementToCheck)->count() > 0);
    }
    
    public function staticPagesProvider()
    {
        return array(
            array('/', 'h2:contains("Example Dot Com")'),
            array('/', 'h2:contains("Glyphicons work, too!")'),
        );
    }
    
    /**
     * Creates a Kernel.
     *
     * Available options:
     *
     *  * environment
     *  * debug
     *
     * @param array $options An array of options
     *
     * @return KernelInterface A KernelInterface instance
     */
    protected static function createKernel(array $options = array())
    {
        require_once self::$projectDir.'/app/AppKernel.php';
        
        return new AppKernel(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }
    
    public static function setUpBeforeClass()
    {
        self::$projectDir = trim(file_get_contents(__DIR__.'/../../.deployed_project_dir'));
    }
}
