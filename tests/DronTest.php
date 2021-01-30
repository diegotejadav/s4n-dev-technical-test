<?php

/**
 * Drones deliveries test cases
 */
use PHPUnit\Framework\TestCase;
// include __DIR__ . '/../app/models/Dron.php'; // include not required due to composer.json autoload config

class DronTest extends TestCase
{
    private $dron;

    public function setup(): void
    {
        $this->dron = new Dron(['AAAAIAA'], 3, 10, 'outTest.txt');
    }

    public function testDoUnavailableInstruction()
    {
        $this->expectException(InvalidArgumentException::class);
        // The only available options are: 'A', 'I, 'D'
        $this->dron->processInstruction('Z');
    }

    public function testDoAvailableInstruction()
    {
        // The only available options are: 'A', 'I, 'D'
        $result = $this->dron->processInstruction('A');
        $this->assertTrue($result);
    }

    public function testDronFinalPosition()
    {
        $this->dron->deliverPath('AAAAIAA');
        $this->assertEquals($this->dron->getCurrentPosition(), [-2, 4, 'W']);
    }

    public function testDronMaximumCoverage()
    {
        $this->expectException(Exception::class);
        // Set a long path to deliver (> 10 squares)
        $this->dron->deliverPath('AAAAAAAAAAA');
    }
    
    public function testDronValidDeliveryPath()
    {
        $result = $this->dron->deliverPath('AAAAAAIAAAAA');
        $this->assertTrue($result);        
    }
}

?>