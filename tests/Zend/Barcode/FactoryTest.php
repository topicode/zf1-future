<?php
use PHPUnit\Framework\TestCase;
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
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Barcode.php';
require_once 'Zend/Config.php';
require_once 'Zend/Barcode/Renderer/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_FactoryTest extends TestCase
{

    public function testMinimalFactory()
    {
        $this->_checkGDRequirement();

        $renderer = Zend_Barcode::factory('code39');
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code39);
    }

    public function testMinimalFactoryWithRenderer()
    {
        $renderer = Zend_Barcode::factory('code39', 'pdf');
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Pdf);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code39);
    }

    public function testFactoryWithOptions()
    {
        $this->_checkGDRequirement();

        $options = ['barHeight' => 123];
        $renderer = Zend_Barcode::factory('code39', 'image', $options);
        $this->assertEquals(123, $renderer->getBarcode()->getBarHeight());
    }

    public function testFactoryWithAutomaticExceptionRendering()
    {
        $this->_checkGDRequirement();

        $options = ['barHeight' => - 1];
        $renderer = Zend_Barcode::factory('code39', 'image', $options);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Error);
    }

    public function testFactoryWithoutAutomaticObjectExceptionRendering()
    {
        $this->expectException(Zend_Barcode_Object_Exception::class);
        $options = ['barHeight' => - 1];
        $renderer = Zend_Barcode::factory('code39', 'image', $options, [], false);
    }

    public function testFactoryWithoutAutomaticRendererExceptionRendering()
    {
        $this->expectException(Zend_Barcode_Renderer_Exception::class);
        $this->_checkGDRequirement();

        $options = ['imageType' => 'my'];
        $renderer = Zend_Barcode::factory('code39', 'image', [], $options, false);
        $this->markTestIncomplete('Need to throw a configuration exception in renderer');
    }

    public function testFactoryWithZendConfig()
    {
        $this->_checkGDRequirement();

        $config = new Zend_Config(
                ['barcode' => 'code39' ,
                        'renderer' => 'image']);
        $renderer = Zend_Barcode::factory($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code39);

    }

    public function testFactoryWithZendConfigAndObjectOptions()
    {
        $this->_checkGDRequirement();

        $config = new Zend_Config(
                ['barcode' => 'code25' ,
                        'barcodeParams' => [
                                'barHeight' => 123]]);
        $renderer = Zend_Barcode::factory($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code25);
        $this->assertEquals(123, $renderer->getBarcode()->getBarHeight());
    }

    public function testFactoryWithZendConfigAndRendererOptions()
    {
        $this->_checkGDRequirement();

        $config = new Zend_Config(
                ['barcode' => 'code25' ,
                        'rendererParams' => [
                                'imageType' => 'gif']]);
        $renderer = Zend_Barcode::factory($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code25);
        $this->assertSame('gif', $renderer->getImageType());
    }

    public function testFactoryWithoutBarcodeWithAutomaticExceptionRender()
    {
        $this->_checkGDRequirement();

        $renderer = Zend_Barcode::factory(null);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Error);
    }

    public function testFactoryWithoutBarcodeWithAutomaticExceptionRenderWithZendConfig()
    {
        $this->_checkGDRequirement();

        $config = new Zend_Config(['barcode' => null]);
        $renderer = Zend_Barcode::factory($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Error);
    }

    public function testFactoryWithExistingBarcodeObject()
    {
        $this->_checkGDRequirement();

        $barcode = new Zend_Barcode_Object_Code25();
        $renderer = Zend_Barcode::factory($barcode);
        $this->assertSame($barcode, $renderer->getBarcode());
    }

    public function testBarcodeObjectFactoryWithExistingBarcodeObject()
    {
        $barcode = new Zend_Barcode_Object_Code25();
        $generatedBarcode = Zend_Barcode::makeBarcode($barcode);
        $this->assertSame($barcode, $generatedBarcode);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsString()
    {
        $barcode = Zend_Barcode::makeBarcode('code25');
        $this->assertTrue($barcode instanceof Zend_Barcode_Object_Code25);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsStringAndConfigAsArray()
    {
        $barcode = Zend_Barcode::makeBarcode('code25', ['barHeight' => 123]);
        $this->assertTrue($barcode instanceof Zend_Barcode_Object_Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsStringAndConfigAsZendConfig()
    {
        $config = new Zend_Config(['barHeight' => 123]);
        $barcode = Zend_Barcode::makeBarcode('code25', $config);
        $this->assertTrue($barcode instanceof Zend_Barcode_Object_Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsZendConfig()
    {
        $config = new Zend_Config(
                ['barcode' => 'code25' ,
                        'barcodeParams' => [
                                'barHeight' => 123]]);
        $barcode = Zend_Barcode::makeBarcode($config);
        $this->assertTrue($barcode instanceof Zend_Barcode_Object_Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsZendConfigButNoBarcodeParameter()
    {
        $this->expectException(Zend_Barcode_Exception::class);
        $config = new Zend_Config(
                [
                        'barcodeParams' => [
                                'barHeight' => 123]]);
        $barcode = Zend_Barcode::makeBarcode($config);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsZendConfigAndBadBarcodeParameters()
    {
        $this->expectException(Zend_Barcode_Exception::class);
        $barcode = Zend_Barcode::makeBarcode('code25', null);
    }

    public function testBarcodeObjectFactoryWithNamespace()
    {
        require_once dirname(__FILE__) . '/Object/_files/BarcodeNamespace.php';
        $barcode = Zend_Barcode::makeBarcode('error',
                [
                        'barcodeNamespace' => 'My_Namespace']);
        $this->assertTrue($barcode instanceof My_Namespace_Error);
    }

    public function testBarcodeObjectFactoryWithNamespaceButWithoutExtendingObjectAbstract()
    {
        $this->expectException(Zend_Barcode_Exception::class);
        require_once dirname(__FILE__) . '/Object/_files/BarcodeNamespaceWithoutExtendingObjectAbstract.php';
        $barcode = Zend_Barcode::makeBarcode('error',
                [
                        'barcodeNamespace' => 'My_Namespace_Other']);
    }

    public function testBarcodeObjectFactoryWithUnexistantBarcode()
    {
        $this->expectException(Zend_Exception::class);
        $barcode = Zend_Barcode::makeBarcode('zf123', []);
    }

    public function testBarcodeRendererFactoryWithExistingBarcodeRenderer()
    {
        $this->_checkGDRequirement();

        $renderer = new Zend_Barcode_Renderer_Image();
        $generatedBarcode = Zend_Barcode::makeRenderer($renderer);
        $this->assertSame($renderer, $generatedBarcode);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsString()
    {
        $this->_checkGDRequirement();

        $renderer = Zend_Barcode::makeRenderer('image');
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsStringAndConfigAsArray()
    {
        $this->_checkGDRequirement();

        $renderer = Zend_Barcode::makeRenderer('image', ['imageType' => 'gif']);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertSame('gif', $renderer->getImageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsStringAndConfigAsZendConfig()
    {
        $this->_checkGDRequirement();

        $config = new Zend_Config(['imageType' => 'gif']);
        $renderer = Zend_Barcode::makeRenderer('image', $config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertSame('gif', $renderer->getimageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsZendConfig()
    {
        $this->_checkGDRequirement();

        $config = new Zend_Config(
                ['renderer' => 'image' ,
                        'rendererParams' => [
                                'imageType' => 'gif']]);
        $renderer = Zend_Barcode::makeRenderer($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertSame('gif', $renderer->getimageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsZendConfigButNoBarcodeParameter()
    {
        $this->expectException(Zend_Barcode_Exception::class);
        $config = new Zend_Config(
                [
                        'rendererParams' => [
                                'imageType' => 'gif']]);
        $renderer = Zend_Barcode::makeRenderer($config);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsZendConfigAndBadBarcodeParameters()
    {
        $this->expectException(Zend_Barcode_Exception::class);
        $renderer = Zend_Barcode::makeRenderer('image', null);
    }

    public function testBarcodeRendererFactoryWithNamespace()
    {
        $this->_checkGDRequirement();

        require_once dirname(__FILE__) . '/Renderer/_files/RendererNamespace.php';
        $renderer = Zend_Barcode::makeRenderer('image',
                [
                        'rendererNamespace' => 'My_Namespace']);
        $this->assertTrue($renderer instanceof My_Namespace_Image);
    }

    public function testBarcodeFactoryWithNamespaceButWithoutExtendingRendererAbstract()
    {
        $this->expectException(Zend_Barcode_Exception::class);
        require_once dirname(__FILE__) . '/Renderer/_files/RendererNamespaceWithoutExtendingRendererAbstract.php';
        $renderer = Zend_Barcode::makeRenderer('image',
                [
                        'rendererNamespace' => 'My_Namespace_Other']);
    }

    public function testBarcodeRendererFactoryWithUnexistantRenderer()
    {
        $this->expectException(Zend_Exception::class);
        $renderer = Zend_Barcode::makeRenderer('zend', []);
    }

    public function testProxyBarcodeRendererDrawAsImage()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                'GD extension is required to run this test');
        }
        $resource = Zend_Barcode::draw('code25', 'image');
        if (PHP_VERSION_ID < 80000) {
            $this->assertTrue(gettype($resource) == 'resource', 'Image must be a resource');
            $this->assertTrue(get_resource_type($resource) == 'gd', 'Image must be a GD resource');
        } else {
            $this->assertTrue(get_class($resource) === 'GdImage');
        }
    }

    public function testProxyBarcodeRendererDrawAsPdf()
    {
        Zend_Barcode::setBarcodeFont(dirname(__FILE__) . '/Object/_fonts/Vera.ttf');
        $resource = Zend_Barcode::draw('code25', 'pdf');
        $this->assertTrue($resource instanceof Zend_Pdf);
        Zend_Barcode::setBarcodeFont('');
    }

    public function testProxyBarcodeObjectFont()
    {
        Zend_Barcode::setBarcodeFont('my_font.ttf');
        $barcode = new Zend_Barcode_Object_Code25();
        $this->assertSame('my_font.ttf', $barcode->getFont());
        Zend_Barcode::setBarcodeFont('');
    }

    protected function _checkGDRequirement()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('This test requires the GD extension');
        }
    }
}
