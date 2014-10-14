<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Alledia\Joomla\Extension\AbstractPlugin;

defined('_JEXEC') or die();

require_once 'include.php';

/**
 * OSMyLicensesManager System Plugin
 *
 */
class PlgSystemOSMyLicensesManager extends AbstractPlugin
{
    public function __construct(&$subject, $config = array())
    {
        $this->namespace = 'OSMyLicensesManager';

        parent::__construct($subject, $config);
    }
}
