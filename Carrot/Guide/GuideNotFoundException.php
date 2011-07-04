<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Guide Not Found Exception
 * 
 * Exception thrown by Model when it can't find the guide queried,
 * see {@see Model} for more information on when this is thrown.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Guide;

use InvalidArgumentException;

class GuideNotFoundException extends InvalidArgumentException
{
    
}