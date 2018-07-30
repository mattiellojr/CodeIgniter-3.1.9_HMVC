
var Department = {

	current_page : 0,

	confirmDelete: function (pDeptid,  irow){
		$('[name="encrypt_dept_id"]').val(pDeptid);

		$('#xrow').text(irow);
		var my_thead = $('#row_' + irow).closest('table').find('th:not(:first-child):not(:last-child)');
		var th = [];
		my_thead.each (function(index) {
			th.push($(this).text());
		});
		
		var active_row = $('#row_' + irow).find('td:not(:first-child):not(:last-child)');
		var detail = '<table class="table table-striped">';
		active_row.each (function(index) {
				detail += '<tr><td align="right"><b>' + th[index] + ' : </b></td><td> ' + $(this).text() + '</td></tr>';
		});
		detail += '</table>';
		$('#div_del_detail').html(detail);

		$('#confirmDelModal').modal('show');
	},
    
	// delete by ajax jquery 
	deleteRecord: function(){
		var frm_action = site_url('company/department/del'); 
		var fdata = $('#formDelete').serialize();
		fdata += '&' + csrf_token_name + '=' + $.cookie(csrf_cookie_name);
		var obj = $('#btn_confirm_delete');
		loading_on(obj);
		$.ajax({
			method: 'POST',
			url: frm_action,
			dataType: 'json',
			data : fdata,
			success: function (results) {
				if(results.is_successful){
					alert_type = 'success';
					setTimeout(function(){ 
						$(window.location).attr('href', site_url('company/department/index/'+ this.current_page));
					}, 500);
				}else{
					alert_type = 'danger';
				}
				notify('ลบรายการ', results.message, alert_type, 'center');
				loading_on_remove(obj);
			},
				error : function(jqXHR, exception){
				loading_on_remove(obj);
				ajaxErrorMessage(jqXHR, exception);
			}
		});
	},

	// load preview to modal 
	loadPreview: function(id){ 
		$.ajax({
			method: 'GET',
			url: site_url('company/department/preview/'+ id),
			success: function (results) {
				$('#divPreview').html(results);
			},
			error : function(jqXHR, exception){
				ajaxErrorMessage(jqXHR, exception);
			}
		});
		$('#modalPreview').modal('show');
	},
	
	validateFormEdit: function(){
		if($('#edit_remark').val().length < 5){
				notify('กรุณาระบุเหตุผล', 'เหตุผลการแก้ไขจะต้องระบุให้ชัดเจน', 'warning', 'center', 'bottom');
		}else{
				this.saveEditForm();
		}
		return false;
	},

	saveFormData: function(){
		var frm_action = site_url('company/department/save');
		var fdata = $('#formAdd').serialize();
		fdata += '&' + csrf_token_name + '=' + $.cookie(csrf_cookie_name);

		var obj = $('#btnConfirmSave');
		loading_on(obj);		
		$.ajax({
			method: 'POST',
			url: frm_action,
			dataType: 'json',
			data : fdata,
			success: function (results) {
				if(results.is_successful){
					alert_type = 'success';
					$('#formAdd')[0].reset();
				}else{
					alert_type = 'danger';
				}
					notify('เพิ่มข้อมูล', results.message, alert_type, 'center');
					loading_on_remove(obj);
				},
			error : function(jqXHR, exception){
				ajaxErrorMessage(jqXHR, exception);
					loading_on_remove(obj);
			}
		});
	},

	saveEditForm: function(){
		$('#editModal').modal('hide');
		var frm_action = site_url('company/department/update');
		var fdata = $('#formEdit').serialize();
		fdata += '&edit_remark=' + $('#edit_remark').val();
		fdata += '&' + csrf_token_name + '=' + $.cookie(csrf_cookie_name);

		var obj = $('#btnSaveEdit');
		loading_on(obj);
		$.ajax({
			method: 'POST',
			url: frm_action,
			dataType: 'json',
			data : fdata,
			success: function (results) {
				if(results.is_successful){
					alert_type = 'success';
				}else{
					alert_type = 'danger';
				}
				notify('บันทึกข้อมูล', results.message, alert_type, 'center');
				loading_on_remove(obj);
			},
			error : function(jqXHR, exception){
				ajaxErrorMessage(jqXHR, exception);
				loading_on_remove(obj);
			}
		});
	}
}

$(document).ready(function() {
	$('.my-tooltip').tooltip('hide');

	$('#btnSave').click(function() {            
		$('#addModal').modal('hide');
		Department.saveFormData();
		return false;            
	});//click

	$('#btnSaveEdit').click(function() {            
		return Department.validateFormEdit();
	});//click

	//List view
	if(typeof param_search_field != 'undefined'){
		$('select[name="search_field"] option[value="'+ param_search_field +'"]').attr('selected','selected');
	}

	if(typeof param_current_page != 'undefined'){
		Department.current_page = Math.abs(param_current_page);
	}

	$('.btn-delete-row').click(function() {
		$('.btn-delete-row').removeClass('active_del');
		$(this).addClass('active_del');
		var row_num = $(this).attr('data-row-number');
		var pDeptid = $(this).attr('data-dept_id');

		Department.confirmDelete(pDeptid,  row_num);
	});//click

	$('#btn_confirm_delete').click(function(){
		Department.deleteRecord();
	});

});//ready