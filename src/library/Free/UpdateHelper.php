<?php
/**
 * @package   OSMyLicensesManager
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Joomlashack.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia\OSMyLicensesManager\Free;

defined('_JEXEC') or die();

/**
 * Helper class
 */
abstract class UpdateHelper
{
    const DEFAULT_LICENSE_KEY = '5a6f1dc7e58c04590b3f83b5f61f1aa4270772da';

    /**
     * @var string
     */
    protected static $downloadBaseURL = 'https://deploy.ostraining.com/client/download/';

    /**
     * Detects if the passed URL is our download URL, returning a boolean value.
     *
     * @param string $url
     *
     * @return string
     */
    public static function isOurDownloadURL($url)
    {
        return 1 === preg_match('#^' . self::$downloadBaseURL . '#', $url);
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
        if (self::isOurDownloadURL($url)) {
            $url = preg_replace('#^(' . self::$downloadBaseURL . '(free|pro)/[^/]+/[^/]+).*$#i', '$1', $url);
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
     * Appends the license key to the URL and returns it. We recognize the url
     * for designated generic extensions using a default license key to allow
     * legacy customers to download updates.
     *
     * @param string $url
     * @param string $keys
     *
     * @return string
     */
    public static function appendLicenseKeyToURL($url, $keys)
    {
        if (self::isOurDownloadURL($url)) {
            // Handle possible generic key extensions
            if (self::isGenericKeyDownload($url)) {
                $keys = $sanitizedKeys = self::DEFAULT_LICENSE_KEY;

            } else {
                // Removes any license key from the URL
                $url = UpdateHelper::getURLWithoutLicenseKey($url);

                $sanitizedKeys = self::sanitizeKey($keys);
            }

            if (!empty($keys)) {
                $encodedKeys = base64_encode($sanitizedKeys);
                $url .= $encodedKeys;
            }
        }

        return $url;
    }

    /**
     * Detects if it is a recognized generic pro license download URL. This method can be
     * updated as needed whenever we a generic license key. For example, legacy extensions
     *
     * @param string $url
     *
     * @return bool
     */
    public static function isGenericKeyDownload($url)
    {
        return false;
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
        preg_match('#^' . self::$downloadBaseURL . '(free|pro)/#', $url, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return false;
    }
}
