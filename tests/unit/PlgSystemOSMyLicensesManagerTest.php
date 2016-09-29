<?php
use \Codeception\Configuration;
use \Codeception\Util\Stub;

require_once SRC_PATH . '/osmylicensesmanager.php';

class PlgSystemOSMyLicensesManagerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Set of urls to test. True if should be validated as our own URLs.
     * False if is invalid, third part URLs.
     */
    protected $updateUrls = array(
        // Invalid update URLs
        'https://update.joomla.org/language/translationlist_3.xml'            => false,
        'https://update.joomla.org/core/list.xml'                             => false,
        'https://update.joomla.org/jed/list.xml'                              => false,
        'https://update.joomla.org/core/extensions/com_joomlaupdate.xml'      => false,
        'https://deploy.ostraining.com'                                       => false,
        'https://ostraining.com'                                              => false,
        'https://deploy.ostraining.com/client/download/free/stable/com_dummy' => false,
        'https://deploy.ostraining.com/client/download/pro/stable/com_dummy'  => false,
        'https://deploy.ostraining.com/client/download/free/1.0.3/com_dummy'  => false,
        // Valid update URLS
        'https://deploy.ostraining.com/client/update/free/stable/com_dummy'   => true,
        'https://deploy.ostraining.com/client/update/pro/stable/com_dummy'    => true,
        'https://deploy.ostraining.com/client/update/free/1.0.3/com_dummy'    => true,
        'https://deploy.ostraining.com/client/update/pro/1.0.3/com_dummy'     => true,
    );

    /**
     * Returns a new instance of the plugin
     *
     * @return PlgSystemOSMyLicensesManager
     */
    protected function getPluginInstance()
    {
        $context = new JEventDispatcher;

        return Stub::construct(
            'PlgSystemOSMyLicensesManager',
            array(&$context),
            array(
                // Overrides the init method
                'init' => true
            )
        );
    }

    /**
     * The plugin should ignore third part URLs
     */
    public function testIgnoringThirdPartURLOnInstallerBeforePackageDownload()
    {
        $plugin = $this->getPluginInstance();

        $headers = array();

        // Test the URLs set
        foreach ($this->updateUrls as $url => $isOwers) {
            // We copy to have the original URL, since it can be updated by the method
            $originalURL = $url;

            $result = $plugin->onInstallerBeforePackageDownload($url, $headers);

            if ($isOwers) {
                // The URL should be updated by the method but we only test here if it returns true
                $this->assertTrue($result, "The plugin should return true for ower URL {$originalURL}");
            } else {
                $this->assertTrue($result, "The plugin should return true for the invalid URL {$originalURL}");
                // Check if the URL was changed by the method. It shouldn't
                $this->assertEquals($originalURL, $url, "The URL {$originalURL} shouldn't be updated to {$url}");
            }
        }
    }

    /**
     * The plugin should update our URLs adding the license key
     */
    public function testAppendingLicenseKeyOnInstallerBeforePackageDownload()
    {
        // $plugin = $this->getPluginInstance();

        // $headers = array();

        // // Test the URLs set
        // foreach ($this->updateUrls as $url => $isOwers) {
        //     // We copy to have the original URL, since it can be updated by the method
        //     $originalURL = $url;

        //     $result = $plugin->onInstallerBeforePackageDownload($url, $headers);

        //     if ($isOwers) {
        //         // The URL should be updated by the method but we only test here if it returns true
        //         $this->assertTrue($result, "The plugin should return true for the URL {$originalURL}");
        //         // Check if the url has the license key

        //     } else {
        //         $this->assertTrue($result, "The plugin should return true for the URL {$originalURL}");
        //         // Check if the URL was changed by the method. It shouldn't
        //         $this->assertEquals($originalURL, $url, "The URL {$originalURL} shouldn't be updated to {$url}");
        //     }
        // }
    }
}
