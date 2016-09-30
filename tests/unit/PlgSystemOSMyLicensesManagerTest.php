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
     * Returns a new instance of the plugin
     *
     * @return PlgSystemOSMyLicensesManager
     */
    protected function getPluginInstance(
        $licenseKeys = 'd41d8cd98f00b204e9800998ecf8427e'
    ) {
        $dummyContext = new JEventDispatcher;

        $dummyParams = new JRegistry(
            array(
                'license-keys'    => $licenseKeys
            )
        );

        return Stub::construct(
            'PlgSystemOSMyLicensesManager',
            array(&$dummyContext),
            array(
                // Overrides the init method
                'init'   => true,
                'params' => $dummyParams
            )
        );
    }

    /**
     * The plugin should ignore third part URLs on the event
     * onInstallerBeforePackageDownload.
     */
    public function testIgnoringThirdPartUrlOnInstallerBeforePackageDownload()
    {
        $plugin = $this->getPluginInstance();

        $updateUrls = array(
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

        $headers = array();

        // Test the URLs set
        foreach ($updateUrls as $url => $isOwers) {
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
     * Test if we handle only URLs for Pro extensions. Check the license filter
     * on the event onInstallerBeforePackageDownload.
     */
    public function testFilteringFreeLicenseOnInstallerBeforePackageDownload()
    {
        $url         = 'https://deploy.ostraining.com/client/update/free/stable/com_dummy/';
        $originalURL = $url;
        $headers     = array();

        $plugin = $this->getPluginInstance();
        $plugin->onInstallerBeforePackageDownload($url, $headers);

        $this->assertEquals($originalURL, $url);
    }

    /**
     * Test updating the package download url with the license key for a pro
     * extension.
     */
    public function testUpdatingUrlWithLicenseKeyForPro()
    {
        $url         = 'https://deploy.ostraining.com/client/update/pro/stable/com_dummy';
        $licenseKey  = 'd41d8cd98f00b204e9800998ecf8427e';
        $expected    = $url . '/' . base64_encode($licenseKey);
        $headers     = array();

        $plugin = $this->getPluginInstance($licenseKey);
        $plugin->onInstallerBeforePackageDownload($url, $headers);

        $this->assertEquals($expected, $url);
    }
}
