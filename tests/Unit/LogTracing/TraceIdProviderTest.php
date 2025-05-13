<?php

declare(strict_types=1);

namespace Paysera\LoggingExtraBundle\Tests\Unit\LogTracing;

use Paysera\LoggingExtraBundle\Service\LogTracing\TraceIdProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class TraceIdProviderTest extends TestCase
{
    private const TRACE_ID_HEADER = 'X-Trace-Id';
    private const TRACE_ID_VALUE = '6ba7b810-9dad-11d1-80b4-00c04fd430c8'; 

    public function testShouldReturnTraceIdFromHeaderWhenRequestExists(): void
    {
        // Arrange
        $request = new Request();
        $request->headers->set(self::TRACE_ID_HEADER, self::TRACE_ID_VALUE);
        
        $requestStack = new RequestStack();
        $requestStack->push($request);
        
        $traceIdProvider = new TraceIdProvider($requestStack, self::TRACE_ID_HEADER);
        
        // Act
        $result = $traceIdProvider->getTraceId();
        
        // Assert
        $this->assertEquals(self::TRACE_ID_VALUE, $result);
    }
    
    public function testShouldGenerateUuidWhenNoRequestExists(): void
    {
        // Arrange
        $requestStack = new RequestStack();
        $traceIdProvider = new TraceIdProvider($requestStack, self::TRACE_ID_HEADER);
        
        // Act
        $result = $traceIdProvider->getTraceId();
        
        // Assert
        $this->assertNotNull($result);
        $this->assertTrue(\Ramsey\Uuid\Nonstandard\Uuid::isValid($result));
    }
    
    public function testShouldGenerateUuidWhenHeaderDoesNotExist(): void
    {
        // Arrange
        $request = new Request();
        
        $requestStack = new RequestStack();
        $requestStack->push($request);
        
        $traceIdProvider = new TraceIdProvider($requestStack, self::TRACE_ID_HEADER);
        
        // Act
        $result = $traceIdProvider->getTraceId();
        
        // Assert
        $this->assertNotNull($result);
        $this->assertTrue(\Ramsey\Uuid\Nonstandard\Uuid::isValid($result));
    }
    
    public function testShouldGenerateUuidWhenInvalidUuidInHeader(): void
    {
        // Arrange
        $request = new Request();
        $request->headers->set(self::TRACE_ID_HEADER, 'invalid-uuid');
        
        $requestStack = new RequestStack();
        $requestStack->push($request);
        
        $traceIdProvider = new TraceIdProvider($requestStack, self::TRACE_ID_HEADER);
        
        // Act
        $result = $traceIdProvider->getTraceId();
        
        // Assert
        $this->assertNotNull($result);
        $this->assertTrue(\Ramsey\Uuid\Nonstandard\Uuid::isValid($result));
    }
    
    public function testShouldResetTraceId(): void
    {
        // Arrange
        $requestStack = new RequestStack();
        $traceIdProvider = new TraceIdProvider($requestStack, self::TRACE_ID_HEADER);
        
        // Get the first trace ID
        $firstTraceId = $traceIdProvider->getTraceId();
        
        // Act
        $traceIdProvider->reset();
        
        // Get a new trace ID after reset
        $secondTraceId = $traceIdProvider->getTraceId();
        
        // Assert
        $this->assertNotNull($firstTraceId);
        $this->assertNotNull($secondTraceId);
        $this->assertNotEquals($firstTraceId, $secondTraceId, 'The trace ID should be different after reset');
    }
}
