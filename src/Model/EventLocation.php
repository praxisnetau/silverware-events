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

use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataObject;
use SilverWare\Countries\Forms\CountryDropdownField;
use SilverWare\Events\Pages\Event;
use SilverWare\Forms\FieldSection;
use SilverWare\Security\CMSMainPermissions;
use SilverWare\Tools\ViewTools;

/**
 * An extension of the data object class for an event location.
 *
 * @package SilverWare\Events\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */
class EventLocation extends DataObject
{
    use CMSMainPermissions;
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Location';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Locations';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_EventLocation';
    
    /**
     * Defines the default sort field and order for this object.
     *
     * @var string
     * @config
     */
    private static $default_sort = 'Name';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Name' => 'Varchar(255)',
        'Street' => 'Varchar(255)',
        'StreetLine2' => 'Varchar(255)',
        'Suburb' => 'Varchar(255)',
        'StateTerritory' => 'Varchar(128)',
        'PostalCode' => 'Varchar(32)',
        'Country' => 'Varchar(2)',
        'Latitude' => 'Decimal(9,6)',
        'Longitude' => 'Decimal(9,6)',
        'Email' => 'Varchar(128)',
        'Phone' => 'Varchar(128)'
    ];
    
    /**
     * Defines the has-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_many = [
        'Events' => Event::class,
        'Sessions' => EventSession::class
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'FullAddress' => 'Text',
        'FullStreet' => 'Text',
        'NameAndAddress' => 'HTMLFragment'
    ];
    
    /**
     * Defines the summary fields of this object.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'Name',
        'FullStreet',
        'Suburb',
        'StateTerritory',
        'PostalCode',
        'CountryName'
    ];
    
    /**
     * Defines the searchable fields of this object.
     *
     * @var array
     * @config
     */
    private static $searchable_fields = [
        'Name' => [
            'filter' => 'PartialMatchFilter'
        ]
    ];
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Create Field List and Tab Set:
        
        $fields = FieldList::create(TabSet::create('Root'));
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNSELECT', 'Select');
        
        // Add Class:
        
        $fields->fieldByName('Root')->addExtraClass(ViewTools::singleton()->convertClass(self::class));
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'Name',
                    $this->fieldLabel('Name')
                ),
                FieldSection::create(
                    'LocationSection',
                    $this->fieldLabel('Location'),
                    [
                        TextField::create(
                            'Street',
                            $this->fieldLabel('Street')
                        )->addExtraClass('street-line-1'),
                        TextField::create(
                            'StreetLine2',
                            $this->fieldLabel('StreetLine2')
                        )->addExtraClass('street-line-2'),
                        TextField::create(
                            'Suburb',
                            $this->fieldLabel('Suburb')
                        )->addExtraClass('suburb'),
                        TextField::create(
                            'StateTerritory',
                            $this->fieldLabel('StateTerritory')
                        )->addExtraClass('state-territory'),
                        TextField::create(
                            'PostalCode',
                            $this->fieldLabel('PostalCode')
                        )->addExtraClass('postal-code'),
                        $country = CountryDropdownField::create(
                            'Country',
                            $this->fieldLabel('Country')
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)->addExtraClass('country')
                    ]
                ),
                FieldSection::create(
                    'CoordinatesSection',
                    $this->fieldLabel('Coordinates'),
                    [
                        TextField::create(
                            'Latitude',
                            $this->fieldLabel('Latitude')
                        )->addExtraClass('latitude'),
                        TextField::create(
                            'Longitude',
                            $this->fieldLabel('Longitude')
                        )->addExtraClass('longitude')
                    ]
                ),
                FieldSection::create(
                    'ContactSection',
                    $this->fieldLabel('Contact'),
                    [
                        EmailField::create(
                            'Email',
                            $this->fieldLabel('Email')
                        )->addExtraClass('email'),
                        TextField::create(
                            'Phone',
                            $this->fieldLabel('Phone')
                        )->addExtraClass('phone')
                    ]
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
            'Name'
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
        
        $labels['Name'] = _t(__CLASS__ . '.NAME', 'Name');
        $labels['Email'] = _t(__CLASS__ . '.EMAIL', 'Email');
        $labels['Phone'] = _t(__CLASS__ . '.PHONE', 'Phone');
        $labels['Street'] = _t(__CLASS__ . '.STREET', 'Street');
        $labels['Suburb'] = _t(__CLASS__ . '.SUBURBCITY', 'Suburb / City');
        $labels['Address'] = _t(__CLASS__ . '.ADDRESS', 'Address');
        $labels['Contact'] = _t(__CLASS__ . '.CONTACT', 'Contact');
        $labels['Country'] = _t(__CLASS__ . '.COUNTRY', 'Country');
        $labels['Location'] = _t(__CLASS__ . '.LOCATION', 'Location');
        $labels['Latitude'] = _t(__CLASS__ . '.LATITUDE', 'Latitude');
        $labels['Longitude'] = _t(__CLASS__ . '.LONGITUDE', 'Longitude');
        $labels['FullStreet'] = _t(__CLASS__ . '.STREET', 'Street');
        $labels['PostalCode'] = _t(__CLASS__ . '.POSTALCODEZIP', 'Postal Code / ZIP');
        $labels['CountryName'] = _t(__CLASS__ . '.COUNTRY', 'Country');
        $labels['Coordinates'] = _t(__CLASS__ . '.COORDINATES', 'Coordinates');
        $labels['StreetLine2'] = _t(__CLASS__ . '.STREETLINE2', 'Street Line 2');
        $labels['StateTerritory'] = _t(__CLASS__ . '.STATETERRITORYREGION', 'State / Territory / Region');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Event method called before the receiver is written to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        // Call Parent Event:
        
        parent::onBeforeWrite();
        
        // Correct Country Value:
        
        $this->Country = strtolower($this->Country);
    }
    
    /**
     * Answers the full address as a string.
     *
     * @return string
     */
    public function getFullAddress($separator = "\n")
    {
        $address = [];
        
        if ($this->Street) {
            $address[] = $this->Street;
        }
        
        if ($this->StreetLine2) {
            $address[] = $this->StreetLine2;
        }
        
        if ($this->Suburb || $this->PostalCode || $this->StateTerritory) {
            
            $line = [];
            
            if ($this->Suburb) {
                $line[] = $this->Suburb;
            }
            
            if ($this->PostalCode) {
                $line[] = $this->PostalCode;
            }
            
            if ($this->StateTerritory) {
                $line[] = $this->StateTerritory;
            }
            
            $address[] = implode(' ', $line);
            
        }
        
        if ($this->Country) {
            $address[] = $this->CountryName;
        }
        
        return implode($separator, $address);
    }
    
    /**
     * Answers the full street of the address.
     *
     * @return string
     */
    public function getFullStreet()
    {
        $street = [];
        
        if ($this->Street) {
            $street[] = $this->Street;
        }
        
        if ($this->StreetLine2) {
            $street[] = $this->StreetLine2;
        }
        
        return implode(', ', $street);
    }
    
    /**
     * Answers the title of the receiver for the CMS interface.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->Name;
    }
    
    /**
     * Converts the address to a string.
     *
     * @return string
     */
    public function toString()
    {
        return $this->getFullAddress(', ');
    }
    
    /**
     * Answers the full name of the country.
     *
     * @return string
     */
    public function getCountryName()
    {
        return i18n::getData()->countryName($this->Country);
    }
    
    /**
     * Answers the link and address for the receiver.
     *
     * @param string $action
     *
     * @return string
     */
    public function getNameAndAddress($action = null)
    {
        return sprintf('%s<br>%s', $this->Name, nl2br($this->FullAddress));
    }
}
