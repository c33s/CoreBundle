<?php

use Symfony\Component\Process\Process;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * DeploymentTest.
 *
 * @author Michael Hirschler <michael.vhirsch@gmail.com>
 */
class DeploymentTest extends WebTestCase
{
    protected static $projectDir = null;
    
    public function testInitProjectDir()
    {
        $this->logProgress("############################################################\nStarting deployment test\n############################################################\n");
        $this->logProgress('Folder: ' . self::$projectDir);
        
        $this->logProgress('Copying composer.json');
        $composerFile = realpath(__DIR__ . '/../../Resources/files/composer-example.json');
        $projectComposerFile = self::$projectDir . '/composer.json';
        copy($composerFile, $projectComposerFile);
        
        if (false !== getenv('SYMFONY_VERSION'))
        {
            $this->logProgress('Setting symfony version to ' . getenv('SYMFONY_VERSION'));
            $content = file_get_contents($projectComposerFile);
            $content = str_replace('"symfony/framework-standard-edition": "2.3.*"', '"symfony/framework-standard-edition": "'.getenv('SYMFONY_VERSION').'"', $content);
            file_put_contents($projectComposerFile, $content);
        }
        
        $this->assertFileExists($projectComposerFile);
        
        $this->logProgress('Copying parameters.yml');
        $parametersFile = realpath(__DIR__ . '/Files/parameters.yml');
        mkdir(self::$projectDir.'/app/config', 0777, true);
        $projectParametersFile = self::$projectDir . '/app/config/parameters.yml';
        copy($parametersFile, $projectParametersFile);
        
        $this->assertFileExists($projectParametersFile);
    }
    
    /**
     * @depends testInitProjectDir
     */
    public function testInstallComposer()
    {
        $composer = $this->findComposerExecutable();
        if (!$composer)
        {
            $this->fail('Unable to find a composer executable!');
        }
        $this->logProgress('Found composer: ' . $composer);
        
        $this->logProgress('Running: ' . $composer . ' install --no-scripts --prefer-source');
        $composerUpdateProcess = new Process($composer . ' install --no-scripts --prefer-source', self::$projectDir);
        $composerUpdateProcess->setTimeout(1800);
        $composerUpdateProcess->run();
        $this->logProgress($composerUpdateProcess->getOutput());
        $this->assertSame(0, $composerUpdateProcess->getExitCode(), $composerUpdateProcess->getOutput());
    }
    
    /**
     * @depends testInstallComposer
     */
    public function testInitSymfony()
    {
        $php = $this->findPhpExecutable();
        if (!$php)
        {
            $this->fail('Unable to find a php executable!');
        }
        $this->logProgress('Found PHP: ' . $php);
        
        $this->logProgress('Running: ' . $php . ' bin/init-symfony run CustomNamespace');
        $initSymfonyProcess = new Process($php  . ' bin/init-symfony run CustomNamespace', self::$projectDir);
        $initSymfonyProcess->setTimeout(600);
        $initSymfonyProcess->run();
        $this->logProgress($initSymfonyProcess->getOutput());
        $this->assertSame(0, $initSymfonyProcess->getExitCode(), $initSymfonyProcess->getOutput());
        
        $composer = $this->findComposerExecutable();
        if (!$composer)
        {
            $this->fail('Unable to find a composer executable!');
        }
        $this->logProgress('Found composer: ' . $composer);
        
        $this->logProgress('Running: ' . $composer . ' run-script post-update-cmd');
        $composerPostUpdateProcess = new Process($composer . ' run-script post-update-cmd', self::$projectDir);
        $composerPostUpdateProcess->setTimeout(60);
        $composerPostUpdateProcess->run();
        $this->logProgress($composerPostUpdateProcess->getOutput());
        $this->assertSame(0, $composerPostUpdateProcess->getExitCode(), $composerPostUpdateProcess->getOutput());
    }
    
    /**
     * @depends testInitSymfony
     */
    public function testInitConfig()
    {
        $php = $this->findPhpExecutable();
        if (!$php)
        {
            $this->fail('Unable to find a php executable!');
        }
        $this->logProgress('Found PHP: ' . $php);
        
        $this->logProgress('Running: ' . $php . ' app/console c33s:init-config CustomNamespace');
        $initConfigProcess = new Process($php  . ' app/console c33s:init-config CustomNamespace', self::$projectDir);
        $initConfigProcess->setTimeout(600);
        $initConfigProcess->run();
        $this->logProgress($initConfigProcess->getOutput());
        $this->assertSame(0, $initConfigProcess->getExitCode(), $initConfigProcess->getOutput());
    }
    
    /**
     * @depends testInitConfig
     */
    public function testInitCms()
    {
        $php = $this->findPhpExecutable();
        if (!$php)
        {
            $this->fail('Unable to find a php executable!');
        }
        $this->logProgress('Found PHP: ' . $php);
        
        $this->logProgress('Running: ' . $php . ' app/console c33s:init-cms CustomNamespace');
        $initCmsProcess = new Process($php  . ' app/console c33s:init-cms CustomNamespace', self::$projectDir);
        $initCmsProcess->setTimeout(600);
        $initCmsProcess->run();
        $this->logProgress($initCmsProcess->getOutput());
        $this->assertSame(0, $initCmsProcess->getExitCode(), $initCmsProcess->getOutput());
    }
    
    /**
     * @depends testInitCms
     */
    public function testBuildAdmin()
    {
        $php = $this->findPhpExecutable();
        if (!$php)
        {
            $this->fail('Unable to find a php executable!');
        }
        $this->logProgress('Found PHP: ' . $php);
        
        $this->logProgress('Running: ' . $php . ' app/console admin:c33s:build CustomNamespace');
        $adminBuildProcess = new Process($php  . ' app/console admin:c33s:build CustomNamespace', self::$projectDir);
        $adminBuildProcess->setTimeout(600);
        $adminBuildProcess->run();
        $this->logProgress($adminBuildProcess->getOutput());
        $this->assertSame(0, $adminBuildProcess->getExitCode(), $adminBuildProcess->getOutput());
    }
    
    /**
     * @depends testBuildAdmin
     */
    public function testClean()
    {
        $php = $this->findPhpExecutable();
        if (!$php)
        {
            $this->fail('Unable to find a php executable!');
        }
        $this->logProgress('Found PHP: ' . $php);
        
        $this->logProgress('Running: ' . $php . ' app/console c33s:clean');
        $cleanProcess = new Process($php  . ' app/console c33s:clean', self::$projectDir);
        $cleanProcess->setTimeout(600);
        $cleanProcess->run();
        $this->logProgress($cleanProcess->getOutput());
        $this->assertSame(0, $cleanProcess->getExitCode(), $cleanProcess->getOutput());
        
        $this->logProgress("############################################################\nDeployment successful\n############################################################\n");
    }
    
    /**
     * @depends testClean
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
            array('/', 'h2:contains("<i class="fa fa-star-o"></i> Example Dot Com")'),
            array('/', 'h2:contains("<i class="glyphicon glyphicon-star-empty"></i> Glyphicons work, too!")'),
        );
    }
    
    protected function findComposerExecutable()
    {
        $executableFinder = new Symfony\Component\Process\ExecutableFinder();
        $composer = $executableFinder->find('composer.phar');
        
        if (null === $composer)
        {
            $composer = $executableFinder->find('composer');
        }
        
        return null === $composer ? false : $composer;
    }

    protected function findPhpExecutable()
    {
        $executableFinder = new Symfony\Component\Process\ExecutableFinder();
        $php = $executableFinder->find('php');
        
        return null === $php ? false : $php;
    }

    public static function setUpBeforeClass()
    {
        self::$projectDir = tempnam(sys_get_temp_dir(), 'c33sCoreBundleTest');
        //self::$projectDir = sys_get_temp_dir().'/c33sCoreBundleTest';
        if (file_exists(self::$projectDir) && is_file(self::$projectDir))
        {
            unlink(self::$projectDir);
        }
        
        mkdir(self::$projectDir);
        
        // required for WebTestCase to find the directory
        $_SERVER['KERNEL_DIR'] = self::$projectDir.'/app';
    }

    public static function tearDownAfterClass()
    {
        self::delTree(self::$projectDir);
    }

    public static function delTree($dir)
    {
        if (empty($dir) || !strpos($dir, 'c33s'))
        {
            return;
        }
        
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file)
        {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        
        return rmdir($dir);
    }
    
    protected function logProgress($content)
    {
        $date = date('c').' ';
        $emptyDate = str_repeat(' ', strlen($date));
        $content = $date . str_replace("\n", "\n" . $emptyDate, $content) . "\n";
        file_put_contents(__DIR__.'/../../deployment.log', $content, FILE_APPEND);
    }
}
