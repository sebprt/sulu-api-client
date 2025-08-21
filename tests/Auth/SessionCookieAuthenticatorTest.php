<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Auth;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Auth\SessionCookieAuthenticator;

class SessionCookieAuthenticatorTest extends TestCase
{
    private RequestInterface $request;

    protected function setUp(): void
    {
        $this->request = $this->createMock(RequestInterface::class);
    }

    public function testImplementsRequestAuthenticatorInterface(): void
    {
        $authenticator = new SessionCookieAuthenticator('session', 'value');
        $this->assertInstanceOf(RequestAuthenticatorInterface::class, $authenticator);
    }

    public function testConstructorWithSingleCookieNameAndValue(): void
    {
        $authenticator = new SessionCookieAuthenticator('session_id', 'abc123');
        $this->assertInstanceOf(SessionCookieAuthenticator::class, $authenticator);
    }

    public function testConstructorWithSingleCookieNameOnly(): void
    {
        $authenticator = new SessionCookieAuthenticator('session_id');
        $this->assertInstanceOf(SessionCookieAuthenticator::class, $authenticator);
    }

    public function testConstructorWithCookieArray(): void
    {
        $cookies = [
            'session_id' => 'abc123',
            'csrf_token' => 'xyz789',
        ];
        $authenticator = new SessionCookieAuthenticator($cookies);
        $this->assertInstanceOf(SessionCookieAuthenticator::class, $authenticator);
    }

    public function testConstructorWithEmptyStringCookieName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cookie name must not be empty');
        
        new SessionCookieAuthenticator('', 'value');
    }

    public function testConstructorWithEmptyArrayCookieName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cookie name must not be empty');
        
        new SessionCookieAuthenticator(['' => 'value', 'valid' => 'test']);
    }

    public function testConstructorWithEmptyCookieArray(): void
    {
        $authenticator = new SessionCookieAuthenticator([]);
        $this->assertInstanceOf(SessionCookieAuthenticator::class, $authenticator);
    }

    public function testAuthenticateWithSingleCookie(): void
    {
        $authenticator = new SessionCookieAuthenticator('session_id', 'abc123');
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn([]);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'session_id=abc123')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithSingleCookieNoValue(): void
    {
        $authenticator = new SessionCookieAuthenticator('session_id');
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn([]);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'session_id=')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithMultipleCookies(): void
    {
        $cookies = [
            'session_id' => 'abc123',
            'csrf_token' => 'xyz789',
        ];
        $authenticator = new SessionCookieAuthenticator($cookies);
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn([]);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'session_id=abc123; csrf_token=xyz789')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithEmptyCookieArray(): void
    {
        $authenticator = new SessionCookieAuthenticator([]);
        
        $this->request->expects($this->never())
            ->method('getHeader');
            
        $this->request->expects($this->never())
            ->method('withHeader');
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithExistingCookieHeader(): void
    {
        $authenticator = new SessionCookieAuthenticator('new_cookie', 'new_value');
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn(['existing_cookie=existing_value']);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'existing_cookie=existing_value; new_cookie=new_value')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithMultipleExistingCookieHeaders(): void
    {
        $authenticator = new SessionCookieAuthenticator('new_cookie', 'new_value');
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn(['cookie1=value1', 'cookie2=value2']);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'cookie1=value1; cookie2=value2; new_cookie=new_value')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithExistingCookieHeaderEndingWithSemicolon(): void
    {
        $authenticator = new SessionCookieAuthenticator('new_cookie', 'new_value');
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn(['existing_cookie=existing_value; ']);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'existing_cookie=existing_value; new_cookie=new_value')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateUrlEncodesSpecialCharacters(): void
    {
        $authenticator = new SessionCookieAuthenticator('cookie name', 'value with spaces & symbols=');
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn([]);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'cookie%20name=value%20with%20spaces%20%26%20symbols%3D')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithNullValue(): void
    {
        $authenticator = new SessionCookieAuthenticator('test_cookie', null);
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn([]);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'test_cookie=')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithIntegerValue(): void
    {
        $cookies = ['user_id' => 12345];
        $authenticator = new SessionCookieAuthenticator($cookies);
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn([]);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'user_id=12345')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateSkipsEmptyNameInArray(): void
    {
        // This tests the edge case where empty names are filtered out in the authenticate method
        $cookies = ['valid_cookie' => 'value'];
        $authenticator = new SessionCookieAuthenticator($cookies);
        
        // Use reflection to add an empty name cookie to test the filtering logic
        $reflection = new \ReflectionClass($authenticator);
        $property = $reflection->getProperty('cookies');
        $property->setAccessible(true);
        $property->setValue($authenticator, ['' => 'should_be_skipped', 'valid_cookie' => 'value']);
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn([]);
            
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', 'valid_cookie=value')
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithAllEmptyNamesReturnsOriginalRequest(): void
    {
        $authenticator = new SessionCookieAuthenticator(['valid' => 'test']);
        
        // Use reflection to set all cookies to have empty names
        $reflection = new \ReflectionClass($authenticator);
        $property = $reflection->getProperty('cookies');
        $property->setAccessible(true);
        $property->setValue($authenticator, ['' => 'value1', '' => 'value2']);
        
        $this->request->expects($this->never())
            ->method('getHeader');
            
        $this->request->expects($this->never())
            ->method('withHeader');
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }

    public function testAuthenticateWithComplexScenario(): void
    {
        $cookies = [
            'session_id' => 'abc123def456',
            'user_pref' => 'dark_mode=true&lang=en',
            'csrf_token' => 'xyz789!@#',
        ];
        $authenticator = new SessionCookieAuthenticator($cookies);
        
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Cookie')
            ->willReturn(['analytics=ga123', 'tracking=utm456; ']);
            
        $expectedCookie = 'analytics=ga123; tracking=utm456; ' .
                         'session_id=abc123def456; ' .
                         'user_pref=dark_mode%3Dtrue%26lang%3Den; ' .
                         'csrf_token=xyz789%21%40%23';
        
        $this->request->expects($this->once())
            ->method('withHeader')
            ->with('Cookie', $expectedCookie)
            ->willReturn($this->request);
            
        $result = $authenticator->authenticate($this->request);
        $this->assertSame($this->request, $result);
    }
}