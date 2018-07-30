<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Overrides the CI Template Parser to allow for multiple occurrences of the
 * same variable pair
 *
 */
class MY_Parser extends CI_Parser {

	// --------------------------------------------------------------------

	/**
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template view,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse($template, $data, $return = FALSE)
	{
		$template = $this->CI->load->view($template, $data, TRUE);
		$results = $this->_parse_double($template, $data);		
		return $this->_parse($results, $data, $return);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Parse a double key/value
	 *
	 * @param	string
	 * @param	array
	 * @return	string
	 */
	protected function _parse_double($results, $data)
	{
		$replace = array();
		preg_match_all("/\{\{(.*?)\}\}/si", $results, $matches);
		
		foreach ($matches[1] as $match)
		{
			$key = '{{'.$match.'}}';
			$replace[$key] = isset($data[$match]) ? $data[$match] : $key;
		}
		return strtr($results, $replace);
	}

}

// END Parser Class

/* End of file MY_Parser.php */
/* Location: ./application/libraries/MY_Parser.php */