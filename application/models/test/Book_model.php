<?php
if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Book_model Class
 * @date 2018-07-28
 */
class Book_model extends MY_Model
{

	private $my_table;
	public $session_name;

	public function __construct()
	{
		parent::__construct();
		$this->my_table = 'book';
		$this->set_table_name($this->my_table);
	}


	public function exists($data)
	{
		$bk_id = checkEncryptData($data['bk_id']);
		$bk_month = checkEncryptData($data['bk_month']);
		$this->set_where("bk_id = $bk_id AND bk_month = '$bk_month'");
		return $this->count_record();
	}


	public function load($id_md5_concat)
	{
		$this->set_where("MD5(CONCAT(bk_id, bk_month)) = '$id_md5_concat'");
		return $this->load_record();
	}


	public function create($post)
	{

		$data = array(
				'bk_month' => $post['bk_month']
				,'bk_code' => $post['bk_code']
				,'bk_name' => $post['bk_name']
				,'bk_last_update' => $post['bk_last_update']
		);
		return $this->add_record($data);
	}


	/**
	* List all data
	* @param $start_row	Number offset record start
	* @param $per_page	Number limit record perpage
	*/
	public function read($start_row, $per_page)
	{
		$search_field 	= $this->session->userdata($this->session_name . '_search_field');
		$value 	= $this->session->userdata($this->session_name . '_value');
		
		$where	= '';
		$order_by	= '';
		if($search_field != ''){
			$search_method_value = '';
			if($search_field == 'bk_id'){
				$value = $value + 0;
				$search_method_value = "= $value";				
			}
			if($search_field == 'bk_month'){
				$search_method_value = "= '$value'";				
			}
			$where		= " $search_field $search_method_value "; 
			$order_by	= " $search_field";
		}
		$total_row = $this->count_record();
		$search_row = $total_row;
		if ($where != '') {
			$this->set_where($where);
			$search_row = $this->count_record();
		}
		$offset = $start_row;
		$limit = $per_page;
		$this->set_order_by($order_by);
		$this->set_offset($offset);
		$this->set_limit($limit);
		$list_record = $this->list_record();
		$data = array(
				'total_row'	=> $total_row, 
				'search_row'	=> $search_row,
				'list_data'	=> $list_record
		);
		return $data;
	}

	public function update($post)
	{
		$data = array(
				'bk_code' => $post['bk_code']
				,'bk_name' => $post['bk_name']
				,'bk_last_update' => $post['bk_last_update']
		);

		$bk_id = checkEncryptData($post['encrypt_bk_id']);
		$bk_month = checkEncryptData($post['encrypt_bk_month']);
		$this->set_where("bk_id = $bk_id AND bk_month = '$bk_month'");
		return $this->update_record($data);
	}


	public function delete($post)
	{
		$bk_id = checkEncryptData($post['encrypt_bk_id']);
		$bk_month = checkEncryptData($post['encrypt_bk_month']);
		$this->set_where("bk_id = $bk_id AND bk_month = '$bk_month'");
		return $this->delete_record();
	}


}
/*---------------------------- END Model Class --------------------------------*/