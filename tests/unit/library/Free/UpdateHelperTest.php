<?php
namespace library\Free;

use Alledia\OSMyLicensesManager\Free\UpdateHelper;

class UpdateHelperTest extends \Codeception\Test\Unit
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
     * Test detecting our own URLs
     */
    public function testCheckingTheUpdateUrlValidation()
    {
        // Test the URLs set
        foreach ($this->updateUrls as $url => $isOwers) {

            $result = UpdateHelper::isOurUpdateURL($url);

            if ($isOwers) {
                $this->assertTrue($result, "{$url} should be a valid update URL");
            } else {
                $this->assertFalse($result, "{$url} should be a invalid update URL");
            }
        }
    }

    /**
     * Test the method to strip the license key from the url.
     */
    public function testStrippingLicenseKeyFromURLWithLicenseKey()
    {
        $url = 'https://deploy.ostraining.com/client/update/pro/stable/com_dummy/5aJ3cjda3YjZmbzkx8s93XcwZWM3NG02cGJoaH0pvLDUzYTMwNWVlZDk2NDwn864MzE1MzNkOTI3NmUwMmIyYzYyZWMyYz3=';

        $newUrl = UpdateHelper::getURLWithoutLicenseKey($url);

        $this->assertEquals(
            'https://deploy.ostraining.com/client/update/pro/stable/com_dummy/',
            $newUrl
        );
    }

    /**
     * Test the method to strip the license key from an url which doesn't have
     * a license key and trailing slash. It should be the same, but with a
     * trailing slash.
     */
    public function testStrippingLicenseKeyFromURLWithoutLicenseKeyAndSlash()
    {
        $url = 'https://deploy.ostraining.com/client/update/pro/stable/com_dummy';

        $newUrl = UpdateHelper::getURLWithoutLicenseKey($url);

        $this->assertEquals(
            'https://deploy.ostraining.com/client/update/pro/stable/com_dummy/',
            $newUrl
        );
    }

    /**
     * Test the method to strip the license key from an url which doesn't have
     * a license key and trailing slash. It should be the same.
     */
    public function testStrippingLicenseKeyFromURLWithoutLicenseKeyWithSlash()
    {
        $url = 'https://deploy.ostraining.com/client/update/pro/stable/com_dummy/';

        $newUrl = UpdateHelper::getURLWithoutLicenseKey($url);

        $this->assertEquals(
            $newUrl,
            $url
        );
    }

    /**
     * Test the method to strip the license key from a third party url, which
     * should return the same url
     */
    public function testStrippingLicenseKeyFromThirdPartyURL()
    {
        $url = 'https://update.joomla.org/core/extensions/com_joomlaupdate.xml';

        $newUrl = UpdateHelper::getURLWithoutLicenseKey($url);

        $this->assertEquals(
            $newUrl,
            $url,
            "The URL can't change if processing a 3rd party URL"
        );
    }

    /**
     * Test the method to append the license key to a 3rd party URL. The url
     * needs to remain untouched.
     */
    public function testAppendingLicenseKeyToThirdPartyURL()
    {
        $url = 'https://update.joomla.org/core/extensions/com_joomlaupdate.xml';

        $licenseKey = 'd41d8cd98f00b204e9800998ecf8427e';

        $newUrl = UpdateHelper::appendLicenseKeyToURL($url, $licenseKey);

        $this->assertEquals(
            $newUrl,
            $url,
            "The URL can't change if processing a 3rd party URL"
        );
    }

    /**
     * Test the method to append one license key to a URL. The final URL needs
     * to have a new segment encoded with base64.
     */
    public function testAppendingOneLicenseKeyToValidURL()
    {
        $url = 'https://deploy.ostraining.com/client/update/pro/stable/com_dummy/';

        $licenseKey = 'd41d8cd98f00b204e9800998ecf8427e';

        $newUrl = UpdateHelper::appendLicenseKeyToURL($url, $licenseKey);

        $this->assertEquals(
            $url . 'ZDQxZDhjZDk4ZjAwYjIwNGU5ODAwOTk4ZWNmODQyN2U=',
            $newUrl
        );
    }

    /**
     * Test the method to append multiple license keys to a URL. The final URL
     * needs to have a new segment encoded with base64. All spaces or invalid
     * chars should be stripped from the license key.
     */
    public function testAppendingMultipleLicenseKeysToValidURL()
    {
        $url = 'https://deploy.ostraining.com/client/update/pro/stable/com_dummy/';

        $licenseKey = 'd41d8cd98f00b204e9800998ecf8427e, 912ec803b2ce49e4a541068d495ab570 ';

        $newUrl = UpdateHelper::appendLicenseKeyToURL($url, $licenseKey);

        $this->assertEquals(
            $url . 'ZDQxZDhjZDk4ZjAwYjIwNGU5ODAwOTk4ZWNmODQyN2UsOTEyZWM4MDNiMmNlNDllNGE1NDEwNjhkNDk1YWI1NzA=',
            $newUrl
        );
    }

    /**
     * Get the license type from a valid URL. The method doesn't validate the
     * URL.
     */
    public function testGettingLicenseTypeFromURL()
    {
        $urls = array(
            'https://deploy.ostraining.com/client/update/pro/stable/com_dummy/'             => 'pro',
            'https://deploy.ostraining.com/client/update/free/stable/com_dummy/'            => 'free',
            'https://deploy.ostraining.com/client/update/invalid-license/stable/com_dummy/' => false
        );

        foreach ($urls as $url => $expectedLicense) {
            $license = UpdateHelper::getLicenseTypeFromURL($url);

            $this->assertEquals(
                $expectedLicense,
                $license,
                "Tested with the url: {$url}"
            );
        }
    }
}
