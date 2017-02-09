<?php
/**
 * TestCase
 * @version     0.0.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance\Tests\unit;

use yii\helpers\ArrayHelper;

/**
 * This is the base class for all yii framework unit tests.
 */
abstract class TestCase extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @inheritdoc
     */
    protected function _before()
    {
        parent::_before();
        $this->mockApplication();
    }

    /**
     * Clean up after test.
     * By default the application created with [[mockApplication]] will be destroyed.
     */
    protected function _after()
    {
        parent::_after();
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     *
     * @param array  $config   The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($config = [], $appClass = '\yii\console\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id'         => 'testapp',
            'basePath'   => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn'   => 'sqlite::memory:',
                ],
            ]
        ], $config));
    }

    protected function mockWebApplication($config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id'         => 'testapp',
            'basePath'   => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile'          => __DIR__ . '/index.php',
                    'scriptUrl'           => '/index.php',
                ],
            ]
        ], $config));
    }

    protected function getVendorPath()
    {
        $vendor = dirname(dirname(__DIR__)) . '/vendor';
        if (!is_dir($vendor)) {
            $vendor = dirname(dirname(dirname(dirname(__DIR__))));
        }

        return $vendor;
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        if (\Yii::$app && \Yii::$app->has('session', true)) {
            \Yii::$app->session->close();
        }
        \Yii::$app = null;
    }

    /**
     * Asserting two strings equality ignoring line endings
     *
     * @param string $expected
     * @param string $actual
     */
    protected function assertEqualsWithoutLE($expected, $actual)
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual   = str_replace("\r\n", "\n", $actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Invokes a inaccessible method
     *
     * @param       $object
     * @param       $method
     * @param array $args
     * @param bool  $revoke whether to make method inaccessible after execution
     *
     * @return mixed
     * @since 2.0.11
     */
    protected function invokeMethod($object, $method, $args = [], $revoke = true)
    {
        $reflection = new \ReflectionClass($object->className());
        $method     = $reflection->getMethod($method);
        $method->setAccessible(true);
        $result = $method->invokeArgs($object, $args);
        if ($revoke) {
            $method->setAccessible(false);
        }

        return $result;
    }

    /**
     * Sets an inaccessible object property to a designated value
     *
     * @param      $object
     * @param      $propertyName
     * @param      $value
     * @param bool $revoke whether to make property inaccessible after setting
     *
     * @since 2.0.11
     */
    protected function setInaccessibleProperty($object, $propertyName, $value, $revoke = true)
    {
        $class = new \ReflectionClass($object);
        while (!$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($value);
        if ($revoke) {
            $property->setAccessible(false);
        }
    }

    /**
     * Gets an inaccessible object property
     *
     * @param      $object
     * @param      $propertyName
     * @param bool $revoke whether to make property inaccessible after getting
     *
     * @return mixed
     */
    protected function getInaccessibleProperty($object, $propertyName, $revoke = true)
    {
        $class = new \ReflectionClass($object);
        while (!$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $result = $property->getValue($object);
        if ($revoke) {
            $property->setAccessible(false);
        }

        return $result;
    }
}
