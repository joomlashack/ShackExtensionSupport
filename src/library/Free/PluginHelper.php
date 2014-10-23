<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia\OSMyLicensesManager\Free;

use Alledia\Framework\Extension;

defined('_JEXEC') or die();

/**
 * Helper class
 */
abstract class PluginHelper
{
    /**
     * Update the license key on the plugin params
     *
     * @param  string $licenseKeys
     * @return boolean
     */
    public static function updateLicenseKeys($licenseKeys = '')
    {
        // Sanitize
        $licenseKeys = preg_replace('/[^a-z0-9]/i', '', $licenseKeys);

        // Update the extension params
        $extension = new Extension('osmylicensesmanager', 'plugin', 'system');
        $extension->params->set('license-keys', $licenseKeys);
        $extension->storeParams();

        return true;
    }
}
