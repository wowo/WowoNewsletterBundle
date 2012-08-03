#!/usr/bin/env php
<?php

set_time_limit(0);

$vendorDir = __DIR__;
$deps = array(
    array('symfony', 'http://github.com/symfony/symfony', isset($_SERVER['SYMFONY_VERSION']) ? $_SERVER['SYMFONY_VERSION'] : 'origin/master'),
    array('doctrine-common', 'http://github.com/doctrine/common.git', 'origin/master'),
    array('doctrine-dbal', 'http://github.com/doctrine/dbal.git', 'origin/master'),
    array('doctrine', 'http://github.com/doctrine/doctrine2.git', 'origin/master'),
    array('swiftmailer', 'http://github.com/swiftmailer/swiftmailer.git', 'v4.1.0'),
    array('proxy-object', 'https://github.com/lapistano/proxy-object.git', 'v1.2.0'),
    array('mockery', 'https://github.com/padraic/mockery.git', 'origin/master'),
    array('bundles/Wowo/QueueBundle', 'git://github.com/wowo/WowoQueueBundle.git', 'origin/master'),
    array('lapistano', 'https://github.com/lapistano/proxy-object.git', 'origin/master'),
);

foreach ($deps as $dep) {
    list($name, $url, $rev) = $dep;

    echo "> Installing/Updating $name\n";

    $installDir = $vendorDir.'/'.$name;
    if (!is_dir($installDir)) {
        system(sprintf('git clone -q %s %s', escapeshellarg($url), escapeshellarg($installDir)));
    }

    system(sprintf('cd %s && git fetch -q origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
}
