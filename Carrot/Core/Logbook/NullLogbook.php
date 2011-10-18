<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Null object for logbook.
 *
 * The logbook implementation to be used if the user chooses to
 * turn off logging. All logging methods does nothing, and all
 * log accessor methods will always return empty values.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Logbook;

class NullLogbook
{
    
}