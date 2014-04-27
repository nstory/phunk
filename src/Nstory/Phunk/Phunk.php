<?php
namespace Nstory\Phunk;

abstract class Phunk
{

    /**
     * @return static
     */
    public static function chunk($size, $arr)
    {
        return static::wrap(array_chunk($arr, $size));
    }

    /**
     * @return static
     */
    public static function filter($func, $arr)
    {
        $r = [];
        foreach ($arr as $k => $v) {
            if ($func($v, $k)) {
                $r[$k] = $v;
            }
        }
        return static::wrap($r);
    }

    /**
     * @return string
     */
    public static function implode($glue, $arr)
    {
        return implode($glue, $arr);
    }

    public static function in($needle, $arr)
    {
        return in_array($needle, $arr);
    }

    /**
     * @return static
     */
    public static function ksort($func, $arr)
    {
        uksort($arr, $func);
        return static::wrap($arr);
    }

    /**
     * @return static
     */
    public static function map($func, $arr)
    {
        $r = [];
        foreach ($arr as $k => $v) {
            $r[] = $func($v, $k);
        }
        return static::wrap($r);
    }

    /**
     * @return mixed
     */
    public static function reduce($func, $initial, $arr)
    {
        foreach ($arr as $v) {
            $initial = $func($initial, $v);
        }
        return $initial;
    }

    /**
     * @return static
     */
    public static function reverse($arr)
    {
        return static::wrap(array_reverse($arr));
    }

    /**
     * @return static
     */
    public static function sort($func, $arr)
    {
        usort($arr, $func);
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
    public static function tap($func, $arr)
    {
        $func($arr);
        return static::wrap($arr);
    }

    /**
     * @return static
     */
    public static function wrap($array)
    {
        return new PhunkObject($array);
    }
}
