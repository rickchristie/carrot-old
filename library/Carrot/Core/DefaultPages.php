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
 * Default pages.
 *
 * Contains default pages in Carrot, including (but not limited
 * to) default no matching HTTP route page and default no
 * matching CLI route page.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Carrot\Response\HTTPResponse,
    Carrot\Response\CLIResponse;

class DefaultPages
{
    /**
     * Default no matching HTTP route destination method.
     * 
     * Returns a 404 response with a generic 404 page not found
     * message in HTML.
     * 
     * @return HTTPResponse
     *
     */
    public function HTTPNoMatchingRoute()
    {
        $body = $this->get404PageBody();
        return new HTTPResponse($body, 404);
    }
    
    /**
     * Default no matching CLI route destination method.
     * 
     * @return CLIResponse
     *
     */
    public function CLINoMatchingRoute()
    {
        $body = 'Router does not find any matching CLI route.' . PHP_EOL
              . 'Make sure your arguments are correctly typed.' . PHP_EOL;
        return new CLIResponse($body);
    }
    
    /**
     * Returns a generic 404 page not found HTML page in a string.
     *
     * @return string
     *
     */
    protected function get404PageBody()
    {
        return "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
        <html>
        <head>
        	<title>Page Not Found</title>
        	<style type=\"text/css\">
        	   body { width: 500px; margin: 150px; font-family: 'Arial', 'Helvetica', sans-serif; font-size: 13px; line-height: 1.5; }
        	   h1 { font-weight: normal; font-size: 25px }
        	   code { font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace; font-size: 90%; }
        	   p { margin: 15px 0; }
        	   p.message { background: #f1f7fd; border: 1px solid #aebece; -moz-border-radius: 5px; border-radius: 5px; padding: 15px; overflow-x: auto; }
        	   ul { margin: 15px 0; padding: 0 0 0 0; list-style-type: none; }
        	   ul li { margin: 8px 0; padding: 0 0 0 40px; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAaRJREFUeNqc0l8oQ1EcB/DvuXfW7v4gWY3JrNGWPKiJlELhDSnlAU8elPLiyYNXj548+fO49qgm7/IkL1LTZCzNljQMubYx9x6/W1eNdGd+dfqdfuf26Xd+5zLzqh9amCxV+C1el6IwCkHPzfhnfAH2j0JxGxyLtA9WApj0HLOo5lCxoGwxizABhonwcaKG6s/6Mu6gVrF7q7hpn3Heai4ogyvxm5hk5kk6SkZmrMNlgSdRvhIhBGxcwkL6DqPVucaRPqDbp2hdRAgJlpsBZNvb+VQm09Mvy+iQFCoADU0iOj2qpCMuQ4CGiKHHh2ies9vUJRXShDyp8LgFBFzcrSPST4Cuzb8V6KNeSnvt7x91bfV05qI524HTayBxzzbHQ7l5Q0BHtP9ix5dXgh02le4iAg6GoxTD7SvGCdk1BHREa3fDIyuzLSYVtU4BGUnAYYZlqd5GSNYQKIHmKK0FZKXGb+U4s4qIF1iYgOk/ATqivcC6K6dOermKC5uIe7ABQg7+BJRAYxrke1GbEw7hhPZdFQEls1mitUxrvmKgBHJSyn8KMAD+06jQNTPITwAAAABJRU5ErkJggg==); background-repeat: no-repeat; background-position: 18px 1px; }
        	</style>
        </head>
        <body>
            <h1>Page Not Found</h1>
        	<p>
        	    We are unable to find the page that you have requested.
            </p>
            <p class=\"message\">
                <code>Ask and it shall be given to you.</code><br />
                <code>Seek and you will find.</code><br />
                <code>Knock and the door will be opened to you.</code>
            </p>
        </body>
        </html>";
    }
}