<?php

$dirFile = __DIR__.'/../.deployed_project_dir';
if (!file_exists($dirFile))
{
    throw new \LogicException('Cannot run deployed test without .deployed_project_dir file in bundle root directory');
}

$projectDir = trim(file_get_contents($dirFile));
if (!is_dir($projectDir))
{
    throw new \LogicException('Invalid directory '.$projectDir.' specified in .deployed_project_dir file');
}

require_once($projectDir.'/app/bootstrap.php.cache');
