<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Events\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */

namespace SilverWare\Events\Model;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\DataObject;
use SilverWare\Events\Pages\Event;
use SilverWare\Extensions\Lists\ListItemExtension;
use SilverWare\Extensions\Model\DetailFieldsExtension;
use SilverWare\Extensions\Model\MetaDataExtension;
use SilverWare\Extensions\Model\URLSegmentExtension;
use SilverWare\Forms\HasOneField;
use SilverWare\Security\CMSMainPermissions;
use SilverWare\View\Renderable;
use SilverWare\View\ViewClasses;

/**
 * An extension of the data object class for an event session.
 *
 * @package SilverWare\Events\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */
class EventSession extends DataObject
{
    use Renderable;
    use ViewClasses;
    use CMSMainPermissions;
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Event Session';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Event Sessions';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_EventSession';
    
    /**
     * Defines the default sort field and order for this object.
     *
     * @var string
     * @config
     */
    private static $default_sort = 'Start';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Start' => 'Datetime',
        'Finish' => 'Datetime',
        'IgnoreTimes' => 'Boolean',
        'Disabled' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Event' => Event::class,
        'Location' => EventLocation::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'IgnoreTimes' => 0,
        'Disabled' => 0
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'LocationLink' => 'HTMLFragment'
    ];
    
    /**
     * Defines the summary fields of this object.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'Start',
        'Finish',
        'Disabled'
    ];
    
    /**
     * Defines the searchable fields of this object.
     *
     * @var array
     * @config
     */
    private static $searchable_fields = [
        
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ListItemExtension::class,
        MetaDataExtension::class,
        URLSegmentExtension::class,
        DetailFieldsExtension::class
    ];
    
    /**
     * Defines the detail fields to show for the object.
     *
     * @var array
     * @config
     */
    private static $detail_fields = [
        'date' => [
            'name' => '$DateTitle',
            'text' => '$DateAndTime'
        ],
        'location' => [
            'name' => 'Location',
            'text' => '$LocationNameAndAddress'
        ]
    ];
    
    /**
     * Defines the list item details to show for this object.
     *
     * @var array
     * @config
     */
    private static $list_item_details = [
        'date' => false,
        'session' => [
            'icon' => 'calendar',
            'text' => '$Title'
        ],
        'location' => [
            'icon' => 'map-marker',
            'text' => '$LocationName'
        ]
    ];
    
    /**
     * Defines the meta field configuration for the object.
     *
     * @var array
     * @config
     */
    private static $meta_fields = [
        'Image' => false,
        'Summary' => false
    ];
    
    /**
     * Defines the date format for event sessions.
     *
     * @var string
     * @config
     */
    private static $date_format = 'E d MMM Y';
    
    /**
     * Defines the time format for event sessions.
     *
     * @var string
     * @config
     */
    private static $time_format = 'h:mm a';
    
    /**
     * Defines the setting for hiding the detail fields header.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_hide_header = false;
    
    /**
     * Defines the setting for showing the detail fields inline.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_inline = false;
    
    /**
     * Defines the setting for hiding the detail field names.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_hide_names = false;
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Create Field List and Tab Set:
        
        $fields = FieldList::create(TabSet::create('Root'));
        
        // Create Field Objects:
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                DatetimeField::create(
                    'Start',
                    $this->fieldLabel('Start')
                ),
                DatetimeField::create(
                    'Finish',
                    $this->fieldLabel('Finish')
                ),
                HasOneField::create(
                    'LocationID',
                    $this->fieldLabel('Location'),
                    EventLocation::get()
                )
                ->setDescriptor('Name')
                ->setRightTitle(
                    _t(
                        __CLASS__ . '.LOCATIONRIGHTTITLE',
                        'Overrides the location defined by the parent event.'
                    )
                ),
                CheckboxField::create(
                    'IgnoreTimes',
                    $this->fieldLabel('IgnoreTimes')
                ),
                CheckboxField::create(
                    'Disabled',
                    $this->fieldLabel('Disabled')
                )
            ]
        );
        
        // Extend Field Objects:
        
        $this->extend('updateCMSFields', $fields);
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a validator for the CMS interface.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return RequiredFields::create([
            'Start',
            'Finish'
        ]);
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
        
        $labels['Start'] = _t(__CLASS__ . '.START', 'Start');
        $labels['Finish'] = _t(__CLASS__ . '.FINISH', 'Finish');
        $labels['Disabled'] = _t(__CLASS__ . '.DISABLED', 'Disabled');
        $labels['IgnoreTimes'] = _t(__CLASS__ . '.IGNORETIMES', 'Ignore times');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['Event'] = _t(__CLASS__ . '.has_one_Event', 'Event');
            $labels['Location'] = _t(__CLASS__ . '.has_one_Location', 'Location');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $start  = strtotime('+1 hour');
        $finish = strtotime('+1 hour', $start);
        
        $this->Start  = date('Y-m-d H:00:00', $start);
        $this->Finish = date('Y-m-d H:00:00', $finish);
    }
    
    /**
     * Answers the title of the receiver for the CMS interface.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getDateAndTime();
    }
    
    /**
     * Answers the date and time of the session.
     *
     * @return string
     */
    public function getDateAndTime()
    {
        $start  = $this->dbObject('Start');
        $finish = $this->dbObject('Finish');
        
        $dateFormat = $this->config()->date_format;
        $timeFormat = $this->config()->time_format;
        
        $datetimeFormat = $dateFormat . ' ' . $timeFormat;
        
        if ($this->IgnoreTimes) {
            
            if ($start->Date() == $finish->Date()) {
                return $start->Format($dateFormat);
            } else {
                return $start->Format($dateFormat) . ' - ' . $finish->Format($dateFormat);
            }
            
        } else {
            
            if ($start->Date() == $finish->Date()) {
                
                if ($start->Time() == $finish->Time()) {
                    return $start->Format($datetimeFormat);
                } else {
                    return $start->Format($datetimeFormat) . ' - ' . $finish->Format($timeFormat);
                }
                
            } else {
                return $start->Format($datetimeFormat) . ' - ' . $finish->Format($datetimeFormat);
            }
            
        }
    }
    
    /**
     * Answers the link for the receiver.
     *
     * @param string $action
     *
     * @return string
     */
    public function getLink($action = null)
    {
        return $this->Event()->Link(Controller::join_links($this->URLSegment, $action));
    }
    
    /**
     * Answers true if times are to be shown.
     *
     * @return boolean
     */
    public function getShowTimes()
    {
        return !$this->IgnoreTimes;
    }
    
    /**
     * Answers the parent event of the session.
     *
     * @return Event
     */
    public function getParent()
    {
        return $this->Event();
    }
    
    /**
     * Answers the meta image from the associated event.
     *
     * @return Image
     */
    public function getMetaImage()
    {
        return $this->Event()->getMetaImage();
    }
    
    /**
     * Answers the meta title from the associated event.
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->Event()->getMetaTitle();
    }
    
    /**
     * Answers the meta summary from the associated event.
     *
     * @return DBHTMLText
     */
    public function getMetaSummary()
    {
        return $this->Event()->getMetaSummary();
    }
    
    /**
     * Answers the title for the date field.
     *
     * @return string
     */
    public function getDateTitle()
    {
        if ($this->IgnoreTimes) {
            return _t(__CLASS__ . '.DATE', 'Date');
        }
        
        return _t(__CLASS__ . '.DATEANDTIME', 'Date & Time');
    }
    
    /**
     * Answers the location for the event session.
     *
     * @return EventLocation
     */
    public function getSessionLocation()
    {
        if ($this->Location()->isInDB()) {
            return $this->Location();
        }
        
        return $this->Event()->Location();
    }
    
    /**
     * Answers the name of the location.
     *
     * @return string
     */
    public function getLocationName()
    {
        if ($location = $this->getSessionLocation()) {
            return $location->Name;
        }
    }
    
    /**
     * Answers the location name and address.
     *
     * @return string
     */
    public function getLocationNameAndAddress()
    {
        if ($location = $this->getSessionLocation()) {
            return $location->NameAndAddress;
        }
    }
}
