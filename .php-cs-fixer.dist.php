<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['src'])
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
;
