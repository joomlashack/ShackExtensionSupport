<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2021 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSMyLicensesManager.
 *
 * OSMyLicensesManager is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSMyLicensesManager is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSMyLicensesManager.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Alledia\OSMyLicensesManager\Free;

use Alledia\Framework\Joomla\Extension\Licensed;

defined('_JEXEC') or die();

/**
 * Helper class
 */
abstract class PluginHelper
{
    /**
     * Update the license key on the plugin params
     *
     * @param ?string $licenseKeys
     *
     * @return bool
     */
    public static function updateLicenseKeys(?string $licenseKeys = ''): bool
    {
        $licenseKeys = UpdateHelper::sanitizeKey($licenseKeys);

        // Update the extension params
        $extension = new Licensed('osmylicensesmanager', 'plugin', 'system');
        $extension->params->set('license-keys', $licenseKeys);
        $extension->storeParams();

        return true;
    }
}
