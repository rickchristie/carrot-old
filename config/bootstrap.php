<?php

/**
 * Bootstrap file. Put your bootstraping code here. They are run
 * at the very start of the request cycle before anything has
 * happened.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */

// Strict mode, baby!
// This could be set from php.ini. It's still here for safety.
error_reporting(-1);

// Don't forget to change this to 'Off' in production
// so that framework errors aren't sent to users.
ini_set('display_errors', 'On');