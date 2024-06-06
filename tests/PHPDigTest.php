<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use webricks\PHPDig\PHPDig;
use webricks\PHPDig\RecordType;


/**
 * PHPDigTest
 *
 * @author j.aloy :: yoonic :: Linked applications
 */
final class PHPDigTest extends TestCase {
    public function testDigA(): void {
        $dig = new PHPDig();
        $res = $dig->query('a.root-servers.net', RecordType::A);
        
        $this->assertSame(1, $this->count());
        $this->assertSame('a.root-servers.net.', $res[0]['name']);
        $this->assertTrue(is_numeric($res[0]['ttl']));
        $this->assertTrue(intval($res[0]['ttl']) > 0);
        $this->assertSame('IN', $res[0]['class']);
        $this->assertSame('A', $res[0]['type']);
        $this->assertSame('198.41.0.4', $res[0]['record']);
    }
    
    public function testDigAAAA(): void {
        $dig = new PHPDig();
        $res = $dig->query('a.root-servers.net', RecordType::AAAA);

        $this->assertSame(1, $this->count());
        $this->assertSame('a.root-servers.net.', $res[0]['name']);
        $this->assertTrue(is_numeric($res[0]['ttl']));
        $this->assertTrue(intval($res[0]['ttl']) > 0);
        $this->assertSame('IN', $res[0]['class']);
        $this->assertSame('AAAA', $res[0]['type']);
        $this->assertSame('2001:503:ba3e::2:30', $res[0]['record']);
    }
    
    public function testDigNonexistant(): void {
        $dig = new PHPDig();
        $res = $dig->query('lalala_iam_not_real_1235236757847547.com', RecordType::A);

        $this->assertSame([], $res);
    }
}
