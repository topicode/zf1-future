<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dojo_View_Helper_BorderContainerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_View_Helper_BorderContainerTest::main");
}

/** Zend_Dojo_View_Helper_BorderContainer */
require_once 'Zend/Dojo/View/Helper/BorderContainer.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_View_Helper_BorderContainer.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_BorderContainerTest extends TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new TestSuite("Zend_Dojo_View_Helper_BorderContainerTest");
        $result = (new TestRunner)->run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        Zend_Registry::_unsetInstance();
        Zend_Dojo_View_Helper_Dojo::setUseDeclarative();

        $this->view   = $this->getView();
        $this->helper = new Zend_Dojo_View_Helper_BorderContainer();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
    }

    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function getContainer()
    {
        $html = '';
        foreach (['top', 'bottom', 'center', 'left', 'right'] as $pane) {
            $id      = $pane . 'Pane';
            $content = 'This is the content of pane ' . $pane;
            $html   .= $this->view->contentPane($id, $content, ['region' => $pane]);
        }
        return $this->helper->borderContainer('container', $html, ['design' => 'headline']);
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getContainer();
        $this->assertMatchesRegularExpression('/<div[^>]*(dojoType="dijit.layout.BorderContainer")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $html = $this->getContainer();
        $this->assertDoesNotMatchRegularExpression('/<div[^>]*(dojoType="dijit.layout.BorderContainer")/', $html);
        $this->assertNotNull($this->view->dojo()->getDijit('container'));
    }

    /**
     * @group ZF-4664
     */
    public function testMultipleCallsToBorderContainerShouldNotCreateMultipleStyleEntries()
    {
        $this->getContainer();
        $this->getContainer();
        $style  = 'html, body { height: 100%; width: 100%; margin: 0; padding: 0; }';
        $styles = $this->helper->view->headStyle()->toString();
        $this->assertEquals(1, substr_count($styles, $style), $styles);
    }
}

// Call Zend_Dojo_View_Helper_BorderContainerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_View_Helper_BorderContainerTest::main") {
    Zend_Dojo_View_Helper_BorderContainerTest::main();
}
