<?php

namespace Elao\MailJetBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class MailjetEvent extends Event implements \ArrayAccess
{

    protected $data = array();

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

}
