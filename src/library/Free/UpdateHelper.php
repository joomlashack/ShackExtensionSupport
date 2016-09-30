<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2015 Alledia.com, All rights reserved
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
     * @var string
     */
    protected static $updateBaseURL = 'https://deploy.ostraining.com/client/update/';

    /**
     * Detects if the passed URL is our update URL, returning a boolean value.
     *
     * @param string $url
     *
     * @return string
     */
    public static function isOurUpdateURL($url)
    {
        return 1 === preg_match('#^' . self::$updateBaseURL . '#', $url);
    }

    /**
     * Removes the license key from the URL and returns it.
     *
     * @param string $url
     *
     * @return string
     */
    public static function getURLWithoutLicenseKey($url)
    {
        if (self::isOurUpdateURL($url)) {
            $url = preg_replace('#^(' . self::$updateBaseURL . '(free|pro)/[^/]+/[^/]+).*$#i', '$1', $url);
            $url .= '/';
        }

        return $url;
    }

    /**
     * Sanitizes the license key, making sure we have only valid chars.
     *
     * @var string $key
     *
     * @return string
     */
    public static function sanitizeKey($key)
    {
        return preg_replace('/[^a-z0-9,]/i', '', $key);
    }

    /**
     * Appends the license key to the URL and returns it.
     *
     * @param string $url
     * @param string $keys
     *
     * @return string
     */
    public static function appendLicenseKeyToURL($url, $keys)
    {
        if (self::isOurUpdateURL($url)) {
            $sanitizedKeys = self::sanitizeKey($keys);

            if (!empty($keys)) {
                $encodedKeys = base64_encode($sanitizedKeys);
                $url .= $encodedKeys;
            }
        }

        return $url;
    }

    /**
     * Detects the license type based on the URL. If no license is detected,
     * returns false
     *
     * @param string $url
     *
     * @return mixed
     */
    public static function getLicenseTypeFromURL($url)
    {
        preg_match('#^' . self::$updateBaseURL . '(free|pro)/#', $url, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return false;
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
