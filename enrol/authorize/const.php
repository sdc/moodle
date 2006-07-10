<?php // $Id$

/**#@+
 * Order status used in enrol_authorize table.
 *
 * NONE: New order or order is in progress. TransactionID hasn't received yet.
 * AUTH: Authorized/Pending Capture.
 * CAPTURE: Captured.
 * AUTHCAPTURE: Authorized/Captured
 * CREDIT: Refunded.
 * VOID: Cancelled.
 * EXPIRE: Expired. Orders be expired unless be accepted within 30 days.
 * TEST: Tested. It means created in TEST mode and TransactionID is 0.
 */
define('AN_STATUS_NONE',        0x00);
define('AN_STATUS_AUTH',        0x01);
define('AN_STATUS_CAPTURE',     0x02);
define('AN_STATUS_AUTHCAPTURE', 0x03);
define('AN_STATUS_CREDIT',      0x04);
define('AN_STATUS_VOID',        0x08);
define('AN_STATUS_EXPIRE',      0x10);
define('AN_STATUS_TEST',        0x80);
/**#@-*/

/**#@+
 * Actions used in authorize_action function.
 *
 * NONE: No action. Function always returns false.
 * AUTH_ONLY: Used to authorize only, don't capture.
 * PRIOR_AUTH_CAPTURE:  Used to capture, it was authorized before.
 * AUTH_CAPTURE: Used to authorize and capture.
 * CREDIT: Used to return funds to a customer's credit card.
 * VOID: Used to cancel an exiting pending transaction.
 *
 * Credit rules:
 *  1. It can be credited within 120 days after the original authorization was obtained.
 *  2. Amount can be any amount up to the original amount charged.
 *  3. Captured/pending settlement transactions cannot be credited,
 *     instead a void must be issued to cancel the settlement.
 *  NOTE: It assigns a new transactionID to the original transaction.
 *        We should save it, so admin can cancel new transaction if it is a mistake return.
 *
 * Void rules:
 *  1. These requests effectively cancel the Capture request that would start the funds transfer process.
 *  2. It mustn't be settled. Please set up settlement date correctly.
 *  3. These transactions can be voided:
 *     authorized/pending capture, captured/pending settlement, credited/pending settlement
 */
define('AN_ACTION_NONE',                0x00);
define('AN_ACTION_AUTH_ONLY',           0x01);
define('AN_ACTION_PRIOR_AUTH_CAPTURE',  0x02);
define('AN_ACTION_AUTH_CAPTURE',        0x03);
define('AN_ACTION_CREDIT',              0x04);
define('AN_ACTION_VOID',                0x08);
/**#@-*/

?>
