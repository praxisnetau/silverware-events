<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Events\Admin
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */

namespace SilverWare\Events\Admin;

use SilverStripe\Admin\ModelAdmin;
use SilverWare\Events\Model\EventLocation;

/**
 * An extension of the model admin class for the event admin.
 *
 * @package SilverWare\Events\Admin
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-events
 */
class EventAdmin extends ModelAdmin
{
    /**
     * Defines the URL segment for this controller.
     *
     * @var string
     * @config
     */
    private static $url_segment = 'events';
    
    /**
     * Defines the menu title for this controller.
     *
     * @var string
     * @config
     */
    private static $menu_title = 'Events';
    
    /**
     * Defines the menu icon class for this controller.
     *
     * @var string
     * @config
     */
    private static $menu_icon_class = 'fa fa-calendar';
    
    /**
     * Defines the models managed by this model admin.
     *
     * @var array
     * @config
     */
    private static $managed_models = [
        EventLocation::class
    ];
}
