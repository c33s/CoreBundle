CoreBundle
==========

everything to quickstart a webpage

[![Build Status](https://secure.travis-ci.org/c33s/CoreBundle.png?branch=master)](http://travis-ci.org/c33s/CoreBundle)
[![Latest Stable Version](https://poser.pugx.org/c33s/core-bundle/v/stable.png)](https://packagist.org/packages/c33s/core-bundle) 
[![Latest Unstable Version](https://poser.pugx.org/c33s/core-bundle/v/unstable.png)](https://packagist.org/packages/c33s/core-bundle) 
[![License](https://poser.pugx.org/c33s/core-bundle/license.png)](https://packagist.org/packages/c33s/core-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c0b45e1c-695f-45d9-ac81-ce2c21ddbb7e/mini.png)](https://insight.sensiolabs.com/projects/c0b45e1c-695f-45d9-ac81-ce2c21ddbb7e)
[![project status](http://stillmaintained.com/c33s/CoreBundle.png)](http://stillmaintained.com/c33s/CoreBundle)

Because json is not a really handy format to read and it also lacks in commenting support, this Bundle supports the composer.yml format. [composer-yaml.phar](https://github.com/igorw/composer-yaml) 
is used, to convert from yml to json. In this manual all composer code snippets are in yml format. Create a script file, which call the yml to json converter before running composer. Make sure you 
have both `composer` and `composer-yaml` commands at your fingertips.

## Short Quick Manual

You can perform the whole installation by executing the following commands inside your empty project directory:      

```sh
# Get sample composer file directly from github
wget https://raw.githubusercontent.com/c33s/CoreBundle/master/Resources/files/composer-example.yml -O composer.yml --no-check-certificate
# Modify composer.yml as needed. You may leave this for later.

# Create empty composer.json
touch composer.json

# Convert composer.yml to json format. Do this every time you modify your composer.yml
composer-yaml convert composer.yml

# Update dependencies without running any scripts. This may take a while.
composer update --no-scripts

# In the following commands, replace "YourNamespace" with your default Namespace prefix you want to use for this project's bundles. Keep it short but helpful.
./bin/init-symfony run YourNamespace
composer run-script post-update-cmd

# Init basic configuration
php app/console c33s:init-config YourNamespace

# Generate cms structure (webpage and admin bundles)
php app/console c33s:init-cms YourNamespace

# Optional: generate AdminGeneratorGenerator configuration that is automatically patched and correctly integrated into your project
php app/console admin:c33s:build YourNamespace

composer dump-autoload

# This command will clear your cache and pre-render assets
php app/console c33s:clean

# Make sure the web server permissions are set up correctly. This includes the path for media uploads as well as the sqlite database used by default.
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs app/data web/media
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs app/data web/media

```

If this goes well, you should see some example pages as well as a secured admin login when accessing /admin/.
