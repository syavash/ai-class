ai-class sourcecodes
====================
Unit 5
------
Here is how to use `Aiclass_NaiveBayes` class:

    require_once "library/Aiclass/NaiveBayes.php";
    require_once "library/Aiclass/Exception.php";
    
    $nb = new Aiclass_NaiveBayes();
    $nb->addClass("ham", array("BEAUTIFUL SUMMER", "SECRET FRIEND"))
    	->addClass("spam", array("A SECRET LINK", "SECRET MONEY"))
    	->setSmoothingParameter(1)
    	->setPrecision(4);

    // This gives you the exact probability for each class
    $probabilities = $nb->getProbabilities();
    echo $probabilities['spam'];
    
    // This gives you the calculation's details for each class
    $outputs = $nb->getOutputs();
    echo '<pre>' . $outputs['spam'] . '</pre>';
    