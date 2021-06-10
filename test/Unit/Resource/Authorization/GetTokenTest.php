<?php

namespace zaporylie\Vipps\Tests\Unit\Resource\Authorization;

use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use zaporylie\Vipps\Model\Authorization\ResponseGetToken;
use zaporylie\Vipps\Resource\Authorization\GetToken;
use zaporylie\Vipps\Resource\HttpMethod;
use zaporylie\Vipps\Tests\Unit\Resource\ResourceTestBase;

class GetTokenTest extends ResourceTestBase
{

    /**
     * @var \zaporylie\Vipps\Resource\Authorization\GetToken
     */
    protected $resource;

    protected function setUp() : void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->resource = $this->getMockBuilder(GetToken::class)
            ->setConstructorArgs([$this->vipps, 'test_subscription_key', 'test_client_secret'])
            ->disallowMockingUnknownTypes()
            ->setMethods(['makeCall'])
            ->getMock();

        $this->resource
            ->expects($this->any())
            ->method('makeCall')
            ->will($this->returnValue(new Response(200, [], stream_for(json_encode([])))));
    }

    /**
     * @covers \zaporylie\Vipps\Resource\Authorization\GetToken::__construct
     * @covers \zaporylie\Vipps\Resource\Authorization\GetToken::getHeaders()
     */
    public function testHeaders()
    {
        $headers = $this->resource->getHeaders();
        $this->assertArrayHasKey('client_id', $headers);
        $this->assertEquals('test_client_id', $headers['client_id']);
        $this->assertArrayHasKey('client_secret', $headers);
        $this->assertEquals('test_client_secret', $headers['client_secret']);
    }

    /**
     * @covers \zaporylie\Vipps\Resource\Authorization\GetToken::getBody()
     */
    public function testBody()
    {
        $this->assertEmpty($this->resource->getBody());
    }

    /**
     * @covers \zaporylie\Vipps\Resource\Authorization\GetToken::getMethod()
     */
    public function testMethod()
    {
        $this->assertEquals(HttpMethod::POST, $this->resource->getMethod());
    }

    /**
     * @covers \zaporylie\Vipps\Resource\Authorization\GetToken::getPath()
     */
    public function testPath()
    {
        $this->assertEquals('/accessToken/get', $this->resource->getPath());
    }

    /**
     * @covers \zaporylie\Vipps\Resource\Authorization\GetToken::call()
     */
    public function testCall()
    {
        $this->assertInstanceOf(ResponseGetToken::class, $response = $this->resource->call());
        $this->assertEquals(new ResponseGetToken(), $response);
    }
}
