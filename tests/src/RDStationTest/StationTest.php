<?php

namespace RdStationTest;

use RDStation\RDStation;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Request;

class RDStationTest extends \PHPUnit_Framework_TestCase
{
    const  API_URL = 'http://www.rdstation.com.br/api/1.2';

    public function testParametersInConstruct()
    {
        $options = [
            'token' => 'xyd',
            'identifier' => 'foo'
        ];

        $station = new RDStation($options);

        $this->assertEquals($options, $station->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Option token not found
     */
    public function testExceptionParameterToken()
    {
        $options = [
            'identifier' => 'foo'
        ];

        (new RDStation($options));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Option identifier not found
     */
    public function testExceptionParameterIdentifier()
    {
        $options = [
            'token' => 'foo'
        ];

        (new RDStation($options));
    }

    public function testMethodGetClient()
    {
        $options = [
            'token' => 'xyd',
            'identifier' => 'foo'
        ];

        $station = new RDStation($options);

        $getClient = $this->invokeMethod($station, 'getClient');

        $this->assertInstanceOf(Client::class, $getClient);
    }


    public function testMethodExecutByPOST()
    {
        $expected = 'OK';

        $options = [
            'token' => 'xyd',
            'identifier' => 'foo'
        ];

        $uri = '/conversions';

        $data = array_merge($options, ['foo' => 'bar']);

        $response = $this->mockResponse($expected);
        $client = $this->mockClient($response, $uri, Request::METHOD_POST, $data);

        $station = $this->getMockBuilder(RDStation::class)
            ->setConstructorArgs([$options])
            ->setMethods(['getClient'])
            ->getMock();

        $station->expects($this->any())
            ->method('getClient')
            ->will($this->returnValue($client));

        $response = $station->execute($uri, Request::METHOD_POST, $data, []);

        $this->assertEquals($expected, $response);
    }

    public function testMethodExecutByPUT()
    {
        $expected = 'OK';

        $options = [
            'token' => 'xyd',
            'identifier' => 'foo'
        ];

        $uri = '/leads';

        $data = array_merge($options, ['foo' => 'bar']);

        $response = $this->mockResponse($expected);
        $client = $this->mockClient($response, $uri, Request::METHOD_PUT, $data);

        $station = $this->getMockBuilder(RDStation::class)
            ->setConstructorArgs([$options])
            ->setMethods(['getClient'])
            ->getMock();

        $station->expects($this->any())
            ->method('getClient')
            ->will($this->returnValue($client));

        $response = $station->execute($uri, Request::METHOD_PUT, $data, []);

        $this->assertEquals($expected, $response);
    }

    public function testMethodExecutByGET()
    {
        $expected = 'OK';

        $options = [
            'token' => 'xyd',
            'identifier' => 'foo'
        ];

        $uri = '/conversions';

        $data = array_merge($options, ['foo' => 'bar']);

        $response = $this->mockResponse($expected);
        $client = $this->mockClient($response, $uri, Request::METHOD_GET, $data);

        $station = $this->getMockBuilder(RDStation::class)
            ->setConstructorArgs([$options])
            ->setMethods(['getClient'])
            ->getMock();

        $station->expects($this->any())
            ->method('getClient')
            ->will($this->returnValue($client));

        $response = $station->execute($uri, Request::METHOD_GET, $data, []);

        $this->assertEquals($expected, $response);
    }

    public function mockResponse($expected)
    {
        $mockResponse = $this->getMockBuilder('\Zend\Http\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getContent'])
            ->getMock();

        $mockResponse->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($expected));

        return $mockResponse;
    }

    public function mockClient($response, $uri, $method, Array $data = [])
    {
        $data['identificador'] = $data['identifier'];
        unset($data['identifier']);

        $mockClient = $this->getMockBuilder('\Zend\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(['setAdapter', 'setMethod', 'setUri', 'send', 'setParameterPost', 'setParameterGet'])
            ->getMock();

        $mockClient->expects($this->any())
            ->method('setAdapter')
            ->with($this->equalTo(new Curl()))
            ->will($this->returnSelf());

        $mockClient->expects($this->any())
            ->method('setUri')
            ->with($this->equalTo(self::API_URL.$uri))
            ->will($this->returnSelf());

        $mockClient->expects($this->any())
            ->method('setMethod')
            ->with($this->equalTo($method))
            ->will($this->returnSelf());

        $mockClient->expects($this->any())
            ->method('setParameterPost')
            ->with($this->equalTo($data))
            ->will($this->returnSelf());

        $mockClient->expects($this->any())
            ->method('setParameterGet')
            ->with($this->equalTo($data))
            ->will($this->returnSelf());

        $mockClient->expects($this->any())
            ->method('send')
            ->will($this->returnValue($response));

        return $mockClient;
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
