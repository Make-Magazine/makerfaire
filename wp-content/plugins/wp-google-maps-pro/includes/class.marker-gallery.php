<?php

namespace WPGMZA;

class MarkerGallery extends Factory implements \JSONSerializable, \ArrayAccess
{
	protected $items;
	
	public function __construct($data=null)
	{
		$this->items = array();
		
		if(!empty($data))
		{
			if(is_string($data))
			{
				$data = json_decode(stripslashes($data));
				if(!$data)
					throw new \Exception('Failed to parse gallery data :- ' . json_last_error_msg());
			}
			
			if(!is_array($data))
				throw new \Exception('Input must be an array');
			
			foreach($data as $obj)
				$this->items[] = new MarkerGalleryItem($obj);
		}
	}
	
	public function __get($name)
	{
		switch($name)
		{
			case "numItems":
			case "length":
				return count($this->items);
				break;
		}
	}
	
	public function isEmpty()
	{
		return empty($this->items);
	}
	
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return $this->items;
	}
	
	#[\ReturnTypeWillChange]
	public function item($index)
	{
		return $this->items[$index];
	}
	
	#[\ReturnTypeWillChange]
	public function offsetExists($offset)
	{
		return count($this->items) < $offset;
	}
	
	#[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		return $this->items[$offset];
	}
	
	#[\ReturnTypeWillChange]
	public function offsetSet($offset, $value)
	{
		$this->items[$offset] = $value;
	}
	
	#[\ReturnTypeWillChange]
	public function offsetUnset($offset)
	{
		return array_splice($this->items, $offset, 1);
	}
}