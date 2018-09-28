<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Events\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */

namespace SilverWare\Events\Pages;

use SilverStripe\Control\HTTPRequest;
use SilverWare\Crumbs\Breadcrumb;
use PageController;

/**
 * An extension of the page controller class for an event controller.
 *
 * @package SilverWare\Events\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */
class EventController extends PageController
{
    /**
     * Defines the URLs handled by this controller.
     *
     * @var array
     * @config
     */
    private static $url_handlers = [
        '$Session!' => 'session'
    ];
    
    /**
     * Defines the allowed actions for this controller.
     *
     * @var array
     * @config
     */
    private static $allowed_actions = [
        'session'
    ];
    
    /**
     * Renders a page for the requested event session.
     *
     * @param HTTPRequest $request
     *
     * @return HTTPResponse
     */
    public function session(HTTPRequest $request)
    {
        if ($session = $this->getCurrentSession()) {
            
            return [
                'EventSession' => $session
            ];
            
        }
        
        return $this->httpError(404);
    }
    
    /**
     * Answers the event session identified by the current request.
     *
     * @return EventSession
     */
    public function getCurrentSession()
    {
        if ($segment = $this->getRequest()->param('Session')) {
            return $this->getSessionByURLSegment($segment);
        }
    }
    
    /**
     * Answers a list of extra breadcrumb items for the template.
     *
     * @return ArrayList
     */
    public function getExtraBreadcrumbItems()
    {
        $items = parent::getExtraBreadcrumbItems();
        
        if ($crumb = $this->getExtraBreadcrumb()) {
            $items->push($crumb);
        }
        
        return $items;
    }
    
    /**
     * Answers an extra breadcrumb object for the current request.
     *
     * @return Breadcrumb
     */
    public function getExtraBreadcrumb()
    {
        // Answer Session Breadcrumb:
        
        if ($session = $this->getCurrentSession()) {
            
            return Breadcrumb::create(
                $session->Link,
                $session->Title
            );
            
        }
    }
    
    /**
     * Performs initialisation before any action is called on the receiver.
     *
     * @return void
     */
    protected function init()
    {
        // Initialise Parent:
        
        parent::init();
        
        // Initialise Object:
        
        
    }
}
