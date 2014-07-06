<?php

use Symfony\Component\Process\Process;

/**
 * DeploymentTest.
 *
 * @author Michael Hirschler <michael.vhirsch@gmail.com>
 */
class DeploymentTest extends \PHPUnit_Framework_TestCase
{

    protected static $projectDir = null;

    public function testDeployAProject()
    {
        // copy composer.json to $projectDir
        $composerYamlFile = realpath(__DIR__ . '/../../Resources/files/composer-example.json');
        $projectComposerYamlFile = self::$projectDir . '/composer.json';
        copy($composerYamlFile, $projectComposerYamlFile);

        $composer = $this->findComposerExecutable();
        if (!$composer) {
            $this->fail('Unable to find a composer executable!');
        }

        $composerUpdateProcess = new Process($composer . ' update --no-scripts', self::$projectDir);
        $composerUpdateProcess->setTimeout(600);
        $composerUpdateProcess->run();

        var_dump(self::$projectDir);
        $this->assertSame(0, $composerUpdateProcess->getExitCode(), $composerUpdateProcess->getOutput());

//        $convertProcess = new Process(__DIR__ . '/../../vendor/bin/composer-yaml convert', $projectDir);
//        $convertProcess->run();
//        $this->assertSame(0, $convertProcess->getExitCode());
//        var_dump($convertProcess->getErrorOutput()); die();
//composer update --no-scripts
//./bin/init-symfony run YourNamespace
//composer run-script post-update-cmd
    }

    protected function findComposerExecutable()
    {
        $executableFinder = new Symfony\Component\Process\ExecutableFinder();
        $composer = $executableFinder->find('composer.phar');

        if (null === $composer) {
            $composer = $executableFinder->find('composer');
        }

        return null === $composer ? false : $composer;
    }

    public static function setUpBeforeClass()
    {
        self::$projectDir = tempnam(sys_get_temp_dir(), 'c33sProject');
        if (file_exists(self::$projectDir)) {
            unlink(self::$projectDir);
        }

        mkdir(self::$projectDir);
    }

    public static function tearDownAfterClass()
    {
        self::delTree(self::$projectDir);
    }

    public static function delTree($dir)
    {
        if (empty($dir) || !strpos($dir, 'c33s')) {
            return;
        }

        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }
}
