<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

define('OSMYLICENSESMANAGER_PLUGIN_PATH', __DIR__);

// Alledia Framework
if (!defined('ALLEDIA_FRAMEWORK_LOADED')) {
    $allediaFrameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';

    if (!file_exists($allediaFrameworkPath)) {
        throw new Exception('Alledia framework not found');
    }

    require_once $allediaFrameworkPath;
}

if (!defined('OSMyLicensesManagerHelper')) {
    require_once 'helper.php';
}
