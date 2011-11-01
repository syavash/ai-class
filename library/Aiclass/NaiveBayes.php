<?php 
class Aiclass_NaiveBayes
{

	/**
	 * The defined classes (Like: SPAM and HAM) with all samples.
	 * 
	 * @var array 
	 */
	protected $_classes = array();
	
	/**
	 * The precision we want to round the probability results.
	 * 
	 * @var integer
	 */
	protected $_precision = 4;
	
	/**
	 * The detailed solution for each class for learning purposes.
	 * 
	 * @var array
	 */
	protected $_outputs = array();
	
	/**
	 * The detailed solution for each class in TeX format for learning purpose
	 * 
	 * @var array
	 */
	protected $_texOutputs = array();
	
	/**
	 * The calculated probabilities for each class
	 * 
	 * @var array
	 */
	protected $_probabilities = array();
	
	/**
	 * The smoothing parameter (k) used to calculate probabilities
	 * 
	 * @var integer
	 */
	protected $_smoothingParameter = 0;
	
	public function __construct($classes = array()) {
		if (sizeof($classes))
			$this->addClasses($classes);
	}
	
	/**
	 * @param integer $precision
	 * @return Aiclass_NaiveBayes 
	 */	
	public function setPrecision($precision)
	{
		$this->_precision = $precision;
		return $this;
	}
	
	/**
	 * @return integer;
	 */
	public function getPrecision() {
		return $this->_precision;
	}
	
	/**
	 * @param integer $smoothingParameter
	 * @return Aiclass_NaiveBayes 
	 */
	public function setSmoothingParameter($smoothingParameter)
	{
		$this->_smoothingParameter = $smoothingParameter;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getSmoothingParameter() {
		return $this->_smoothingParameter;
	}
	
	/**
	 * @return array
	 */
	public function getClasses() {
		return $this->_classes;
	}
	
	/**
	 * @return array
	 */
	public function getClass($name) {
		return $this->_classes[$name];
	}
	
	/**
	 * @return Aiclass_NaiveBayes 
	 */
	public function clearClasses() {
		$this->_classes = array();
		return $this;
	}

	/**
	 * @param array $classes
	 * @return Aiclass_NaiveBayes 
	 */
	public function addClasses($classes) {
		foreach ($classes as $name=>$items)
			$this->addClass($name, $items);
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param array $items
	 * @return Aiclass_NaiveBayes 
	 */
	public function addClass($name, $items) {		
		foreach ($items as $key=>$item) {
			if (!$items[$key] = strtoupper(trim($item)))
				unset($items[$key]);
		}
		$this->_classes[$name] = $items;
		return $this;
	}
	
	/**
	 * @param array $outputs
	 * @return Aiclass_NaiveBayes 
	 */
	public function setOutputs($outputs) {
		$this->_outputs = $outputs;
		return $this;
	}
	
	/**
	 * @return array 
	 */
	public function getOutputs() {
		return $this->_outputs;
	}
	
	/**
	 * @param string $key
	 * @return array 
	 */
	public function getOutput($key) {
		return $this->_outputs[$key];
	}
	
	/**
	 * @param array $classes
	 * @return Aiclass_NaiveBayes 
	 */
	public function setTexOutputs($texOutputs) {
		$this->_texOutputs = $texOutputs;
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getTexOutputs() {
		return $this->_texOutputs;
	}
	
	/**
	 * @param string $key
	 * @return array 
	 */
	public function getTexOutput($key) {
		return $this->_texOutputs[$key];
	}
	
	/**
	 * @param array $probabilities
	 * @return array 
	 */
	public function setProbabilities($probabilities) {
		$this->_probabilities = $probabilities;
	}
	
	/**
	 * @return array 
	 */
	public function getProbabilities() {
		return $this->_probabilities;
	}
	
	/**
	 * @param string $key
	 * @return array 
	 */
	public function getProbability($key) {
		return $this->_probabilities[$key];
	}
	
	/**
	 * @param string $query The query you want to find the probabilities for.
	 * @return Aiclass_NaiveBayes 
	 */
	public function calculate($query) {
		$query = strtoupper(trim($query));
		// Is query empty? Throw an error!
		if ($query == '')
			throw new Aiclass_Exception('You must pass a query to this method.');

		// Calculating number of all words in dictionary and also unique word count (dictionarySize).
		$classes = $this->getClasses();
		$itemsCount = 0;
		$allWords = "";
		foreach ($classes as $items)
		{
			$itemsCount += sizeof($items);
			$allWords .= " " . implode(" ", $items);
		}
		$dictionarySize = sizeof(array_unique(explode(" ", (trim($allWords)))));

		// Dividends for each class.
		$dividends = array();
		
		// Probability result for each class
		$probabilities = array();
		
		// Detailed output and detailed TeX output for each class
		$outputs = array();
		$texOutputs = array();
		
		// Our query separated with space
		$qParts = explode(" ", $query);
		
		// The smoothing parameter.
		$k = $this->getSmoothingParameter();
		
		// The divisor value which is identical for all classes
		$divisor = 0;

		// The detailed output for divisor
		$outputDivisor = "";
		
		// Calculate pribabilities for each class
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

		// It seems we have a division by zero as a word in the query doesn't exist in any class and smoothing parameter is 0.
		if ($divisor == 0)
			throw new Aiclass_Exception("Not enough datasets for classification.");
		
		// Fill results array.
		foreach ($devindends as $key=>$devindend)
		{
			$probabilities[$key] = round($devindend / $divisor, $this->getPrecision());
			$texOutputs[$key] = strip_tags("P(" . strtoupper($key) . ' | M) = \frac{' . $outputs[$key] . '}{' . $outputDivisor . '}');
			$outputs[$key] = "P(" . strtoupper($key) . " | \"" . $query . "\") = <span class=\"gray\">(" . $outputs[$key] . ")</span> / <span class=\"gray\">(" . $outputDivisor . ")</span>";
		}
		$this->setOutputs($outputs);
		$this->setTexOutputs($texOutputs);
		$this->setProbabilities($probabilities);
		return $this;
	}
	

}