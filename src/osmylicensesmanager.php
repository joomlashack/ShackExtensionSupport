<?php
/**
 * @package   ShackExtensionSupport
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2021 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackExtensionSupport.
 *
 * ShackExtensionSupport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackExtensionSupport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackExtensionSupport.  If not, see <http://www.gnu.org/licenses/>.
 */

use Alledia\Framework\Joomla\Extension\AbstractPlugin;
use Alledia\Framework\Joomla\Extension\Helper;
use Alledia\OSMyLicensesManager\Free\PluginHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

if (include 'include.php') {
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
         * @return void
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
         * @return void
         */
        public function onAfterRender()
        {
            $option    = $this->app->input->getCmd('option');
            $extension = $this->app->input->getCmd('extension');

            if (
                $this->app->isClient('administrator')
                && $option === 'com_categories'
                && $extension
                && $extension !== 'com_content'
            ) {
                $this->addCustomFooterIntoNativeComponentOutput($extension);
            }
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
            if (!PluginHelper::isOurDownloadURL($url)) {
                return true;
            }

            // Check if it is not a free extension
            if (PluginHelper::getLicenseTypeFromURL($url) === 'free') {
                return true;
            }

            $this->init();

            // Appends the license keys to the URL
            $licenseKeys = $this->params->get('license-keys', '');
            $url         = PluginHelper::appendLicenseKeyToURL($url, $licenseKeys);

            return true;
        }

        /**
         * @param ?string $element
         *
         * @return void
         */
        protected function addCustomFooterIntoNativeComponentOutput(?string $element)
        {
            // Check if the specified extension is from Alledia
            $extension = Helper::getExtensionForElement($element);
            $footer    = $extension->getFooterMarkup();

            if (!empty($footer)) {
                $this->app->setBody(
                    str_replace('</section>', '</section>' . $footer, $this->app->getBody())
                );
            }
        }
    }
}
