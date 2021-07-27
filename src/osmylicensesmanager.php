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

use Alledia\Framework\Joomla\Extension\AbstractPlugin;
use Alledia\OSMyLicensesManager\Free\PluginHelper;
use Alledia\OSMyLicensesManager\Free\UpdateHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

if (!require_once 'include.php') {
    return;
}

class PlgSystemOSMyLicensesManager extends AbstractPlugin
{
    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @inheritdoc
     */
    protected $namespace = 'OSMyLicensesManager';

    /**
     * This method detects when a recently installed extension is
     * trying to update the license key.
     *
     * @return void
     * @throws Exception
     */
    public function onAfterInitialise()
    {
        $plugin = $this->app->input->getCmd('plugin');
        $task   = $this->app->input->getCmd('task');
        $user   = Factory::getUser();

        // Filter the request, to only trigger when the user tried to save a license key from the installer screen
        if (
            !$this->app->isClient('administrator')
            || $plugin !== 'system_osmylicensesmanager'
            || $task !== 'license.save'
            || $user->guest
        ) {
            return;
        }

        $this->init();

        $licenseKeys = $this->app->input->post->getString('license-keys', '');

        $result = (object)[
            'success' => PluginHelper::updateLicenseKeys($licenseKeys)
        ];

        echo json_encode($result);

        jexit();
    }

    /**
     * Handle download URL and headers append the license keys to the url,
     * if it is a valid URL of Pro extension.
     *
     * @param string $url
     *
     * @return bool
     */
    public function onInstallerBeforePackageDownload(string &$url): bool
    {
        // Only handle our urls
        if (!UpdateHelper::isOurDownloadURL($url)) {
            return true;
        }

        // Check if it is not a free extension
        if ('free' === UpdateHelper::getLicenseTypeFromURL($url)) {
            return true;
        }

        $this->init();

        // Appends the license keys to the URL
        $licenseKeys = $this->params->get('license-keys', '');
        $url         = UpdateHelper::appendLicenseKeyToURL($url, $licenseKeys);

        return true;
    }
}
