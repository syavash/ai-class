<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
	<head>
		<title>Siavash Mahmoudian - ai-class - Bayes Networks</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Language" content="en-US" />		
		<link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />
		<script type='text/javascript' src='js/jquery-1.3.2.min.js'></script>
		<script type="text/javascript" src="js/jquery.tipsy.js"></script>
		<script type="text/javascript" src="js/script.js"></script>
	</head>
	<body>
		<h1>AI-CLASS.COM - Bayes Network <span>By <a href="http://syavash.com">Siavash Mahmoudian</a></span></h1>
		<div class="desc">
		This tool is created based on Bayes Network lesson in Unit 5 of <a href="http://ai-class.com">ai-class.com</a>.<br />
		You can find the PHP sourcecode on <a href="https://github.com/syavash/ai-class/">github.com</a>.
		The main calculation class is <a href="https://github.com/syavash/ai-class/blob/master/library/Aiclass/NaiveBayes.php">Aiclass_NaiveBayes</a>.
		</div>
		<form action="?" method="post">
			<div class="column">
			<h3>Movie</h3>
<textarea name="classA">
A PERFECT WORLD
MY PERFECT WOMAN
PRETTY WOMAN
</textarea>
			</div>
			<div class="column">
			<h3>Song</h3>
<textarea name="classB">
A PERFECT WORLD
ELECTRIC STORM
ANOTHER RAINY DAY
</textarea>
			</div>
			<div class="spacer"></div>
			<dl>
				<dt><label for="smoothingParameter">Smoothing Parameter: </label></dt>
				<dd><input type="text" name="smoothingParameter" id="smoothingParameter" value="<?=array_key_exists('smoothingParameter', $_POST) ? $_POST['smoothingParameter'] : '1'?>" /></dd>
				<dt><label for="query">Query: </label></dt><dd><input value="<?=array_key_exists('query', $_POST) ? $_POST['query'] : 'Perfect Storm'?>" type="text" name="query" id="query" /></dd>
			</dl>
			<input type="submit" class="submit-button" value="Calculate Probabilities!" />
		</form>
		<div class="spacer"></div>
		<?php
			if ($_POST) {
				require_once "library/Aiclass/NaiveBayes.php";
				require_once "library/Aiclass/Exception.php";
				$classA = explode("\n", $_POST['classA']);
				$classB = explode("\n", $_POST['classB']);

				$nb = new Aiclass_NaiveBayes();
				$nb->addClass("movie", $classA)
					->addClass("song", $classB)
					->setSmoothingParameter($_POST['smoothingParameter'])
					->setPrecision(4);
				
				echo '<div class="result"><h2>Results</h2>';
				try {
					$nb->calculate($_POST['query']);
					echo 'Hover over each highlighted statement to see details.';
					foreach ($nb->getClasses() as $key=>$class) {
						echo '<h3>' . strtoupper($key) . '</h3><ul>'.
								'<li><b>Probability:</b> <pre>' . $nb->getProbability($key) . '</pre></li>'.
								'<li><b>Raw Output:</b> <pre>' . $nb->getOutput($key) . '</pre></li>'.
								'<li><b>Output:</b><pre><img src="http://latex.codecogs.com/gif.latex?' . ($nb->getTexOutput($key)) . '" alt="Equation" /></pre></li>'.
							'</ul>';
					}
				} catch (Exception $e) {
					echo '<h3>' . $e->getMessage() . '</h3>';
				}
				echo '</div>';
			}
		
		
		?>
	</body>
</html>