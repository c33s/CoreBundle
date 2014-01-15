<?php

namespace c33s\CoreBundle\Util;


class BundleHelper
{
    protected $kernel;
    protected $kernelManipulator;


    public function __construct(KernelInterface $kernel)
    {
	$this->kernel = $kernel;
	$this->kernelManipulator = new KernelManipulator($kernel);
    }
    public function enableBundle()
    {
	
    }
    
    public function disableBundle()
    {
	
    }
    protected function updateKernel(KernelInterface $kernel, $namespace, $bundle)
    {
        $auto = true;
   
        
        $manip = new KernelManipulator($kernel);
        try {
            $ret = $auto ? $manip->addBundle($namespace.'\\'.$bundle) : false;

            if (!$ret) {
                $reflected = new \ReflectionObject($kernel);

                return array(
                    sprintf('- Edit <comment>%s</comment>', $reflected->getFilename()),
                    '  and add the following bundle in the <comment>AppKernel::registerBundles()</comment> method:',
                    '',
                    sprintf('    <comment>new %s(),</comment>', $namespace.'\\'.$bundle),
                    '',
                );
            }
        } catch (\RuntimeException $e) {
            return array(
                sprintf('Bundle <comment>%s</comment> is already defined in <comment>AppKernel::registerBundles()</comment>.', $namespace.'\\'.$bundle),
                '',
            );
        }
    }   
    
}