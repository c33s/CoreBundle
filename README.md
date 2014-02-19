CoreBundle
==========

everything to quickstart a webpage


[![Build Status](https://secure.travis-ci.org/c33s/CoreBundle.png?branch=master)](http://travis-ci.org/c33s/CoreBundle) [![Latest Stable Version](https://poser.pugx.org/c33s/core-bundle/v/stable.png)](https://packagist.org/packages/c33s/core-bundle) [![Latest Unstable Version](https://poser.pugx.org/c33s/core-bundle/v/unstable.png)](https://packagist.org/packages/c33s/core-bundle) [![License](https://poser.pugx.org/c33s/core-bundle/license.png)](https://packagist.org/packages/c33s/core-bundle) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/c0b45e1c-695f-45d9-ac81-ce2c21ddbb7e/mini.png)](https://insight.sensiolabs.com/projects/c0b45e1c-695f-45d9-ac81-ce2c21ddbb7e)


Because json is not a really handy format to read and it also lacks in commenting support, this Bundle supports the composer.yml format. [composer-yaml.phar](https://github.com/igorw/composer-yaml) is used, to convert from yml to json. In this manual all composer code snippets are in yml format. Create a script file, which call the yml to json converter before running composer.


create a composer.yml with the framework-standard-edition version you want to use and also include the core bundle:

    repositories:
      - type: vcs
        url: https://github.com/c33s/AssetManagementBundle.git
    require:
        symfony/framework-standard-edition: '2.3.*'
        c33s/core-bundle:   'dev-master#v0.99.1'
    
        #### Locks from corebundle ###########################################################################
        cedriclombardot/admingenerator-generator-bundle: 'dev-master#6dd565dacb6e668b9bcfa216a2acca356949375c'
        avocode/form-extensions-bundle:                  'dev-master#cd83e011f7fcc979cb5714c33423845c7ce36f0a'
        white-october/pagerfanta-bundle:                 'dev-master#606467f9e9f9e80975128db589eec2f9d11139c2'
        #### End Locks corebundle ###########################################################################
    
    scripts:
        post-install-cmd:
          - 'Incenteev\ParameterHandler\ScriptHandler::buildParameters'
          - 'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::buildBootstrap'
          - 'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::clearCache'
          - 'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::installAssets'
          - 'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::installRequirementsFile'
        post-update-cmd:
          - 'Incenteev\ParameterHandler\ScriptHandler::buildParameters'
          - 'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::buildBootstrap'
          - 'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::clearCache'
          - 'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::installAssets'
          - 'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::installRequirementsFile'
    config:
        bin-dir: bin
        component-dir: "web/components"
        component-baseurl: "/components"
    minimum-stability: stable
    extra:
        symfony-app-dir: app
        symfony-web-dir: web
        incenteev-parameters: { file: app/config/parameters.yml }
        branch-alias: { dev-master: 2.3-dev }
      
after you ran ```composer update --no-scripts```, you can use the ```./bin/init-symfony run``` command to create a project. the command copies the data from the framework-standard-edition.

now you can run the update scripts.

    composer run-script post-update-cmd

then you can call 

    php app/console  c33s:init-config


## Short Quick Manual

    create empty composer.json
    create basic composer.yml 
    json convert composer.yml
    composer update --no-scripts
    bin/init-symfony run
    composer run-script post-update-cmd
    php app/console c33s:init-config
