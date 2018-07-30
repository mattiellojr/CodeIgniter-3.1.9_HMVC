<?php
if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * [ Controller File name : Forgot_password.php ]
 */
class Forgot_password extends CI_Controller 
{
	private $breadcrumb_data;
	private $left_sidebar_data;
	private $another_js;
	private $another_css;

	public function __construct()
	{
		parent::__construct();
		
		$data['base_url'] = base_url();
		$data['site_url'] = site_url();
		$data['page_url'] = site_url('example_pages/forgot_password');
		$data['csrf_token_name'] = $this->security->get_csrf_token_name();
		$data['csrf_cookie_name'] = $this->config->item('csrf_cookie_name');
		$this->data = $data;
		$this->breadcrumb_data = $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Render this controller page
	 * @param String path of controller
	 * @param Integer total record
	 */
	private function render_view($path)
	{
		$this->data['left_sidebar'] = $this->parser->parse('company/includes/left_sidebar_view', $this->data, TRUE);
		$this->data['breadcrumb_list'] = $this->parser->parse('company/includes/breadcrumb_view', $this->breadcrumb_data, TRUE);
		$this->data['page_content'] = $this->parser->parse($path, $this->data, TRUE);
		
		$this->data['another_css'] = $this->another_css;
		$this->data['another_js'] = $this->another_js;
		
		$this->parser->parse('company/includes/homepage_view', $this->data);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Index of controller
	 */
	public function index()
	{
		$this->breadcrumb_data['breadcrumb'] = array(
						array('title' => 'Forgot password', 'class' => 'active', 'url' => '#'),
		);
	
		$this->render_view('forgot_password.php');
	}

		
	private function add_js($js_url)
	{
		$this->another_js .= '<script src="'. base_url('assets/'.$js_url) .'"></script>';
	}
}
/*---------------------------- END Controller Class --------------------------------*/
