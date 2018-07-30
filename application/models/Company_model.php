<?php
if (!defined('BASEPATH'))  exit('No direct script access allowed');

  /**
  *@generate by  
  *@date 2018-06-11
  */

class Company_model extends MY_Model
{

public $session_name;

	public function __construct()
	{
		parent::__construct();
		$this->set_table_name('tb_company');
	}


	public function exists($pk_value = '')
	{
		$check_id = decrypt($pk_value);
		if($check_id != ''){
			$pk_value = $check_id;//ถ้าไม่เป็นค่าว่าง แสดงว่ายังเข้ารหัสอยู่ ให้ใช้ที่ถอดรหัสแล้ว
		}
		$this->set_where("CompanyID = $pk_value");
		return $this->count_record();
	}


	public function create($post)
	{

		$data = array(
				'CompanyName' => $post['CompanyName']
				,'Image' => $post['Image']
				,'Description' => $post['Description']
				,'CompanyEmail' => $post['CompanyEmail']
				,'CompanyPhone' => $post['CompanyPhone']
		);
		return $this->add_record($data);
	}


	public function read($start_row, $per_page)
	{
		$task 	= $this->session->userdata($this->session_name . '_task');
		$value 	= $this->session->userdata($this->session_name . '_value');
		
		$defaultField = 'CompanyID';
		
		switch ($task) {
			case '0' : 
				$where = "  LIKE '%$value%' "; 
				$order_by = " ";
				break;
			case '1' :
				$where = "  = '$value' ";
				$order_by = " ";
				break;
			case '2' :
				$value += 0; //Set int
				$where = " CompanyID = $value ";
				$order_by = " CompanyID";
				break;
			default :
				$where = " $defaultField LIKE '%$value%' "; 
				$order_by = " $defaultField";
				break;
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
		$data = array(
						'total_row' => $total_row, 
						'search_row' => $search_row,
						'list_data' => $this->list_record()
		);
		return $data;
	}


	public function update($post)
	{
		$data = array(
				'CompanyName' => $post['CompanyName']
				,'Image' => $post['Image']
				,'Description' => $post['Description']
				,'CompanyEmail' => $post['CompanyEmail']
				,'CompanyPhone' => $post['CompanyPhone']
		);
		$pk_value = $post['encrypt_id'];
		$check_id = decrypt($pk_value);
		if($check_id != ''){
			$pk_value = $check_id;//ถ้าไม่เป็นค่าว่าง แสดงว่ายังเข้ารหัสอยู่ ให้ใช้ที่ถอดรหัสแล้ว
		}
		$this->set_where("CompanyID = '$pk_value'");
		return $this->update_record($data);
	}


	public function delete($pk_value)
	{
		$check_id = decrypt($pk_value);//ถ้าถอดรหัสมาก่อนแล้ว จะกลายเป็นค่าว่าง
		if($check_id != ''){
			$pk_value = $check_id;//ถ้าไม่เป็นค่าว่าง แสดงว่ายังเข้ารหัสอยู่ ให้ใช้ที่ถอดรหัสแล้ว
		}
		$this->set_where("CompanyID = '$pk_value'");
		return $this->delete_record();
	}


}
/*---------------------------- END Model Class --------------------------------*/