<?php
/**
 * A base model with a series of CI Generator
 *
 * @link http://phpcodemania.blogspot.com
 * @copyright Copyright (c) 2018, SONGCHAI SAETERN
 */
class MY_Model extends CI_Model
{
    private $_table_name;
	private $select_field;
    private $where;
    private $order_by;
    private $offset;
    private $limit;
	public $error_message;

    /**
     * Initialise the model, load database with session or default
     */
    public function __construct()
    {
        parent::__construct();

		$this->select_field = '';
        $this->where      = '';
        $this->order_by   = '';
        $this->offset     = '';
        $this->limit      = '';        
    }
	
    /**
     * Count reord
     * @return Number of record
     */
    public function count_record()
    {
    	$num = 0;
    	$this->set_query_parameter();
    	if($query = $this->db->get($this->_table_name)){
    		$num = $query->num_rows();
    	}
    	return $num;
    }	
	
    /**
     * List all record with condition
     * @return Array multiple record
     */
    public function list_record()
    {
    	$data = array();
        $this->set_query_parameter();
		$this->db->from($this->_table_name);
        if($query = $this->db->get()){
        	$data = $query->result_array();
        }
        return $data;
    }

	/**
	 * C = Create Record
	 * @param array $data
	 * @return Integer of new id record 
	 */
    public function add_record($data = array())
    {
		$this->db->set($data);
        $query = $this->db->insert($this->_table_name);
        return $this->db->insert_id();
    }
	
    /**
     * R = Read Record Data
     * @return Array on record
     */
    public function load_record()
    {
		if($this->where == ''){
			return 'Choose WHERE Clause for load record data';
		}else{
			$this->set_query_parameter();
			$query = $this->db->get_where($this->_table_name);
			return $query->row_array();
		}
    }

    /**
     * U = Update record data
     * @param array $data
     */
    public function update_record($data = array())
    {
		if($this->where == ''){
			$this->error_message = 'Choose WHERE Clause for Update / Delet';
			return false;
		}else{
			$this->log_edit_history();
			
			$this->set_query_parameter();
			$this->db->set($data);
			$results = $this->db->update($this->_table_name);
			return $results;
		}
    }

    /**
     * D = Delete record 
     */
    public function delete_record()
    {
		if($this->where == ''){
			return 'Choose WHERE Clause for Update / Delet';
		}else{
			//Move to LOG
			if($this->log_delete()){
				//Delete Record
				$this->set_query_parameter();
				return $this->db->delete($this->_table_name);
			}else{
				$this->error_message = 'Could not create LOG';
			}
		}
    }
	
	private function log_edit_history()
    {
		$n=0;
		if($pk_field = $this->get_primary_key()){
			//จะทำงานเมื่อพบ PRIMARY Key เท่านั้น
			$pk_field_name = $pk_field;
			$num_pk = count($pk_field);
			if ($num_pk > 1){//if array
				$pk_field_name = implode(',', $pk_field);//ใช้กับ CONCAT_WS(',', {$log_table_pk})
			}
			
			$log_edit_remark = $this->input->post('edit_remark', TRUE);
			
			$n = 0;
			$this->set_query_parameter();
			$this->db->select($pk_field_name);//หา ID เท่านั้น  ไม่ต้องเก็บข้อมูล
			$query = $this->db->get($this->_table_name);
			foreach ($query->result_array() as $row){
				if ($num_pk > 1){
					$pk_value = array();
					foreach($pk_field as $pk_name){
						$pk_value[] = isset($row[$pk_name]) ? $row[$pk_name] : '';
					}
					$log_table_id = implode(',', $pk_value);
				}else{
					$log_table_id = isset($row[$pk_field]) ? $row[$pk_field] : '';
				}

				$data = array(
					'log_edit_remark' => mb_substr($log_edit_remark,0,50,'UTF-8')
					,'log_edit_table' => $this->_table_name
					,'log_edit_table_pk_name' => $pk_field_name
					,'log_edit_table_pk_value' => $log_table_id
					,'log_edit_condition' => mb_substr($this->where,0,100,'UTF-8')
					,'log_edit_user' => $this->session->userdata('user_id')
					,'log_edit_datetime' => date('Y-m-d H:i:s')
					,'log_login_id' => $this->session->userdata('login_id')
				);
				$this->db->set($data);
				$this->db->insert('tb_log_history');
				$n++;
			}
			return $n;
		}
	}
	
	private function log_delete()
    {
		//table, del condition, data
		$n=0;
		if($pk_field = $this->get_primary_key()){
			$log_del_remark = $this->input->post('delete_remark', TRUE);
			
			$pk_field_name = $pk_field;
			$num_pk = count($pk_field);
			if ($num_pk > 1){//if array
				$pk_field_name = implode(',', $pk_field);//ใช้กับ CONCAT_WS(',', {$log_table_pk})
			}
			//จะทำงานเมื่อพบ PRIMARY Key เท่านั้น
			$this->set_query_parameter();
			$query = $this->db->get($this->_table_name);
			foreach ($query->result_array() as $row){
				if ($num_pk > 1){
					$pk_value = array();
					foreach($pk_field as $pk_name){
						$pk_value[] = isset($row[$pk_name]) ? $row[$pk_name] : '';
					}
					$log_table_id = implode(',', $pk_value);
				}else{
					$log_table_id = isset($row[$pk_field]) ? $row[$pk_field] : '';
				}
				
				$log_record_data = json_encode($row);

				$data = array(
						'log_del_remark' => mb_substr($log_del_remark,0,50,'UTF-8')
					  ,'log_table_name' => $this->_table_name
					  ,'log_del_condition' => mb_substr($this->where,0,100,'UTF-8')
					  ,'log_table_pk_name' => $pk_field_name
					  ,'log_table_pk_value' => $log_table_id
					  ,'log_record_data' => $log_record_data
					  ,'create_user_id' => $this->session->userdata('user_id')
					  ,'create_datetime' => date('Y-m-d H:i:s')
					  ,'log_login_id' => $this->session->userdata('login_id')
				);
				$this->db->set($data);
				$this->db->insert('tb_log_delete');
				$n++;
			}
		}
        return $n;
	}
	
	private function get_primary_key()
    {
		$sql = "SHOW KEYS FROM $this->_table_name WHERE Key_name = 'PRIMARY'";
		$query = $this->db->query($sql);
		if( $query->num_rows() > 1){
			$all_field = array();
			foreach ($query->result() as $row){
				$all_field[] = $row->Column_name;
			}
			return $all_field;
		}else{
			$row = $query->row();
			if (!empty($row)){
				return $row->Column_name;
			}
		}
	}

    /**
     * Set table name from model
     * @param String $table_name
     */
    public function set_table_name($table_name)
    {
        $this->_table_name = $table_name;
    }
	
	/**
     * Set table name from model
     * @param String $table_name
     */
    public function set_select_field($fields_name)
    {
        $this->select_field = $fields_name;
    }

    /**
     * Set condition for query
     * @param string $where
     */
    public function set_where($where)
    {
        $this->where = $where;
    }
	
	public function show_where()
    {
        echo $this->where;
    }
	

    /**
     * Set order by of query
     * @param string $order_by
     */
    public function set_order_by($order_by)
    {
        $this->order_by = $order_by;
    }

    /**
     * Set start fetch row
     * @param Integer $start_row
     */
    public function set_offset($start_row)
    {
        $this->offset = $start_row;
    }

    /**
     * Set limit fetch row
     * @param Integer $limit
     */
    public function set_limit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Set condition before query
     */
    public function set_query_parameter()
    {
		if($this->select_field != ''){
            $this->db->select($this->select_field);
        }
        if($this->where != ''){
            $this->db->where($this->where);
        }
        if($this->order_by != ''){
            $this->db->order_by($this->order_by);
        }
        if($this->offset != ''){
            $this->db->offset($this->offset);
        }
        if($this->limit != ''){
            $this->db->limit($this->limit);
        }
    }

}