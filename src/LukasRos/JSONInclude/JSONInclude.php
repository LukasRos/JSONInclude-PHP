<?php

namespace LukasRos\JSONInclude;

class JSONInclude {
	
	private $options;
	
	public function __construct($options = array()) {
		$this->options = array_merge(array(
			'include_symbol' => '@',
			'base_dir' => '',
			'silent' => true	
		), $options);
	}
	
	public function parseDataWithIncludes($data, $baseDir = null, $fileChain = array()) {
		if (!$baseDir) $baseDir = $this->options['base_dir'];
		
		if (is_array($data)) {
			// Array is handled recursively
			$output = array();
			foreach ($data as $key => $value) {
				$output[$key] = $this->parseDataWithIncludes($value, $baseDir, $fileChain);
			}
			return $output;
		} elseif (is_string($data) && strlen($data)>0 && $data[0]==$this->options['include_symbol']) {
			// Include file
			return $this->parseFileWithIncludes(substr($data, 1), $baseDir, $fileChain);
		} else {
			// Return data
			return $data;
		}
	}
	
	public function parseFileWithIncludes($filename, $baseDir = null, $fileChain = array()) {
		if (!$baseDir) $baseDir = $this->options['base_dir'];
		
		if ($baseDir=='')
			$baseDir = dirname($filename);
		else
			$filename = realpath($baseDir.'/'.$filename);

		if (!file_exists($filename)) {
			if ($this->options['silent']) return null;
			else throw new \Exception('A requested file does not exist.');
		}
		if (in_array($filename, $fileChain)) {
			if ($this->options['silent']) return null;
			else throw new \Exception('A file is inserted recursively: '.$filename);
		}
		$fileChain[] = $filename;
		
		return $this->parseDataWithIncludes(json_decode(file_get_contents($filename), true), $baseDir, $fileChain);
	}
	
}