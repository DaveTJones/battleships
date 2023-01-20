<?php declare(strict_types=1);

include 'battleships.php';
use PHPUnit\Framework\TestCase;

final class DestroyerTest extends TestCase
{
    public function test_health()
    {
        $d = new Destroyer();
        $this->assertSame(100, $d->get_health());
	}
	
	public function test_is_hit()
	{
        $d = new Destroyer();
		$d->is_hit(10);
		$this->assertSame(90, $d->get_health());	
	}
	
	public function test_sinks()
	{
        $d = new Destroyer();
		$d->is_hit(100);
		$this->assertSame(0, $d->get_health());
	}
	
	public function test_self_hit() 
	{
		$d = new Destroyer();
		$result = $d->attacks($d);
		$this->assertSame(-1, $result);
	}
	
	public function test_hit_destroyed_vessel()
	{
		$d = new Destroyer();
		$c = new Carrier();
		$d->is_hit(100);
		$result = $c->attacks($d);
		$this->assertSame(-1, $result);
	}
	
	public function test_good_recruit()
	{
		$d = new Destroyer();
		$result = $d->recruit("Blackbeard");
		$this->assertSame(0, $result);
	}
	
	public function test_bad_recruit()
	{
		$d = new Destroyer();
		$result = $d->recruit("Bob");
		$this->assertSame(-1, $result);
	}
	
}
?>