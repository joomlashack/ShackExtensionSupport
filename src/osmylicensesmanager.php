<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Alledia\Joomla\Extension\AbstractPlugin;
use Alledia\Joomla\Extension;
use Alledia\Helper;

defined('_JEXEC') or die();

require_once 'include.php';

/**
 * OSMyLicensesManager System Plugin
 *
 */
class PlgSystemOSMyLicensesManager extends AbstractPlugin
{
    /**
     * The constructor
     *
     * @param [type] $subject [description]
     * @param array  $config  [description]
     */
    public function __construct(&$subject, $config = array())
    {
        $this->namespace = 'OSMyLicensesManager';

        parent::__construct($subject, $config);
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
        $task = $app->input->getCmd('task');

        // Filter the request, to only trigger when the user is looking for an update
        if ($app->getName() != 'administrator'
            || $option !== 'com_installer'
            || $view !== 'update'
            || $task !== 'update.find') {

            return;
        }

        OSMyLicensesManagerHelper::updateLicenseKeys($this->params->get('license-keys', ''));
        OSMyLicensesManagerHelper::updateReleaseChannel($this->params->get('release-channel', 'stable'));
    }
}
