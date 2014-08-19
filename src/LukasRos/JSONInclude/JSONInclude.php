<?php

namespace LukasRos\JSONInclude;

class JSONInclude {
	
	private $options;
	private $processors = array();
	
	public function __construct($options = array()) {
		$this->options = array_merge(array(
			'include_symbol' => '@',
			'base_dir' => '',
			'silent' => true,
			'accept_comments' => true	
		), $options);
	}
	
	/**
	 * Adds a custom JSON pre-processor.
	 * @param $regExp A regular expression that a JSON string should match in order to be handled with this processor.
	 * @param $callback A function containing the implementation of the processor.
	 */
	public function addCustomPreProcessor($regExp, $callback) {
		$this->processors[$regExp] = $callback;
	}
	
	/**
	 * Parses includes in an array representing JSON data.
	 */
	public function parseDataWithIncludes($data, $baseDir = null, $fileChain = array()) {
		if (!$baseDir) $baseDir = $this->options['base_dir'];
		
		if (is_array($data)) {
			// Array is handled recursively
			$output = array();
			foreach ($data as $key => $value) {
				$output[$key] = $this->parseDataWithIncludes($value, $baseDir, $fileChain);
			}
			return $output;
		} elseif (is_string($data)) {
			// Process string with custom JSON preprocessors
			foreach ($this->processors as $regExp => $callback) {
				if (preg_match($regExp, $data)===1) $data = $callback($data);
			}
			
			if (strlen($data)>0 && $data[0]==$this->options['include_symbol'])
				// Include file
				return $this->parseFileWithIncludes(substr($data, 1), $baseDir, $fileChain);
			else
				return $data;
		} else {
			// Return data
			return $data;
		}
	}
	
	/**
	 * Parses includes in a file containing JSON data.
	 */
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
		
		$jsonString = file_get_contents($filename);
		if ($this->options['accept_comments']) {
			// Parse comments - code taken from http://php.net/manual/de/function.json-decode.php#111551
			$jsonString = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $jsonString); 
		}
		return $this->parseDataWithIncludes(json_decode($jsonString, true), $baseDir, $fileChain);
	}
	
}