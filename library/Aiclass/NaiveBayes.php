<?php 
class Aiclass_NaiveBayes
{
	protected $_classes = array();
	protected $_precision = 4;
	protected $_outputs = array();
	protected $_texOutputs = array();
	protected $_probabilities = array();
	protected $_smoothingParameter = 0;
	
	public function __construct($classes = array()) {
		if (sizeof($classes))
			$this->addClasses($classes);
	}
	
	public function setPrecision($precision)
	{
		$this->_precision = $precision;
	}
	
	public function getPrecision() {
		return $this->_precision;
	}
	
	public function setSmoothingParameter($smoothingParameter)
	{
		$this->_smoothingParameter = $smoothingParameter;
	}
	
	public function getSmoothingParameter() {
		return $this->_smoothingParameter;
	}
	
	public function getClasses() {
		return $this->_classes;
	}
	
	public function getClass($name) {
		return $this->_classes[$name];
	}
	
	public function clearClasses() {
		$this->_classes = array();
	}

	public function addClasses($classes) {
		foreach ($classes as $name=>$items)
			$this->addClass($name, $items);
	}
	
	public function addClass($name, $items) {		
		foreach ($items as $key=>$item) {
			if (!$items[$key] = strtoupper(trim($item)))
				unset($items[$key]);
		}
		$this->_classes[$name] = $items;
	}
	
	public function setOutputs($outputs) {
		$this->_outputs = $outputs;
	}
	
	public function getOutputs() {
		return $this->_outputs;
	}
	
	public function getOutput($key) {
		return $this->_outputs[$key];
	}
	
	public function setTexOutputs($texOutputs) {
		$this->_texOutputs = $texOutputs;
	}
	
	public function getTexOutputs() {
		return $this->_texOutputs;
	}
	
	public function getTexOutput($key) {
		return $this->_texOutputs[$key];
	}
	
	public function setProbabilities($probabilities) {
		$this->_probabilities = $probabilities;
	}
	
	public function getProbabilities() {
		return $this->_probabilities;
	}
	
	public function getProbability($key) {
		return $this->_probabilities[$key];
	}
	
	public function calculate($query) {
		$query = strtoupper(trim($query));
		if ($query == '')
			throw new Aiclass_Exception('You must pass a query to this method.');
			
		$qParts = explode(" ", $query);
		$classes = $this->getClasses();
		$itemsCount = 0;
		$allWords = "";
		foreach ($classes as $items)
		{
			$itemsCount += sizeof($items);
			$allWords .= " " . implode(" ", $items);
		}
		$dictionarySize = sizeof(array_unique(explode(" ", (trim($allWords)))));
		$dividends = array();
		$probabilities = array();
		$k = $this->getSmoothingParameter();
		$outputs = array();
		$texOutputs = array();
		$outputDivisor = "";
		$output = "";
		$divisor = 0;
		$result = array();
		foreach ($classes as $key=>$items) {
			$allItems = implode(" ", $items);
			$p = (sizeof($items)+$k)/($itemsCount+($k*sizeof($classes)));
			$outputs[$key] = '<span class="green" title="P(' . strtoupper($key) . ')">(' . ($k ? '(' : '') . sizeof($items) . ($k ? '+' . $k  . ')' : '') . "/" . ($k ? '(' : '') . $itemsCount . ($k ? '+(' . $k . '*' . sizeof($classes) . '))' : '') . ")</span>";
			foreach ($qParts as $part) {	
					$p *= (substr_count(' ' . $allItems . ' ', ' ' . $part . ' ') + $k) / (sizeof(explode(" ", $allItems)) + ($k*$dictionarySize));
					$outputs[$key] .= "*<span class=\"yellow\" title=\"P('" . $part . "' | " . strtoupper($key) . ")\">(" . ($k ? '(' : '') . substr_count(' ' . $allItems . ' ', ' ' . $part . ' ') . ($k ? '+' . $k . ')' : '') . "/" . ($k ? '(' : '') . sizeof(explode(" ", $allItems)) . ($k ? '+(' . $k . '*' . $dictionarySize . ')' : '') . ')</span>';
			}
			$outputDivisor .= $outputs[$key] . "+";
			$devindends[$key] = $p;
			$divisor += $p;
		}
		$outputDivisor = substr($outputDivisor, 0, -1);

		if ($divisor == 0)
			throw new Aiclass_Exception("Not enough datasets for classification.");
		
		foreach ($devindends as $key=>$devindend)
		{
			$probabilities[$key] = round($devindend / $divisor, $this->getPrecision());
			$texOutputs[$key] = strip_tags("P(" . strtoupper($key) . ' | M) = \frac{' . $outputs[$key] . '}{' . $outputDivisor . '}');
			$outputs[$key] = "P(" . strtoupper($key) . " | \"" . $query . "\") = <span class=\"gray\">(" . $outputs[$key] . ")</span> / <span class=\"gray\">(" . $outputDivisor . ")</span>";
		}
		$this->setOutputs($outputs);
		$this->setTexOutputs($texOutputs);
		$this->setProbabilities($probabilities);
		return $probabilities;
	}
	

}