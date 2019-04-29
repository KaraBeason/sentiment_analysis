<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'block/mysites:myaddinstance' => array(
        'riskbitmask'   => RISK_SPAM | RISK_XSS,
        'captype'       => 'write',
        'contextlevel'  => CONTEXT_SYSTEM,
        'archetypes'    => array(
            'user' => CAP_ALLOW,
        ),
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),
);