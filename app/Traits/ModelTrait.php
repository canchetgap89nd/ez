<?php

namespace App\Traits;

trait ModelTrait
{
	protected $segment = '_';

    public function getIndexTable(int $dividend, int $divisor)
    {
    	if (!$dividend || $dividend < 0) {
    		throw new \Exception('divisor by zero or negative numbers.');
    	}
    	return $dividend % $divisor;
    }

    public function getNameTable(int $dividend, string $prefix, int $countTable)
    {
    	$indexTable = $this->getIndexTable($dividend, $countTable);
    	if ($countTable >= 0 && $prefix) {
    		return $prefix . $this->segment . $this->getIndexTable($dividend, $countTable);
    	}
    	throw new \Exception('Not has name table conversation.');
    }
}