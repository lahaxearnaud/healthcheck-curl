<?php

namespace Alahaxe\HealthCheckBundle\Checks\Curl\Tests;

use Alahaxe\HealthCheck\Contracts\CheckStatusInterface;
use Alahaxe\HealthCheckBundle\Checks\Curl\CurlCheck;
use PHPUnit\Framework\TestCase;

class CurlCheckTest extends TestCase
{
    public function testCurlOkWithDefaultConfiguration()
    {
        $name = uniqid('test');
        $check = new CurlCheck(
            'https://httpbin.org/get',
            $name
        );
        $result = $check->check();
        $this->assertEquals(CheckStatusInterface::STATUS_OK, $result->getStatus());
        $this->assertEquals($name, $result->getAttributeName());
        $this->assertEquals(get_class($check), $result->getCheckerClass());
    }

    public function testCurlKoWithDefaultConfiguration()
    {
        $name = uniqid('test');
        $check = new CurlCheck(
            'https://httpbin.org/status/503',
            $name
        );
        $result = $check->check();
        $this->assertEquals(CheckStatusInterface::STATUS_INCIDENT, $result->getStatus());
        $this->assertEquals($name, $result->getAttributeName());
        $this->assertEquals(get_class($check), $result->getCheckerClass());
    }

    public function testCurlTimeout()
    {
        $name = uniqid('test');
        $check = new CurlCheck(
            'https://httpbin.org/delay/2',
            $name,
            1,
            1
        );
        $result = $check->check();
        $this->assertEquals(CheckStatusInterface::STATUS_INCIDENT, $result->getStatus());
        $this->assertEquals($name, $result->getAttributeName());
        $this->assertEquals(get_class($check), $result->getCheckerClass());
    }

    public function testCurlTimeoutOnlyWarning()
    {
        $name = uniqid('test');
        $check = new CurlCheck(
            'https://httpbin.org/delay/2',
            $name,
            1,
            1,
            true,
            true
        );
        $result = $check->check();
        $this->assertEquals(CheckStatusInterface::STATUS_WARNING, $result->getStatus());
        $this->assertEquals($name, $result->getAttributeName());
        $this->assertEquals(get_class($check), $result->getCheckerClass());
    }

    public function testCurlBadSSLCert()
    {
        $name = uniqid('test');
        $check = new CurlCheck(
            'https://expired.badssl.com/',
            $name,
        );
        $result = $check->check();
        $this->assertEquals(CheckStatusInterface::STATUS_INCIDENT, $result->getStatus());
        $this->assertEquals($name, $result->getAttributeName());
        $this->assertEquals(get_class($check), $result->getCheckerClass());
    }

    public function testCurlBadSSLCertWithTrustSsl()
    {
        $name = uniqid('test');
        $check = new CurlCheck(
            'https://expired.badssl.com/',
            $name,
            1,
            1,
            true,
            false,
            true
        );
        $result = $check->check();
        $this->assertEquals(CheckStatusInterface::STATUS_INCIDENT, $result->getStatus());
        $this->assertEquals($name, $result->getAttributeName());
        $this->assertEquals(get_class($check), $result->getCheckerClass());
    }
}
