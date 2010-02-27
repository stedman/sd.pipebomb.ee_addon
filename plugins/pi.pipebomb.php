<?php
/*
	CHANGELOG:
		1.0  2010-02-26
			* renamed parameters
		0.3  2010-02-25
			* added cookie_switch parameter
		0.2  2010-02-24
			* changed name from pi.language_pipe.php to pi.pipebomp.php
			* moved hard-coded attributes into plugin paramters
		0.1  2010-02-09
*/
$plugin_info = array(
	'pi_name' => 'PipeBomb',
	'pi_version' => '1.0',
	'pi_author' => 'Steve Stedman',
	'pi_author_url' => 'http://stedmandesign.com/',
	'pi_description' => 'Run simple switch/case expressions within a small tag footprint.',
	'pi_usage' => PipeBomb::usage()
);

class PipeBomb {

    var $return_data;

	function PipeBomb()
	{
		global $IN, $TMPL;

		$switch = $TMPL->fetch_param('switch');
		$switch_type = strtolower($TMPL->fetch_param('switch_type'));
		// here's where the name 'pipebomb' comes from:
		$cases = explode('|', $TMPL->fetch_param('cases') );
		$values = explode('|', $TMPL->fetch_param('values') );

		// extract the switch from a cookie
		if ($switch_type == 'cookie') {
			// toss the EE prefix, if there is one
			$switch = str_replace('exp_', '', $switch);
			$switch = $IN->GBL($switch, 'COOKIE');
		}

		// save switch position in cases as an array key
		$key = array_search($switch, $cases);
		// if no key found, default to the first item in the array
		$key = ($key === FALSE) ? 0 : $key;

		// if the array value at the provided key location is empty, default to the first item in the array
		$this->return_data = empty($values[$key]) ? $values[0] : $values[$key];
	}

	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------

	function usage()
	{
		ob_start();
	?>
SAMPLES
--------------------
Common usage:
{exp:pipebomb switch="{dynamic_variable}" cases="1|2|3|4" values="Number One|Number Two|Number Three|Number Four"}

In the example above, if the dynamic variable is 3, then "Number Three" shall be returned.

Switch on ExpressionEngine cookies:
{exp:pipebomb switch="language" switch_type="cookie" cases="|_es|_pg" values="Display English|Indicar Español|Mostrar Português"}

If you need to get a cookie variable set by ExpressionEngine, set the switch_type to "cookie" and enter the cookie name in the switch parameter.

PARAMETERS
--------------------
switch: the variable expression that will be used to search for a match in "cases"
switch_type="cookie": [OPTIONAL] to get the value of an ExpressionEngine cookie (the value of "switch" should be the cookie name)
cases: the piped fields for the "switch" to match
values: the piped output that corresponds to the "cases" fields
	<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
	// END
}

?>