<?php

namespace Sulu\ApiClient\Tests\Http;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Http\ClientOptions;

class ClientOptionsTest extends TestCase
{
    public function testDefaultValues()
    {
        $options = new ClientOptions();
        
        $this->assertEquals(30, $options->getTimeout());
        $this->assertTrue($options->getVerifySsl());
    }
    
    public function testSetTimeout()
    {
        $options = new ClientOptions();
        
        $result = $options->setTimeout(60);
        
        $this->assertSame($options, $result);
        $this->assertEquals(60, $options->getTimeout());
    }
    
    public function testSetVerifySsl()
    {
        $options = new ClientOptions();
        
        $result = $options->setVerifySsl(false);
        
        $this->assertSame($options, $result);
        $this->assertFalse($options->getVerifySsl());
    }
}