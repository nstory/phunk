<?php
namespace Nstory\Phunk;

class PhunkObject
{
    private $array;

    public function __construct($array)
    {
        $this->array = $array;
    }

    public function __call($name, $args)
    {
        return call_user_func_array(__NAMESPACE__ . '\Phunk::' . $name,
            array_merge($args, [$this->array]));
    }

    public function asArray()
    {
        return $this->array;
    }
}
