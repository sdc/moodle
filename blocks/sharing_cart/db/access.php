<?php // $Id: access.php 905 2012-12-05 05:36:52Z malu $

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'block/sharing_cart:addinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ),
        'clonepermissionsfrom' => 'moodle/site:manageblocks',
    ),
);
