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
    protected $downloadUrls = array(
        // Invalid download URLs
        'https://update.joomla.org/language/translationlist_3.xml'            => false,
        'https://update.joomla.org/core/list.xml'                             => false,
        'https://update.joomla.org/jed/list.xml'                              => false,
        'https://update.joomla.org/core/extensions/com_joomlaupdate.xml'      => false,
        'https://deploy.ostraining.com'                                       => false,
        'https://ostraining.com'                                              => false,
        'https://deploy.ostraining.com/client/update/free/stable/com_dummy'   => false,
        'https://deploy.ostraining.com/client/update/pro/stable/com_dummy'    => false,
        'https://deploy.ostraining.com/client/update/free/1.0.3/com_dummy'    => false,
        // Valid download URLS
        'https://deploy.ostraining.com/client/download/free/stable/com_dummy'   => true,
        'https://deploy.ostraining.com/client/download/pro/stable/com_dummy'    => true,
        'https://deploy.ostraining.com/client/download/free/1.0.3/com_dummy'    => true,
        'https://deploy.ostraining.com/client/download/pro/1.0.3/com_dummy'     => true,
    );

    /**
     * Test detecting our own download URLs
     */
    public function testCheckingTheDownloadUrlValidation()
    {
        // Test the URLs set
        foreach ($this->downloadUrls as $url => $isOwers) {

            $result = UpdateHelper::isOurDownloadURL($url);

            if ($isOwers) {
                $this->assertTrue($result, "{$url} should be a valid download URL");
            } else {
                $this->assertFalse($result, "{$url} should be a invalid download URL");
            }
        }
    }

    /**
     * Test the method to strip the license key from the url.
     */
    public function testStrippingLicenseKeyFromURLWithLicenseKey()
    {
        $url = 'https://deploy.ostraining.com/client/download/pro/stable/com_dummy/5aJ3cjda3YjZmbzkx8s93XcwZWM3NG02cGJoaH0pvLDUzYTMwNWVlZDk2NDwn864MzE1MzNkOTI3NmUwMmIyYzYyZWMyYz3=';

        $newUrl = UpdateHelper::getURLWithoutLicenseKey($url);

        $this->assertEquals(
            'https://deploy.ostraining.com/client/download/pro/stable/com_dummy/',
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
        $url = 'https://deploy.ostraining.com/client/download/pro/stable/com_dummy';

        $newUrl = UpdateHelper::getURLWithoutLicenseKey($url);

        $this->assertEquals(
            'https://deploy.ostraining.com/client/download/pro/stable/com_dummy/',
            $newUrl
        );
    }

    /**
     * Test the method to strip the license key from an url which doesn't have
     * a license key and trailing slash. It should be the same.
     */
    public function testStrippingLicenseKeyFromURLWithoutLicenseKeyWithSlash()
    {
        $url = 'https://deploy.ostraining.com/client/download/pro/stable/com_dummy/';

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
     * to have a new segment encoded as base64.
     */
    public function testAppendingOneLicenseKeyToValidURL()
    {
        $url = 'https://deploy.ostraining.com/client/download/pro/stable/com_dummy/';

        $licenseKey = 'd41d8cd98f00b204e9800998ecf8427e';

        $newUrl = UpdateHelper::appendLicenseKeyToURL($url, $licenseKey);

        $this->assertEquals(
            $url . 'ZDQxZDhjZDk4ZjAwYjIwNGU5ODAwOTk4ZWNmODQyN2U=',
            $newUrl
        );
    }

    /**
     * Test the method to append multiple license keys to a URL. The final URL
     * needs to have a new segment encoded as base64. All spaces or invalid
     * chars should be stripped from the license key.
     */
    public function testAppendingMultipleLicenseKeysToValidURL()
    {
        $url = 'https://deploy.ostraining.com/client/download/pro/stable/com_dummy/';

        $licenseKey = 'd41d8cd98f00b204e9800998ecf8427e, 912ec803b2ce49e4a541068d495ab570 ';

        $newUrl = UpdateHelper::appendLicenseKeyToURL($url, $licenseKey);

        $this->assertEquals(
            $url . 'ZDQxZDhjZDk4ZjAwYjIwNGU5ODAwOTk4ZWNmODQyN2UsOTEyZWM4MDNiMmNlNDllNGE1NDEwNjhkNDk1YWI1NzA=',
            $newUrl
        );
    }

    /**
     * Test the method to append license keys to a URL with there is no license
     * key set. The final URL should be the same.
     */
    public function testAppendingBlankLicenseKeyToValidURL()
    {
        $url = 'https://deploy.ostraining.com/client/download/pro/stable/com_dummy/';

        $licenseKey = '';

        $newUrl = UpdateHelper::appendLicenseKeyToURL($url, $licenseKey);

        $this->assertEquals($url, $newUrl);
    }

    /**
     * Test the method to append license keys to a URL with there is no license
     * key set. The final URL should be the same.
     */
    public function testAppendingInvalidCharsOnLicenseKeyToValidURL()
    {
        $url = 'https://deploy.ostraining.com/client/download/pro/stable/com_dummy/';

        $licenseKey = '?==';

        $newUrl = UpdateHelper::appendLicenseKeyToURL($url, $licenseKey);

        $this->assertEquals($url, $newUrl);
    }

    /**
     * Get the license type from a valid URL. The method doesn't validate the
     * URL.
     */
    public function testGettingLicenseTypeFromURL()
    {
        $urls = array(
            'https://deploy.ostraining.com/client/download/pro/stable/com_dummy/'             => 'pro',
            'https://deploy.ostraining.com/client/download/free/stable/com_dummy/'            => 'free',
            'https://deploy.ostraining.com/client/download/invalid-license/stable/com_dummy/' => false
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

    /**
     * Tests the method which sanitizes a license key, removing invalid chars.
     */
    public function testSanitizingLicenseKey()
    {
        $keys = array(
            'e8a88bb6f4d420a8517965d25cd54a14'                                      => 'e8a88bb6f4d420a8517965d25cd54a14',
            'e8a88bb 6f4d420a8517965d25cd54a14 '                                    => 'e8a88bb6f4d420a8517965d25cd54a14',
            'e8a88bb6f4d420a8517965d25cd54a14,306fce8f3c4f74085a92081326713628'     => 'e8a88bb6f4d420a8517965d25cd54a14,306fce8f3c4f74085a92081326713628',
            ' e8a88bb6f4d420a8517965d25cd54a14 ,  306fce8f3c4f74085a92081326713628' => 'e8a88bb6f4d420a8517965d25cd54a14,306fce8f3c4f74085a92081326713628',
            '98-9238732723hshdhfs988?test=1233'                                     => '989238732723hshdhfs988test1233',
            '&ˆ%ˆ%$##@@@#$%ˆ&*())(*&ˆ%$#@#$%ˆ&*'                                    => '',
            'test@example.com'                                                      => 'testexamplecom'
        );

        foreach ($keys as $key => $expected) {
            $sanitizedKey = UpdateHelper::sanitizeKey($key);

            $this->assertEquals($expected, $sanitizedKey);
        }
    }

    /**
     * We need to check if it correctly detects the JCal Pro download Url.
     */
    public function testDetectingJcalProDownloadUrl()
    {
        $urls = array(
            // Invalid
            'https://deploy.ostraining.com/client/download/free/stable/com_dummy'    => false,
            'https://deploy.ostraining.com/client/download/pro/stable/com_dummy'     => false,
            'https://deploy.ostraining.com/client/download/free/stable/com_jcalpro'  => false,
            // Valid
            'https://deploy.ostraining.com/client/download/pro/stable/com_jcalpro'   => true,
            'https://deploy.ostraining.com/client/download/pro/unstable/com_jcalpro' => true
        );

        foreach ($urls as $url => $expected) {
            $result = UpdateHelper::isJCalProDownloadUrl($url);

            $this->assertEquals(
                $expected,
                $result,
                "Testing URL: {$url}"
            );

            $this->assertInternalType(
                'boolean',
                $result,
                "It should always return a boolean value for the URL: {$url}"
            );
        }
    }

    /**
     * Test the method to append one license key to a JCal Pro Url. The final
     * URL needs to have the default key, instead of the client's key.
     */
    public function testAppendingOneLicenseKeyToJcalProURL()
    {
        $urls = array(
            'https://deploy.ostraining.com/client/download/pro/stable/com_jcalpro/',
            'https://deploy.ostraining.com/client/download/pro/stable/pkg_jcalpro/'
        );

        // User's license key
        $licenseKey = 'd41d8cd98f00b204e9800998ecf8427e';

        foreach ($urls as $url) {
            $newUrl = UpdateHelper::appendLicenseKeyToURL($url, $licenseKey);

            $this->assertEquals(
                $url . base64_encode(UpdateHelper::DEFAULT_LICENSE_KEY),
                $newUrl,
                "Testing URL: {$url}. It should have the default license key"
            );
        }
    }
}
