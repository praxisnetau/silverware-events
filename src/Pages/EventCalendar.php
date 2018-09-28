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

use SilverWare\Events\Model\EventSession;
use SilverWare\Extensions\Model\ImageDefaultsExtension;
use SilverWare\Extensions\Lists\ListViewExtension;
use SilverWare\Lists\ListSource;
use Page;

/**
 * An extension of the page class for an even calendar.
 *
 * @package SilverWare\Events\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */
class EventCalendar extends Page implements ListSource
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Event Calendar';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Event Calendars';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'An event calendar which holds a series of events and sessions';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/events: admin/client/dist/images/icons/EventCalendar.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_EventCalendar';
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = Event::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        Event::class
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ListViewExtension::class,
        ImageDefaultsExtension::class
    ];
    
    /**
     * Answers a list of all events within the calendar.
     *
     * @return DataList
     */
    public function getAllEvents()
    {
        return Event::get()->filter('ParentID', $this->ID);
    }
    
    /**
     * Answers a list of current event sessions.
     *
     * @return ArrayList
     */
    public function getCurrentEventSessions()
    {
        // Obtain Child Event IDs:
        
        $eventIds = $this->getAllEvents()->column('ID');
        
        // Filter Event Sessions:
        
        $sessions = EventSession::get()->filter([
            'EventID' => $eventIds ?: null,
            'Disabled' => 0
        ])->where('"Start" >= NOW() OR ("Start" < NOW() AND "Finish" >= NOW())');
        
        // Answer List:
        
        return $sessions;
    }
    
    /**
     * Answers a list of upcoming event sessions.
     *
     * @return ArrayList
     */
    public function getUpcomingEventSessions()
    {
        // Obtain Child Event IDs:
        
        $eventIds = $this->getAllEvents()->column('ID');
        
        // Filter Event Sessions:
        
        $sessions = EventSession::get()->filter([
            'EventID' => $eventIds ?: null,
            'Disabled' => 0,
            'Start:GreaterThanOrEqual' => date('Y-m-d')
        ]);
        
        // Answer List:
        
        return $sessions;
    }
    
    /**
     * Answers a list of upcoming event sessions within the receiver.
     *
     * @return ArrayList
     */
    public function getListItems()
    {
        return $this->getCurrentEventSessions();
    }
}
