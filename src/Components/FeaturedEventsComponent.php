<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Events\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */

namespace SilverWare\Events\Components;

use SilverStripe\ORM\ArrayList;
use SilverWare\Components\BaseComponent;
use SilverWare\Events\Pages\Event;
use SilverWare\Extensions\Lists\ListViewExtension;

/**
 * An extension of the base component class for a featured events component.
 *
 * @package SilverWare\Events\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */
class FeaturedEventsComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Featured Events';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Featured Events';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'Shows a list of the upcoming featured events';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/events: admin/client/dist/images/icons/FeaturedEventsComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_FeaturedEventsComponent';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ListViewExtension::class
    ];
    
    /**
     * Answers the source for the list component.
     *
     * @return DataList
     */
    public function getListSource()
    {
        return ArrayList::create(
            Event::get()->filter([
               'Featured' => 1
            ])->toArray()
        )->sort('CurrentOrUpcomingStart');
    }
}
