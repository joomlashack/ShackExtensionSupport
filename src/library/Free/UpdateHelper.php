<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia\OSMyLicensesManager\Free;

defined('_JEXEC') or die();

/**
 * Helper class
 */
abstract class UpdateHelper
{
    /**
     * Get all Alledia Pro extensions and update the
     * licence keys on the update url
     *
     * @param  string $keys
     * @return void
     */
    public static function updateLicenseKeys($keys = '')
    {
        if (!empty($keys)) {
            // Get the license keys
            $keys = explode("\n", $keys);

            // Sanitize the key
            if (!empty($keys)) {
                foreach ($keys as &$key) {
                    $key = trim(preg_replace('/[^a-z0-9]/i', '', $key));
                }
            }

            // Convert the keys to a url param
            $keys = base64_encode(implode(',', $keys));

            $allediaProExtensions = \Alledia\Framework\Helper::getAllediaExtensions('pro');
            if (!empty($allediaProExtensions)) {
                foreach ($allediaProExtensions as $extension) {
                    $url = $extension->getUpdateURL();

                    // Check if we already have any key on the update urls
                    preg_match(
                        '#^http[s]?://[a-z0-9\.]*/client/update/pro/[^/]+/[^/]*(/([a-z0-9=\+/]*))#i',
                        $url,
                        $matches
                    );

                    if (isset($matches[2])) {
                        if ($matches[2] === $keys) {
                            // We don't need to change this url
                            continue;
                        } else {
                            // Remove the current key and the '/'
                            $url = str_replace($matches[1], '', $url);
                        }
                    }

                    // Add the key on the udpate url
                    $url .= '/' . $keys;

                    $extension->setUpdateURL($url);
                }
            }
        }
    }

    /**
     * Get all Alledia Pro extensions and update the
     * release channel on the update url
     *
     * @param  string $channel
     * @return void
     */
    public static function updateReleaseChannel($channel)
    {
        if (!empty($channel)) {
            $allediaProExtensions = \Alledia\Framework\Helper::getAllediaExtensions('pro');

            if (!empty($allediaProExtensions)) {
                foreach ($allediaProExtensions as $extension) {
                    $url = $extension->getUpdateURL();

                    // Check if we already have the correct channel
                    preg_match(
                        '#^https://[a-z0-9\.]*:3425/joomla/update/pro/([^/]+)/.*#i',
                        $url,
                        $matches
                    );

                    if (isset($matches[1])) {
                        if ($matches[1] === $channel) {
                            // We don't need to update this url
                            continue;
                        } else {
                            // Replace the current channel for the new one
                            $url = str_replace('/' . $matches[1] . '/', '/' . $channel . '/', $url);
                            $extension->setUpdateURL($url);
                        }
                    }
                }
            }
        }
    }
}
