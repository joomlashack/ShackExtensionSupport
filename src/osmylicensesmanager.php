<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2015 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Alledia\Framework\Joomla\Extension\AbstractPlugin;
use Alledia\OSMyLicensesManager\Free\UpdateHelper;
use Alledia\OSMyLicensesManager\Free\PluginHelper;

defined('_JEXEC') or die();

require_once 'include.php';

if (defined('ALLEDIA_FRAMEWORK_LOADED')) {
    /**
     * OSMyLicensesManager System Plugin
     *
     */
    class PlgSystemOSMyLicensesManager extends AbstractPlugin
    {
        /**
         * The constructor
         *
         * @param [type] $subject
         * @param array  $config
         */
        public function __construct(&$subject, $config = array())
        {
            $this->namespace = 'OSMyLicensesManager';

            parent::__construct($subject, $config);
        }

        /**
         * This method detects when a recent installed extension is
         * trying to update the license key.
         *
         * @return void
         */
        public function onAfterInitialise()
        {
            $app    = JFactory::getApplication();
            $plugin = $app->input->getCmd('plugin');
            $task   = $app->input->getCmd('task');
            $user   = JFactory::getUser();

            // Filter the request, to only trigger when the user is looking for an update
            if ($app->getName() !== 'administrator'
                || $plugin !== 'system_osmylicensesmanager'
                || $task !== 'license.save'
                || $user->guest) {

                return;
            }

            $this->init();

            $licenseKeys = $app->input->post->getString('license-keys', '');

            $result = new stdClass;
            $result->success = false;
            if (PluginHelper::updateLicenseKeys($licenseKeys)) {
                $result->success = true;
            }

            echo json_encode($result);

            jexit();
        }

        /**
         * This method detects when Joomla is looking for updates and
         * find all Alledia Pro extensions trying to inject the
         * license keys on the update url and change the release channel.
         *
         * @return void
         */
        public function onAfterRoute()
        {
            $app    = JFactory::getApplication();
            $option = $app->input->getCmd('option');
            $view   = $app->input->getCmd('view');
            $task   = $app->input->getCmd('task');

            // Filter the request, to only trigger when the user is looking for an update
            if ($app->getName() != 'administrator'
                || $option !== 'com_installer'
                || $view !== 'update'
                || $task !== 'update.find') {

                return;
            }

            $this->init();

            UpdateHelper::updateLicenseKeys($this->params->get('license-keys', ''));
            UpdateHelper::updateReleaseChannel($this->params->get('release-channel', 'stable'));
        }
    }
}
