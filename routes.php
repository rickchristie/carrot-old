<?php

/**
 * List of routes, loaded by Carrot's DefaultRouter class.
 *
 * -NOTICE- If you don't use the default router provided by Carrot,
 * this file would never be loaded let a lone examined.
 *
 * Create a new chain by using RouterChain::add(), like this:
 *
 * <code>
 * $chain->add(function($request, $session, $chain)
 * {
 *    if ($request->getAppRequestURISegments(0) == 'about')
 *    {
 *        return new Destination
 *        (
 *            'controller' => '\Vendor\Namespace\Subnamespace\Class:name',
 *            'method' => 'index',
 *            'params' => array('Key Lime Pie', 'Black Forest', 'Orange Juice');
 *        );
 *    }
 *    
 *    // We can't handle this route, pass the responsibility to the next chain
 *    return $chain->next($request, $session, $chain);
 * });
 * </code>
 *
 */

