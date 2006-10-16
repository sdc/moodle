<?php // $Id$

$enrol_authorize_capabilities = array(

    'enrol/authorize:managepayments' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'enrol/authorize:uploadcsv' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    )

);

?>
