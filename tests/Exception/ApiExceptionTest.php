<?php

namespace Sulu\ApiClient\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Exception\ApiException;

class ApiExceptionTest extends TestCase
{
    public function testConstructor()
    {
        $message = 'Test error message';
        $code = 404;
        $previous = new \Exception('Previous exception');
        $responseData = ['error' => 'Not found'];
        
        $exception = new ApiException($message, $code, $previous, $responseData);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertSame($responseData, $exception->getResponseData());
    }
    
    public function testConstructorWithoutResponseData()
    {
        $exception = new ApiException('Test message', 500);
        
        $this->assertNull($exception->getResponseData());
    }
    
    public function testConstructorWithoutOptionalParameters()
    {
        $exception = new ApiException();
        
        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
        $this->assertNull($exception->getResponseData());
    }
}