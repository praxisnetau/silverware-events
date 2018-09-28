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

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverWare\Events\Model\EventLocation;
use SilverWare\Events\Model\EventSession;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\HasOneField;
use Page;

/**
 * An extension of the page class for an event.
 *
 * @package SilverWare\Events\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */
class Event extends Page
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Event';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Events';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'An event within an event calendar';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/events: admin/client/dist/images/icons/Event.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_Event';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Featured' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Location' => EventLocation::class
    ];
    
    /**
     * Defines the has-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_many = [
        'Sessions' => EventSession::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ShowInMenus' => 0,
        'ShowInSearch' => 1
    ];
    
    /**
     * Determines whether this object can exist at the root level.
     *
     * @var boolean
     * @config
     */
    private static $can_be_root = false;
    
    /**
     * Defines the list item details to show for this object.
     *
     * @var array
     * @config
     */
    private static $list_item_details = [
        'date' => false,
        'current-or-upcoming-session' => [
            'icon' => 'calendar',
            'text' => '$CurrentOrUpcomingSessionTitle'
        ]
    ];
    
    /**
     * Defines the asset folder for uploaded meta images.
     *
     * @var string
     * @config
     */
    private static $meta_image_folder = 'Events';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Main Fields:
        
        $fields->addFieldToTab(
            'Root.Main',
            HasOneField::create(
                'LocationID',
                $this->fieldLabel('Location'),
                EventLocation::get()
            )->setDescriptor('Name'),
            'Content'
        );
        
        // Create Sessions Tab:
        
        $fields->findOrMakeTab(
            'Root.Sessions',
            $this->fieldLabel('Sessions')
        );
        
        // Create Sessions Field:
        
        $fields->addFieldToTab(
            'Root.Sessions',
            GridField::create(
                'Sessions',
                $this->fieldLabel('Sessions'),
                $this->Sessions(),
                $sessionsConfig = GridFieldConfig_RecordEditor::create()
            )
        );
        
        // Obtain Data Columns Component:
        
        $dataColumns = $sessionsConfig->getComponentByType(GridFieldDataColumns::class);
        
        // Define Data Column Field Formatting:
        
        $dataColumns->setFieldFormatting([
            'Start' => function ($value, $item) {
                return $item->dbObject('Start')->Nice();
            },
            'Finish' => function ($value, $item) {
                return $item->dbObject('Finish')->Nice();
            },
            'Disabled' => function ($value, $item) {
                return $item->dbObject('Disabled')->Nice();
            }
        ]);
        
        // Create Options Tab:
        
        $fields->findOrMakeTab(
            'Root.Options',
            $this->fieldLabel('Options')
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'EventOptions',
                    $this->fieldLabel('Event'),
                    [
                        CheckboxField::create(
                            'Featured',
                            $this->fieldLabel('Featured')
                        )
                    ]
                )
            ]
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['Event'] = _t(__CLASS__ . '.EVENT', 'Event');
        $labels['Options'] = _t(__CLASS__ . '.OPTIONS', 'Options');
        $labels['Featured'] = _t(__CLASS__ . '.FEATURED', 'Featured');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['Sessions'] = _t(__CLASS__ . '.has_many_Sessions', 'Sessions');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers a list of the enabled sessions within the receiver.
     *
     * @return DataList
     */
    public function getEnabledSessions()
    {
        return $this->Sessions()->filter('Disabled', 0);
    }
    
    /**
     * Answers an enabled session with the given URL segment.
     *
     * @param string $segment
     *
     * @return EventSession
     */
    public function getSessionByURLSegment($segment)
    {
        return $this->getEnabledSessions()->find('URLSegment', $segment);
    }
    
    /**
     * Answers a list of the current sessions.
     *
     * @param integer $limit
     *
     * @return DataList
     */
    public function getCurrentSessions($limit = null)
    {
        $now = date('Y-m-d');
        
        $current = $this->getEnabledSessions()->filter([
            'Start:LessThan' => $now,
            'Finish:GreaterThanOrEqual' => $now
        ]);
        
        if ($limit) {
            $current = $current->limit($limit);
        }
        
        return $current;
    }
    
    /**
     * Answers true if the event has an upcoming session.
     *
     * @return boolean
     */
    public function hasUpcomingSession()
    {
        return $this->getEnabledSessions()->exclude([
            'Start:LessThan' => date('Y-m-d')
        ])->exists();
    }
    
    /**
     * Answers a list of the upcoming sessions within the receiver.
     *
     * @param integer $limit
     *
     * @return DataList
     */
    public function getUpcomingSessions($limit = null)
    {
        $upcoming = $this->getEnabledSessions()->exclude([
            'Start:LessThan' => date('Y-m-d')
        ]);
        
        if ($limit) {
            $upcoming = $upcoming->limit($limit);
        }
        
        return $upcoming;
    }
    
    /**
     * Answers the current or upcoming event session.
     *
     * @return EventSession
     */
    public function getCurrentOrUpcomingSession()
    {
        if ($session = $this->getCurrentSession()) {
            return $session;
        }
        
        return $this->getNextSession();
    }
    
    /**
     * Answers the current event session (if available).
     *
     * @return EventSession
     */
    public function getCurrentSession()
    {
        return $this->getCurrentSessions()->first();
    }
    
    /**
     * Answers the next upcoming event session.
     *
     * @return EventSession
     */
    public function getNextSession()
    {
        return $this->getUpcomingSessions(1)->first();
    }
    
    /**
     * Answers the start date/time of the next upcoming event session.
     *
     * @return DBDatetime
     */
    public function getStartOfNextSession()
    {
        if ($session = $this->getNextSession()) {
            return $session->dbObject('Start');
        }
    }
    
    /**
     * Answers the title of the current or upcoming session title.
     *
     * @return string
     */
    public function getCurrentOrUpcomingSessionTitle()
    {
        if ($session = $this->getCurrentOrUpcomingSession()) {
            return $session->Title;
        }
    }
    
    /**
     * Answers the start date/time of the current or upcoming session.
     *
     * @return DB_Datetime
     */
    public function getCurrentOrUpcomingStart()
    {
        if ($session = $this->getCurrentOrUpcomingSession()) {
            return $session->dbObject('Start');
        }
    }
}
