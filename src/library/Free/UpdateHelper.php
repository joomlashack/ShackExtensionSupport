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
            $sanitizedKeys = preg_replace('/[^a-z0-9,]/i', '', $keys);

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
     * licence keys on the update url
     *
     * @param  string $keys
     * @return void
     */
    public static function updateLicenseKeys($keys = '')
    {
        if (!empty($keys)) {
            // Get the license keys, converting to a url param
            $keys = base64_encode(preg_replace('/[^a-z0-9,]/i', '', $keys));

            $allediaProExtensions = \Alledia\Framework\Helper::getAllediaExtensions('pro');
            if (!empty($allediaProExtensions)) {
                foreach ($allediaProExtensions as $extension) {
                    $url = $extension->getUpdateURL();

                    // Check if we already have any key on the update urls
                    preg_match(
                        '#^http[s]?://[a-z0-9\.]*/client/update/pro/[^/]+/[^/]*/?([a-z0-9=+]*)[/]?#i',
                        $url,
                        $matches
                    );

                    if (isset($matches[1])) {
                        if ($matches[1] === $keys) {
                            // We don't need to change this url
                            continue;
                        } else {
                            // Remove the current key and the '/'
                            $url = str_replace($matches[1], '', $url);
                        }
                    }

                    // Add the key on the udpate url
                    if (!preg_match('#/$#i', $url)) {
                        $url .= '/';
                    }
                    $url .= $keys;

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
