<?php

namespace C33s\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;

use C33s\CoreBundle\Tools\Tools;
use C33s\CoreBundle\Command\BaseInitCmd as BaseInitCommand;

class InitConfigCommand extends BaseInitCommand
{
    protected function configure()
    {
        $this
            ->setName('c33s:init-config')
            ->setDescription('the task will init the projects config with the importing system')
            ->addArgument('name', InputArgument::REQUIRED, 'the Name of the Customer (used as Namespace Part)' )
            ->addOption(
               'force',
               null,
               InputOption::VALUE_NONE,
               'If set, the task will overwrite the existing config files'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->io->write('<info>c33s:init-config</info>');
        $this->rebuildBundles();
        $this->createSqlDataDirectory();
        $this->createPropelFixturesDirectory();
        $this->initTemplatesAndResources();
        $this->enableTranslationsInConfig();
    }

    protected function createPropelFixturesDirectory()
    {

        $propelFixturesDir = $this->getContainer()->get('kernel')->getRootDir().'/propel/fixtures';
        $this->fs->mkdir($propelFixturesDir);
    }

    protected function rebuildBundles()
    {
        $bundles = array_reverse($this->getContainer()->getParameter('c33s_core.config.bundles'));
        $appKernel = $this->getContainer()->get('kernel')->getRootDir().'/AppKernel.php';

        $this->removeBundles($appKernel);
        $this->cleanBaseImporter();
        $this->rebuildBaseImporter($bundles);
        $this->addBundles($appKernel, $bundles);

        $this->io->write('added Bundles');
    }

    protected function createSqlDataDirectory()
    {
        $sqlDataDir = $this->getContainer()->get('kernel')->getRootDir().'/data';

        $fs = new Filesystem();
        $fs->mkdir($sqlDataDir);
        $fs->dumpFile($sqlDataDir.'/.gitkeep', " ");
    }

    protected function rebuildBaseImporter($bundles)
    {
        $coreBundleConfigDir = $this->getContainer()->get('kernel')->getRootDir().'/config/corebundle';

        $importerLines = array();
        $importerLines[] = 'imports:';
        foreach ($bundles as $bundle => $properties)
        {
            $path = $this->getBundleConfigPath($bundle);

            if ($path !== false)
            {
                $importerLines[] = "- { resource: '@C33sCoreBundle/Resources/config/config/$bundle.yml' }";
            }
        }
        $fs = new Filesystem();
        $fs->dumpFile($coreBundleConfigDir.'/_base_importer.yml', implode("\n", $importerLines));

        $this->addBaseImporterYmlToConfig();

        $this->io->write('base importer rebuild');
    }

    protected function cleanBaseImporter()
    {
        $coreBundleConfigDir = $this->getContainer()->get('kernel')->getRootDir().'/config/corebundle';

        Tools::removeLineFromFile($this->getContainer()->get('kernel')->getRootDir().'/config/config.yml', '- { resource: corebundle/_base_importer.yml }');

        $fs = new Filesystem();
        $fs->remove($coreBundleConfigDir);
        $fs->mkdir($coreBundleConfigDir);
    }

    protected function addBaseImporterYmlToConfig()
    {
        $configDir = $this->getContainer()->get('kernel')->getRootDir().'/config';
        Tools::addLineToFile($configDir.'/config.yml', "    - { resource: corebundle/_base_importer.yml }\n", "- { resource: @C33sCoreBundle/Resources/config/config.yml }");
    }

    protected function enableTranslationsInConfig()
    {
        $configFile = $this->getContainer()->get('kernel')->getRootDir().'/config/config.yml';
        $original = '#translator:      { fallback: "%locale%" }';
        $new = "    translator:      { fallback: \"%locale%\" }\n";

        $this->io->write('enabling translations in main config');

        Tools::removeLineFromFile($configFile, $original);
        Tools::addLineToFile($configFile, $new, 'esi:             ~');
    }

    protected function getBundleConfigPath($bundle)
    {
        $path = false;
        try
        {
            $path = $this->getContainer()->get('kernel')->locateResource("@C33sCoreBundle/Resources/config/config/$bundle.yml");
        }
        catch (\InvalidArgumentException $e)
        {
            return false;
        }
        return $path;
    }

    protected function removeBundles($appKernel)
    {
        Tools::cropFileByLine($appKernel, "//# Sub Bundles ###", "//### End Core Bundle ###,", 1, -1, true);
    }

    protected function addBundles($appKernel, $bundles)
    {
        foreach ($bundles as $bundle => $properties)
        {
            if ($properties['class'] !== false)
            {
                $bundleDefinition = "            new ".$properties['class']."(),\n";
                Tools::addLineToFile($appKernel, $bundleDefinition, "//# Sub Bundles ###");
            }
        }
    }

    protected function renderFileFromTemplate($file, $targetDirectory = null, $parameters = array())
    {
        $parameters['asseticBundles'] = $this->asseticBundles;

        parent::renderFileFromTemplate($file, $targetDirectory, $parameters);
    }
}
