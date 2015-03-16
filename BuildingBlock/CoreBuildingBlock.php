<?php

namespace C33s\CoreBundle\BuildingBlock;

use C33s\ConstructionKitBundle\BuildingBlock\SimpleBuildingBlock;

class CoreBuildingBlock extends SimpleBuildingBlock
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
            // main bundle (holds config and assets)
            'C33s\\CoreBundle\\C33sCoreBundle',

            // any other required bundles
            'Avocode\\FormExtensionsBundle\\AvocodeFormExtensionsBundle',
            'Bazinga\\Bundle\\PropelEventDispatcherBundle\\BazingaPropelEventDispatcherBundle',
            'Braincrafted\\Bundle\\BootstrapBundle\\BraincraftedBootstrapBundle',
            'C33s\\AttachmentBundle\\C33sAttachmentBundle',
            'C33s\\ContactFormBundle\\C33sContactFormBundle',
            'C33s\\MenuBundle\\C33sMenuBundle',
            //'C33s\\StaticPageContentBundle\\C33sStaticPageContentBundle',
            'Cocur\\HumanDate\\Bridge\\Symfony\\CocurHumanDateBundle',
            'Cocur\\Slugify\\Bridge\\Symfony\\CocurSlugifyBundle',
            'Fkr\\CssURLRewriteBundle\\FkrCssURLRewriteBundle',
            'FOS\\UserBundle\\FOSUserBundle',
            'Glorpen\\Propel\\PropelBundle\\GlorpenPropelBundle',
            'Knp\\Bundle\\GaufretteBundle\\KnpGaufretteBundle',
            'Knp\\Bundle\\MarkdownBundle\\KnpMarkdownBundle',
            'Liip\\ImagineBundle\\LiipImagineBundle',
            'Propel\\PropelBundle\\PropelBundle',
            'SunCat\\MobileDetectBundle\\MobileDetectBundle',
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
        return 'c33s_core';
    }
}
