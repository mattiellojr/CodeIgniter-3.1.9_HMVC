<?php
function insert_csrf_field($return=false){
	$CI =& get_instance();
	$csrf = array(
		'name' => $CI->security->get_csrf_token_name(),
		'hash' => $CI->security->get_csrf_hash()
	);
	$input = '<input type="hidden" name="'. $csrf['name'] .'" value="'. $csrf['hash'] .'" />';
	if($return == true){
		return $input;
	}else{
		echo $input;
	}
	
}

function addTabs($num){
    return str_repeat("\t", $num);
}

function set_single_qoute($field_type){
    $string = '';
    if($field_type != 'int' && $field_type != 'float' && $field_type != 'double'){
        $string = "'";
    }
    return $string;
}

function setDateFormat($date){//สร้างรูปแบบของวันที่ yyyy-mm-dd
	$y = '';
	$m = '';
	$d = '';
	if($date!=''){
		//ZAN@2017-06-20
		$dte = $arrDate = explode(" ", $date);
		$date = $dte[0];
		if(preg_match("/^([0-9]{1,2})\-([0-9]{1,2})\-([0-9]{4})$/",$date,$arr) || preg_match("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/",$date,$arr) ){
			//ถ้าเป็น xx-xx-yyyy หรือ xx/xx/yyyy
			$y = $arr[3];
			$m = sprintf("%02d",$arr[2]);
			$d = sprintf("%02d",$arr[1]);
		}else if(preg_match("/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/",$date,$arr) || preg_match("/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})$/",$date,$arr)){
			//ถ้าเป็น yyyy-xx-xx หรือ yyyy/xx/xx
			$y = $arr[1];
			$m = sprintf("%02d",$arr[2]);
			$d = sprintf("%02d",$arr[3]);
		}
	}
	if(($y!="" && $m != "" && $d != "") and ($y!= '0000' && $m != '00' && $d != '00')){
		return $y."-".$m."-".$d; //คืนค่า ปี-เดือน-วัน
	}
}

// DD/MM/YYYY+543 ??:??:??
function setDateToThai($date){
    if($date == '') return $date;
    $arr    = explode(' ', $date);
    $time   = isset($arr[1]) ? ' ' . $arr[1] : '';

    $new_format = setDateFormat($arr[0]);
	$dte    = explode('-', $new_format);
	$y      = (isset($dte[0]) && $dte[0] > 0) ? $dte[0]+543 : '-';
	$m      = isset($dte[1]) ? $dte[1] : '-';
	$d      = isset($dte[2]) ? $dte[2] : '-';
	return $d.'/'.$m.'/'. $y . $time;
}

// YYYY-MM-DD ??:??:??
function setDateToStandard($date){
	if($date=='') return $date;
    $dateA = explode(' ', $date);
    $time   = isset($dateA[1]) ? ' ' . $dateA[1] : '';

    $new_format = setDateFormat($dateA[0]);
	$arrD   = explode('-', $new_format);
    $y      = (isset($arrD[0]) && $arrD[0] > 0) ? $arrD[0] - 543 : $arrD[0];
	$m      = isset($arrD[1]) ? $arrD[1] : '/';
	$d      = isset($arrD[2]) ? $arrD[2] : '/';
	return $y .'-'.$m.'-'.$d . $time;
}

function stringToNumber($val){
	$val = str_replace(",","",$val);
	return floatval($val);
}

function getValueAll($table, $field_value, $field_text, $where = '', $db=NULL){
	if($db===NULL){
		$CI =& get_instance();
		$db = $CI->db;
	}
	if($where!='') $where = "WHERE ". $where;

	$sql = "SELECT $field_value, $field_text FROM $table $where";
	$qry = $db->query($sql);
	$data = array();
	foreach ($qry->result_array() as $row) {
		$data[$row[$field_value]] = $row[$field_text];
	}
	return $data;
}

function getValueOf($table, $field_select, $where = '', $db = NULL){
	if($db === NULL){
		$CI =& get_instance();
		$db = $CI->db;
	}
	if($where != '') $where = "WHERE ". $where;
	$sql = "SELECT $field_select FROM $table $where LIMIT 1";
	$qry = $db->query($sql);
	if($row = $qry->row_array()){
		return $row[$field_select];
	}
}

function getRowOf($table, $field_select='*', $where = '', $db = NULL){
	if($db === NULL){
		$CI =& get_instance();
		$db = $CI->db;
	}
	if($where != '') $where = "WHERE ". $where;
	$sql = "SELECT $field_select FROM $table $where LIMIT 1";
	$qry = $db->query($sql);
	return $qry->row_array();
}

function optionList($table, $field_value, $field_text, $condition = array(), $db=NULL){
	if($db===NULL){
		$CI =& get_instance();
		$mydb = $CI->db;
	}else{
		$mydb = $db;
	}
	$where = '';
	if(isset($condition['where'])){
		$where = "WHERE ". $condition['where'];
	}
	if(isset($condition['order_by'])){
		$order_by = $condition['order_by'];
	}else{
		$order_by = $field_text;
	}

	$ret = false;
	if(isset($condition['return'])){
		$ret = $condition['return'];
	}

	$select_value = '';
	if(isset($condition['active'])){
		$select_value = $condition['active'];
	}

	$list = '';
	$order_by = 'ORDER BY '. $order_by;
	$sql = "SELECT $field_value, $field_text FROM $table $where $order_by";
	$qry = $mydb->query($sql);
	foreach ($qry->result_array() as $row) {
		$selected = '';
		if($select_value == $row[$field_value]){
			$selected = 'selected="selected"';
		}
		$option = '<option value="'. $row[$field_value] . '" '.$selected.'>' . $row[$field_text] . '</option>';
		if($ret == true){
			$list .= $option;
		}else{
			echo $option;
		}
	}

	if($ret == true){
		return $list;
	}
}

function my_simple_crypt( $string, $action = 'e' ) {
    // you may change these values to your own
    $secret_key = 'my@simple#secret-key234';
    $secret_iv = 'my@simple#secret-iv345';
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }
 
    return $output;
}

function encrypt($string){
	return my_simple_crypt( $string, 'e' );
}

function decrypt($string){
	return my_simple_crypt( $string, 'd' );
}

function checkEncryptData($value){
    $check_id = decrypt($value);//ถ้าถอดรหัสมาก่อนแล้ว จะกลายเป็นค่าว่าง
    if($check_id != ''){
        $value = $check_id;         //ถ้าไม่เป็นค่าว่าง แสดงว่าก่อนหน้านี้ยังเข้ารหัสอยู่ ให้ใช้ค่าที่ถอดรหัสแล้ว
    }
    return $value;
}
?>