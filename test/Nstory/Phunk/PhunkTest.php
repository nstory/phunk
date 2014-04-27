<?php
namespace Nstory\Phunk;

use Nstory\Phunk\Phunk as F;

class PhunkTest extends \PHPUnit_Framework_TestCase
{
    public function test_chunk()
    {
        $l = F::chunk(2, [1,2,3,4])->asArray();
        $this->assertEquals([[1,2], [3,4]], $l);
    }

    public function test_filter()
    {
        $l = F::filter(function($v, $k) {
            return ($k == 'a' || $v == 2);
        }, ['a' => 1, 'b' => 2, 'c' => 3])->asArray();
        $this->assertEquals(['a' => 1, 'b' => 2], $l);
    }

    public function test_implode()
    {
        $s = F::implode(',', ['a', 'b', 'c']);
        $this->assertEquals('a,b,c', $s);
    }

    public function test_in()
    {
        $this->assertTrue(F::in(2, [1,2,3]));
        $this->assertFalse(F::in(2, [1,3,4]));
    }

    /**
     * @return static
     */
    public function test_ksort()
    {
        $l = F::ksort(function($a, $b) {
            return $a - $b;
        }, [1 => true, 3 => true, 2 => true])->asArray();
        $this->assertEquals([1=>true, 2=>true, 3=>true], $l);
    }

    public function test_map_static()
    {
        $l = F::map(function($v, $k) {
            return "$k.$v";
        }, [1,2,3])->asArray();
        $this->assertEquals(['0.1', '1.2', '2.3'], $l);
    }

    public function test_map_instance()
    {
        $l = F::wrap([1,2,3])
            ->map(function($v, $k) {
                return "$k.$v";
            })->asArray();
        $this->assertEquals(['0.1', '1.2', '2.3'], $l);
    }

    public function test_reduce()
    {
        $r = F::reduce(function($carry, $item) {
            return ($carry - $item);
        }, 1, [2,3,4]);
        // ((1-2)-3)-4 = -8
        $this->assertEquals(-8, $r);
    }

    public function test_reverse()
    {
        $r = F::reverse([1,2,3])->asArray();
        $this->assertEquals([3,2,1], $r);
    }

    public function test_sort()
    {
        $l = F::sort(function($a, $b) {
            return $a - $b;
        }, [2,3,1])->asArray();
        $this->assertEquals([1,2,3], $l);
    }

    public function test_sum()
    {
        $sum = F::sum([1,2,3]);
        $this->assertEquals(6, $sum);
    }

    public function test_tap()
    {
        F::wrap([1,2,3])->tap(function($arr) use(&$actual) {
            $actual = $arr;
        });
        $this->assertEquals([1,2,3], $actual);
    }
}
