<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2020 Joomlashack.com. All rights reserved
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

use Alledia\Framework\AutoLoader;

defined('_JEXEC') or die();

// Alledia Framework
if (!defined('ALLEDIA_FRAMEWORK_LOADED')) {
    $allediaFrameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';

    if (file_exists($allediaFrameworkPath)) {
        require_once $allediaFrameworkPath;
    } else {
        JFactory::getApplication()
            ->enqueueMessage('[Joomlashack License Key Manager] Joomlashack Framework not found', 'error');
    }
}

// Extension's library
if (defined('ALLEDIA_FRAMEWORK_LOADED') && !defined('OSMYLICENSESMANAGER_LOADED')) {
    AutoLoader::register('\\Alledia\\OSMyLicensesManager', JPATH_SITE . '/plugins/system/osmylicensesmanager/library');

    define('OSMYLICENSESMANAGER_LOADED', 1);
}
