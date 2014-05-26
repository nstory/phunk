<?php
namespace Nstory\Phunk;

use Nstory\Phunk\Phunk as F;

class PhunkTest extends \PHPUnit_Framework_TestCase
{
    public function test_chunk()
    {
        $this->assertEquals(
            [[1,2], [3,4]],
            F::chunk([1,2,3,4], 2)->asArray()
        );

        // preserve keys
        $this->assertEquals(
            [['a' => 1, 'b' => 2], ['c' => 3]],
            F::chunk(['a' => 1, 'b' => 2, 'c' => 3], 2, true)->asArray()
        );
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
        // implode an array
        $this->assertEquals(
            'a,b,c',
            F::implode(['a', 'b', 'c'], ',')
        );

        // implode a range (iterable)
        $this->assertEquals(
            '1,2,3',
            F::range(1,3)->implode(',')
        );
    }

    public function test_in()
    {
        $this->assertTrue(F::in([1,2,3], 2));
        $this->assertFalse(F::in([1,3,4], 2));

        // strict
        $this->assertTrue(F::in([1,2,3], 2, true));
        $this->assertFalse(F::in([1,2,3], "2", true));

        // iterable
        $this->assertTrue(F::range(1,5)->in(3));
        $this->assertFalse(F::range(1,5)->in(6));
    }

    public function test_keys()
    {
        $this->assertEquals(
            ['a', 'b'],
            F::keys(['a' => 1, 'b' => 2])->asArray()
        );

        // with an iterator
        $this->assertEquals(
            [0,1,2],
            F::range(1,3)->keys()->asArray()
        );
    }

    /**
     * @return static
     */
    public function test_ksort()
    {
        $cmp = function ($a, $b) { return $a - $b; };

        $this->assertEquals(
            [1=>true, 2=>true, 3=>true],
            F::ksort([1 => true, 3 => true, 2 => true], $cmp)->asArray()
        );

        // with an iterator
        $this->assertEquals(
            [2=>1, 1=>2, 0=>3],
            F::range(3,1)->ksort($cmp)->asArray()
        );
    }

    public function test_ksort_default()
    {
        $this->assertEquals(
            [1=>true, 2=>true, 3=>true],
            F::ksort([1 => true, 3 => true, 2 => true])->asArray()
        );

        // with an iterator
        $this->assertEquals(
            [2=>1, 1=>2, 0=>3],
            F::range(3,1)->ksort()->asArray()
        );
    }

    public function test_map()
    {
        $f = function($v, $k) { return "$k.$v"; };
        $this->assertEquals(
            ['0.1', '1.2', '2.3'],
            F::map([1,2,3], $f)->asArray()
        );

        // with an iterator
        $this->assertEquals(
            ['0.1', '1.2', '2.3'],
            F::range(1,3)->map($f)->asArray()
        );
    }

    public function test_min()
    {
        $this->assertEquals(
            3,
            F::wrap([20,3,5])->min()
        );

        // with an interator
        $this->assertEquals(
            3,
            F::range(3,10)->min()
        );
    }

    public function test_min_comparator()
    {
        $cmp = function($a, $b) {
            return strlen($a) - strlen($b);
        };
        $this->assertEquals(
            'a',
            F::min(['aa', 'a', 'aaa'], $cmp)
        );

        // with an iterator
        $this->assertEquals(
            '9',
            F::range(9,20)->min($cmp)
        );
    }

    public function test_min_empty_array()
    {
        $this->assertNull(
            F::min([])
        );
        $this->assertNull(
            F::min([], function() {})
        );
    }

    public function test_max()
    {
        $this->assertEquals(
            20,
            F::wrap([20,3,5])->max()
        );
        $this->assertEquals(
            20,
            F::range(1,20)->max()
        );
    }

    public function test_max_comparator()
    {
        $cmp = function($a, $b) {
            return strlen($a) - strlen($b);
        };
        $this->assertEquals(
            'aaa',
            F::max(['aa', 'a', 'aaa'], $cmp)
        );

        // with an iterator
        $this->assertEquals(
            '100',
            F::range(98,100)->max($cmp)
        );
    }

    public function test_max_empty_array()
    {
        $this->assertNull(
            F::max([])
        );
        $this->assertNull(
            F::max([], function() {})
        );
    }

    public function test_path()
    {
        $this->assertInstanceOf(
            'Nstory\Phunk\Path',
            F::path()
        );
    }

    public function test_range()
    {
        $this->assertEquals(
            [1,2,3],
            F::range(1,3)->asArray()
        );
        $this->assertEquals(
            [3,2,1],
            F::range(3,1)->asArray()
        );
        $this->assertEquals(
            [5,10,15],
            F::range(5,15,5)->asArray()
        );
        $this->assertEquals(
            [15,10,5],
            F::range(15,5,5)->asArray()
        );
        $this->assertEquals(
            [15,10,5],
            F::range(15,5,-5)->asArray()
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
        $this->assertEquals(
            [3,2,1],
            F::reverse([1,2,3])->asArray()
        );

        // with an iterator
        $this->assertEquals(
            [3,2,1],
            F::range(1,3)->reverse()->asArray()
        );
    }

    public function test_reverse_preserve_keys()
    {
        $l = F::reverse([1,2,3], true)->asArray();
        $this->assertEquals([2=>3, 1=>2, 0=>1], $l);
    }

    public function test_shuffle()
    {
        $examples = [
            F::wrap([1,2,3]),
            F::range(1,3)
        ];
        foreach ($examples as $ex) {
            $shuffled = $ex->shuffle()->asArray();
            $this->assertEquals(3, count($shuffled));
            $this->assertContains(1, $shuffled);
            $this->assertContains(2, $shuffled);
            $this->assertContains(3, $shuffled);
        }
    }

    public function test_slice()
    {
        $examples = [
            function() { return F::wrap([1,2,3]); },
            function() { return F::range(1,3); }
        ];

        foreach ($examples as $ex) {
            // no length
            $this->assertEquals(
                [2,3],
                $ex()->slice(1)->asArray()
            );

            // explicit length
            $this->assertEquals(
                [2],
                $ex()->slice(1,1)->asArray()
            );

            // preserve keys
            $this->assertEquals(
                [1 => 2],
                $ex()->slice(1,1,true)->asArray()
            );

            // negative start
            $this->assertEquals(
                [2,3],
                $ex()->slice(-2, 2)->asArray()
            );

            // negative length
            $this->assertEquals(
                [1,2],
                $ex()->slice(0, -1)->asArray()
            );
        }
    }

    public function test_sort()
    {
        $cmp = function($a, $b) { return $b - $a; };
        $this->assertEquals(
            [3,2,1],
            F::sort([2,3,1], $cmp)->asArray()
        );

        // with an iterator
        $this->assertEquals(
            [3,2,1],
            F::range(1,3)->sort($cmp)->asArray()
        );
    }

    public function test_sort_default()
    {
        // uses sort() if no comparator is supplied
        foreach ([F::range(1,3), F::wrap([1,2,3])] as $ex) {
            $this->assertEquals(
                [1,2,3],
                $ex->sort()->asArray()
            );
        }
    }

    public function test_sum()
    {
        foreach ([F::range(1,3), F::wrap([1,2,3])] as $ex) {
            $this->assertEquals(
                6,
                $ex->sum()
            );
        }
    }

    public function test_tap()
    {
        foreach ([F::wrap([1,2,3]), F::range(1,3)] as $ex) {
            $ex->tap(function($arr) use (&$actual) {
                $this->assertEquals([1,2,3], $arr);
                $actual++;
            });
        }
        $this->assertEquals(2, $actual);
    }

    public function test_unique()
    {
        $this->assertEquals(
            [0 => 1, 2 => 3],
            F::unique([1,1,3,1])->asArray()
        );

        // with an iterator
        $this->assertEquals(
            [1,2,3],
            F::range(1,3)->unique()->asArray()
        );
    }

    public function test_values()
    {
        $this->assertEquals(
            [1,2,3],
            F::values([1,2,3])->asArray()
        );

        // with an iterator
        $this->assertEquals(
            [1,2,3],
            F::range(1,3)->values()->asArray()
        );
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
