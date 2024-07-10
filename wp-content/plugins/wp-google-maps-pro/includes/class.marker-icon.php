<?php

namespace WPGMZA;

class MarkerIcon extends Factory implements \JsonSerializable
{
	public $url = "";
	public $retina = false;
	public $hover_url = "";
	public $hover_retina = false;
	
	public function __construct($arg=null)
	{
		if(is_string($arg))
		{
			$obj = @json_decode($arg);
			
			if(!$obj)
				$this->url = $arg;
			else foreach($obj as $key => $value)
				$this->{$key} = $value;
		}
		else if(is_array($arg) || is_object($arg))
		{
			$arr = (array)$arg;
			
			foreach($arr as $key => $value)
				$this->{$key} = $value;
		}
	}
	
	public function __get($name)
	{
		if($name == "isDefault")
			return empty($this->url) || preg_replace("/^http(s?):|\?.+$/", "", stripslashes($this->url)) == preg_replace("/^http(s?):|\?.+$/", "", Marker::DEFAULT_ICON);

		if($name == "isDefaultHover")
			return empty($this->hover_url) || preg_replace("/^http(s?):|\?.+$/", "", stripslashes($this->hover_url)) == preg_replace("/^http(s?):|\?.+$/", "", Marker::DEFAULT_ICON);
	}
	
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		$serialized = array();
		if(!$this->isDefault){
			$serialized['url'] = $this->url;
			$serialized['retina'] = $this->retina;
		}

		if(!$this->isDefaultHover){
			$serialized['hover_url'] = $this->hover_url;
			$serialized['hover_retina'] = $this->hover_retina;
		}

		return $serialized;
	}
	
	public function __toString()
	{
		return $this->url;
	}
}