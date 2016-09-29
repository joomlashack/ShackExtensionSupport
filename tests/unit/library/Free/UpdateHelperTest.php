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

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     * Test detecting our own URLs
     */
    public function testCheckTheUpdateUrlValidation()
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
}
