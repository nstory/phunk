<?php
namespace Nstory\Phunk;

abstract class Phunk
{
    /**
     * Convert a PhunkObject or an Iterator into an array; if an array is
     * passed-in, it will be returned as-is.
     * @param array|\Iterator $iter
     * @return array the passed-in iterable as an array
     */
    public static function asArray($iter)
    {
        if (is_array($iter)) {
            return $iter;
        }
        $arr = [];
        foreach ($iter as $k => $v) {
            $arr[$k] = $v;
        }
        return $arr;
    }

    /**
     * @param array $arr
     * @param int $size maximum number of elements in each chunk
     * @param boolean $preserve_keys
     * @return Nstory\Phunk\PhunkObject
     */
    public static function chunk($arr, $size, $preserve_keys = false)
    {
        $func = function() use ($arr, $size, $preserve_keys) {
            $chunk = [];
            foreach ($arr as $k => $v) {
                if ($preserve_keys) {
                    $chunk[$k] = $v;
                } else {
                    $chunk[] = $v;
                }
                if (count($chunk) == $size) {
                    yield $chunk;
                    $chunk = [];
                }
            }
            if (!empty($chunk)) {
                yield $chunk;
            }
        };
        return static::wrap($func());
    }

    /**
     * Array keys are preserved.
     * @param array|\Iterator $iter
     * @param callable $cb
     * @return Nstory\Phunk\PhunkObject
     */
    public static function filter($iter, $cb = null)
    {
        // by default, filter out falsey values
        $cb = $cb ?: function($v) {
            return (boolean)$v;
        };

        $func = function() use($iter, $cb) {
            foreach ($iter as $k => $v) {
                if ($cb($v)) {
                    yield $k => $v;
                }
            }
        };
        return static::wrap($func());
    }

    /**
     * @param array|\Iterator $iter
     * @param string $glue
     * @return string
     */
    public static function implode($iter, $glue)
    {
        return implode($glue, static::asArray($iter));
    }

    /**
     * @param array|Iterator $haystack collection to search
     * @param mixed $needle value to search for
     * @param boolean $strict use strict equality if this is true, loose otherwise
     * @return boolean if $needle as found in $haystack
     */
    public static function in($haystack, $needle, $strict = false)
    {
        foreach ($haystack as $v) {
            if ($strict ? $v === $needle : $v == $needle) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array|Iterator $iter
     * @return Nstory\Phunk\PhunkObject
     */
    public static function keys($iter)
    {
        $func = function() use($iter) {
            foreach ($iter as $k => $v) {
                yield $k;
            }
        };
        return static::wrap($func());
    }

    /**
     * @param array|Iterator $iter
     * @param callable $func
     * @return Nstory\Phunk\PhunkObject
     */
    public static function ksort($iter, $func=null)
    {
        $arr = static::asArray($iter);
        if ($func) {
            uksort($arr, $func);
        } else {
            ksort($arr);
        }
        return static::wrap($arr);
    }

    /**
     * @param array|Iterator $iter
     * @param callable $func a function with the signature function ($value) or
     * function ($value, $key)
     * @return Nstory\Phunk\PhunkObject
     */
    public static function map($iter, $cb)
    {
        $func = function() use($iter, $cb) {
            foreach ($iter as $k => $v) {
                yield $cb($v, $k);
            }
        };
        return static::wrap($func());
    }

    /**
     * @param array|\Iterator $iter
     * @param callable $comparator
     * @return mixed the lowest value in $iter or null if the array
     * is empty
     */
    public static function min($iter, $comparator = null)
    {
        if (empty($iter)) {
            return null;
        }

        // default to numeric comparison
        $comparator = $comparator ?: function($a, $b) {
            return $a - $b;
        };

        $min = $sentinel = new \stdClass;
        foreach ($iter as $v) {
            if ($min === $sentinel || $comparator($v, $min) < 0) {
                $min = $v;
            }
        }
        return $min;
    }

    /**
     * @param array|\Iterator $iter
     * @param callable $comparator
     * @return mixed the greatest value in $iter or null if the array
     * is empty
     */
    public static function max($iter, $comparator = null)
    {
        // default to numeric comparison
        $comparator = $comparator ?: function($a, $b) {
            return $a - $b;
        };

        return static::min($iter, function($a, $b) use($comparator) {
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
     * @return Nstory\Phunk\PhunkObject
     */
    public static function range($start, $end, $step = 1)
    {
        $func = function() use($start, $end, $step) {
            $step = abs($step);
            if ($start <= $end) {
                for (; $start <= $end; $start += $step) {
                    yield $start;
                }
            } else {
                for (; $start >= $end; $start -= $step) {
                    yield $start;
                }
            }
        };
        return static::wrap($func());
    }

    /**
     * @param array|\Iterator $iter
     * @param callable $func
     * @param mixed $initial
     * @return mixed
     */
    public static function reduce($iter, $func, $initial)
    {
        foreach ($iter as $v) {
            $initial = $func($initial, $v);
        }
        return $initial;
    }

    /**
     * @param Iterator|array $iter
     * @param boolean $preserve_keys
     * @return Nstory\Phunk\PhunkObject
     */
    public static function reverse($iter, $preserve_keys = false)
    {
        return static::wrap(
            array_reverse(
                static::asArray($iter),
                $preserve_keys
            )
        );
    }

    /**
     * @param array|\Iterator $iter
     * @return Nstory\Phunk\PhunkObject
     */
    public static function shuffle($iter)
    {
        $array = static::asArray($iter);
        shuffle($array);
        return static::wrap($array);
    }

    /**
     * @param array|\Iterator $iter
     * @param int $start
     * @param int $length
     * @param boolean $preserve_keys
     * @return Nstory\Phunk\PhunkObject
     */
    public static function slice(
        $iter,
        $start,
        $length = null,
        $preserve_keys = false) {
            // if $start or $length are negative, we need the entire sequence
            // (b/c, otherwise, we don't know the length). so, we might as
            // well just use the built-in function
            if ($start < 0 || $length < 0) {
                return static::wrap(array_slice(
                    static::asArray($iter),
                    $start,
                    $length,
                    $preserve_keys
                ));
            }

            // otherwise, use a generator so we only read what we have to
            $func = function() use($iter, $start, $length, $preserve_keys) {
                $i = 0;
                foreach ($iter as $k => $v) {
                    if ($i >= $start && ($length === null || $i < $start+$length)) {
                        if ($preserve_keys) {
                            yield $k => $v;
                        } else {
                            yield $v;
                        }
                    } else if ($length !== null && $i >= $start+$length) {
                        break;
                    }
                    $i++;
                }
            };
            return static::wrap($func());
    }

    /**
     * @param array|Iterator $iter
     * @param callable $func
     * @return Nstory\Phunk\PhunkObject
     */
    public static function sort($iter, $func=null)
    {
        $arr = static::asArray($iter);
        if ($func) {
            usort($arr, $func);
        } else {
            sort($arr);
        }
        return static::wrap($arr);
    }

    /**
     * @param array|\Iterator $iter
     * @return number
     */
    public static function sum($iter)
    {
        $arr = static::asArray($iter);
        return array_sum($arr);
    }

    /**
     * @param array|\Iterator $iter
     * @param callable $func
     * @return Nstory\Phunk\PhunkObject
     */
    public static function tap($iter, $func)
    {
        $arr = static::asArray($iter);
        $func($arr);
        return static::wrap($arr);
    }

    /**
     * Keys are preserved.
     *
     * @param array|\Iterator $iter
     * @return Nstory\Phunk\PhunkObject
     */
    public static function unique($iter)
    {
        $arr = static::asArray($iter);
        return static::wrap(array_unique($arr));
    }

    /**
     * @param array|\Iterator $iter
     * @return Nstory\Phunk\PhunkObject
     */
    public static function values($iter)
    {
        $func = function() use ($iter) {
            foreach ($iter as $value) {
                yield $value;
            }
        };
        return static::wrap($func());
    }

    /**
     * @return Nstory\Phunk\PhunkObject
     */
    public static function wrap($array)
    {
        return new PhunkObject($array);
    }
}
