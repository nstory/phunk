<?php
namespace Nstory\Phunk;

abstract class Phunk
{
    /**
     * @return static
     */
    public static function chunk($arr, $size, $preserve_keys = false)
    {
        return static::wrap(array_chunk($arr, $size, $preserve_keys));
    }

    /**
     * Preserves keys
     * @return static
     */
    public static function filter($arr, $func = null)
    {
        return static::wrap(
            $func ? array_filter($arr, $func) : array_filter($arr)
        );
    }

    /**
     * @return string
     */
    public static function implode($arr, $glue)
    {
        return implode($glue, $arr);
    }

    public static function in($arr, $needle, $strict = false)
    {
        return in_array($needle, $arr, $strict);
    }

    public static function keys($arr)
    {
        return static::wrap(array_keys($arr));
    }

    /**
     * @return static
     */
    public static function ksort($arr, $func=null)
    {
        if ($func != null) {
            uksort($arr, $func);
        } else {
            ksort($arr);
        }
        return static::wrap($arr);
    }

    /**
     * @return static
     */
    public static function map($arr, $func)
    {
        $r = [];
        foreach ($arr as $k => $v) {
            $r[] = $func($v, $k);
        }
        return static::wrap($r);
    }

    /**
     * @return mixed the lowest value in $arr or null if the array
     * is empty
     */
    public static function min($arr, $comparator = null)
    {
        if (empty($arr)) {
            return null;
        }

        // default to the PHP implementation
        if ($comparator === null) {
            return min($arr);
        }

        // otherwise use the comparator to find the lowest value
        foreach ($arr as $v) {
            if (!isset($min) || $comparator($v, $min) < 0) {
                $min = $v;
            }
        }
        return $min;
    }

    public static function max($arr, $comparator = null)
    {
        if (empty($arr)) {
            return null;
        }

        // default to the PHP implementation
        if ($comparator === null) {
            return max($arr);
        }

        return static::min($arr, function($a, $b) use($comparator) {
            return $comparator($b, $a);
        });
    }

    /**
     * @return Nstory\Phunk\Path
     */
    public static function path()
    {
        return new Path;
    }

    /**
     * @return mixed
     */
    public static function reduce($arr, $func, $initial)
    {
        foreach ($arr as $v) {
            $initial = $func($initial, $v);
        }
        return $initial;
    }

    /**
     * @return static
     */
    public static function reverse($arr, $preserve_keys = false)
    {
        return static::wrap(array_reverse($arr, $preserve_keys));
    }

    /**
     * @return static
     */
    public static function sort($arr, $func=null)
    {
        if ($func) {
            usort($arr, $func);
        } else {
            sort($arr);
        }
        return static::wrap($arr);
    }

    /**
     * @return float
     */
    public static function sum($arr)
    {
        return array_sum($arr);
    }

    /**
     * @return static
     */
    public static function tap($arr, $func)
    {
        $func($arr);
        return static::wrap($arr);
    }

    /**
     * Keys are preserved.
     *
     * @return static
     */
    public static function unique($arr)
    {
        return static::wrap(array_unique($arr));
    }

    /**
     * @return static
     */
    public static function values($arr)
    {
        return static::wrap(array_values($arr));
    }

    /**
     * @return static
     */
    public static function wrap($array)
    {
        return new PhunkObject($array);
    }
}
