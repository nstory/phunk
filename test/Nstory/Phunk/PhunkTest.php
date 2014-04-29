<?php
namespace Nstory\Phunk;

use Nstory\Phunk\Phunk as F;

class PhunkTest extends \PHPUnit_Framework_TestCase
{
    public function test_chunk()
    {
        $l = F::chunk([1,2,3,4], 2)->asArray();
        $this->assertEquals([[1,2], [3,4]], $l);
    }

    public function test_chunk_preserve_keys()
    {
        $l = F::chunk(['a' => 1, 'b' => 2, 'c' => 3], 2, true)->asArray();
        $this->assertEquals([['a' => 1, 'b' => 2], ['c' => 3]], $l);
    }

    public function test_filter()
    {
        $l = F::filter([1,2,3], function($e) {
            return $e != 2;
        })->asArray();
        $this->assertEquals([0 => 1, 2 => 3], $l);
    }

    public function test_filter_default()
    {
        $l = F::filter([1, null, 2])->asArray();
        $this->assertEquals([0 => 1, 2 => 2], $l);
    }

    public function test_implode()
    {
        $s = F::implode(['a', 'b', 'c'], ',');
        $this->assertEquals('a,b,c', $s);
    }

    public function test_in()
    {
        $this->assertTrue(F::in([1,2,3], 2));
        $this->assertFalse(F::in([1,3,4], 2));
    }

    public function test_in_strict()
    {
        $this->assertTrue(F::in([1,2,3], 2, true));
        $this->assertFalse(F::in([1,2,3], "2", true));
    }

    public function test_keys()
    {
        $l = F::keys(['a' => 1, 'b' => 2])->asArray();
        $this->assertEquals(['a', 'b'], $l);
    }

    /**
     * @return static
     */
    public function test_ksort()
    {
        $l = F::ksort([1 => true, 3 => true, 2 => true],
            function($a, $b) {
                return $a - $b;
        })->asArray();
        $this->assertEquals([1=>true, 2=>true, 3=>true], $l);
    }

    public function test_ksort_default()
    {
        $l = F::ksort([1 => true, 3 => true, 2 => true])->asArray();
        $this->assertEquals([1=>true, 2=>true, 3=>true], $l);
    }

    public function test_map_static()
    {
        $l = F::map([1,2,3],
            function($v, $k) {
                return "$k.$v";
        })->asArray();
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

    public function test_path()
    {
        $this->assertInstanceOf(
            'Nstory\Phunk\Path',
            F::path()
        );
    }

    public function test_reduce()
    {
        $r = F::reduce([2,3,4],
            function($carry, $item) {
                return ($carry - $item);
            }, 1);
        // ((1-2)-3)-4 = -8
        $this->assertEquals(-8, $r);
    }

    public function test_reverse()
    {
        $r = F::reverse([1,2,3])->asArray();
        $this->assertEquals([3,2,1], $r);
    }

    public function test_reverse_preserve_keys()
    {
        $l = F::reverse([1,2,3], true)->asArray();
        $this->assertEquals([2=>3, 1=>2, 0=>1], $l);
    }

    public function test_sort()
    {
        $l = F::sort([2,3,1],
            function($a, $b) {
                return $b - $a;
            }, [2,3,1])->asArray();
        $this->assertEquals([3,2,1], $l);
    }

    public function test_sort_default()
    {
        // uses sort() if no comparator is supplied
        $l = F::sort([2,3,1])->asArray();
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

    public function test_unique()
    {
        $l = F::unique([1,1,3,1])->asArray();
        $this->assertEquals([0=>1,2=>3], $l);
    }

    public function test_values()
    {
        $l = F::values([1,2,3])->asArray();
        $this->assertEquals([1,2,3], $l);
    }

    public function test_simplexml()
    {
        $sxe = new \SimpleXMLElement('<a><b>1</b><b>2</b></a>');
        $this->assertEquals(
            ["1", "2"],
            F::map($sxe, function($e) {
                return (string)$e;
            })->asArray()
        );
    }
}
