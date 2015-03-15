<?php

namespace C33s\CoreBundle\BuildingBlock;

use C33s\ConstructionKitBundle\BuildingBlock\SimpleBuildingBlock;

class PropelBuildingBlock extends SimpleBuildingBlock
{
    /**
     * Return true if this block should be installed automatically as soon as it is registered (e.g. using composer).
     * This is the only public method that should not rely on a previously injected Kernel.
     *
     * @return boolean
     */
    public function isAutoInstall()
    {
        return true;
    }

    /**
     * Get the fully namespaced classes of all bundles that should be enabled to use this BuildingBlock.
     * These will be used in AppKernel.php
     *
     * @return array
     */
    public function getBundleClasses()
    {
        return array(
            'Bazinga\\Bundle\\PropelEventDispatcherBundle\\BazingaPropelEventDispatcherBundle',
            'C33s\\AttachmentBundle\\C33sAttachmentBundle',
            'Glorpen\\Propel\\PropelBundle\\GlorpenPropelBundle',
            'Propel\\PropelBundle\\PropelBundle',
        );
    }

    /**
     * This suffix will be added to all searches for default configs, config templates and assets.
     * Use this to easily have 1 bundle serve multiple building blocks without them interfering.
     *
     * @return string
     */
    protected function getPathSuffix()
    {
        return 'propel';
    }
}
