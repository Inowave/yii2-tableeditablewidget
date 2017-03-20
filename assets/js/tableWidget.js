$(function () {

		$(document).on("click", ".ui-datepicker", function(e) {
			e.preventDefault();
			return false;
		});		
		   
		$(document).on("focus", ".editable_time_field", function() {
		
		   var current_field = $(this);
		   
			$(this).timepicker({
				timeFormat: "HH:mm",
				hourGrid: 4,
				minuteGrid: 10,
				//stepHour: 2,
				//stepMinute: 10,
				addSliderAccess: true,
				sliderAccessArgs: { touchonly: false },
				//timeFormat: 'hh:mm tt',
				//hourMin: 8,
				//hourMax: 16
				showButtonPanel : true,
				onClose : function(datetimeText, datepickerInstance) {
					var $form = $(this).closest('form');
		           setTimeout(function() {
		               $form.submit();
		           }, 20);
				},
			});
	
			//------------------
			$(this).inputmask("hh:mm", {
				    placeholder: "чч:мм",
				    onincomplete: function(){
			           $(this).val('');
			       },
			});
			
			$(this).prop('selectionStart', 0).prop('selectionEnd', 0);
		});
		
		

		$(document).on("focus", ".editable-checklist, .editable-radiolist, .editable_select2_field", function() {
			var obj = $(this).closest('.editable-container.editable-inline');
			obj.height('auto');
			obj.children().height('auto');
			obj.find('.editableform').height('auto');
			obj.find('.control-group').height('auto');
			obj.find('.control-group > div').height('auto');
		});
		
		// textarea save on Ctrl+Enter press
		/*$(document).on("keydown", ".editable_textarea_field", function(e) {
			  if (e.ctrlKey && e.keyCode == 13) {
			    // Ctrl-Enter pressed
			    $(this).closest('form').submit();
			  }
		});*/
		
		// select2 save on Enter press
		$(document).on("keyup keypress keydown", ".select2-container input.select2-input", function(e) {
			if(e.keyCode === 13) {
				 $(this).closest('form').submit();
				 $('#select2-drop').hide();
			}
		});
		
		
		// masks of numeriс fields

		$(document).on("keyup keypress keydown", ".editable_int_field, .editable_real_field, .editable_percent_field, .editable_price_field", function(e) {			
			if(e.keyCode === 13) {
				$(this).blur();
				$(this).closest('form').submit();
			}
		});
		
		$(document).on('change', '.editable-file input[type=file]',function(e) {
			// select the form and submit
			$(this).closest('form').submit();
		});		
		
		function getRealNumberMask(data_obj) {
			// https://github.com/RobinHerbots/Inputmask/blob/3.x/README_numeric.md
			var mask_obj = {
				alias: 'numeric',
				autoGroup: true,
				//autoGroup: false,
				rightAlign: false,
				onincomplete: function(){
				  $(this).val('0');
			   },
			   };
			
			// min val			
			if(!(typeof data_obj.numbermin === "undefined") && $.isNumeric( data_obj.numbermin )) {
       	 		$.extend( mask_obj,{'min':parseFloat(data_obj.numbermin)});	
       	 	}
			// max val
			if(!(typeof data_obj.numbermax === "undefined") && $.isNumeric( data_obj.numbermax )) {
       	 		$.extend( mask_obj,{'max':parseFloat(data_obj.numbermax)});	
       	 	}
			// groupSeparator
			if(!(typeof data_obj.numberseparator === "undefined")) {
       	 		$.extend( mask_obj,{'groupSeparator':data_obj.numberseparator});	
       	 	} else {
				$.extend( mask_obj,{'groupSeparator':' '});	
			}
			
			// radixPoint
			if(!(typeof data_obj.numberradix === "undefined")) {
       	 		$.extend( mask_obj,{'radixPoint':data_obj.numberradix});	
       	 	} else {
				$.extend( mask_obj,{'radixPoint':'.'});
			}
			
			// digits
			if(!(typeof data_obj.numberdigits === "undefined") && $.isNumeric( data_obj.numberdigits )) {
       	 		$.extend( mask_obj,{'digits':data_obj.numberdigits});	
				$.extend( mask_obj,{'digitsOptional':false});
       	 	} else {
				$.extend( mask_obj,{'digits':0});	
			}
			// allowMinus
			if(!(typeof data_obj.numberneg === "undefined")) {
       	 		$.extend( mask_obj,{'allowMinus':data_obj.numberneg});	
       	 	}
			// integerDigits
			if(!(typeof data_obj.numberlen === "undefined") && $.isNumeric( data_obj.numberlen )) {
       	 		$.extend( mask_obj,{'integerDigits':data_obj.numberlen});	
       	 		//$.extend( mask_obj,{'integerOptional':false});				
       	 	}
			
			// prefix
			if(!(typeof data_obj.numberprefix === "undefined")) {
       	 		$.extend( mask_obj,{'prefix':data_obj.numberprefix});	
       	 	}
			// suffix
			if(!(typeof data_obj.numbersuffix === "undefined")) {
       	 		$.extend( mask_obj,{'suffix':data_obj.numbersuffix});	
       	 	}
			
			//console.log(mask_obj);
			return mask_obj;

		}
	
		
		function countTableSize() {
			// table height
			$(".tablewidget").each(function(){
				var head_table = $(this).find('.headers_table').eq(0);
				var body_table = $(this).find('.body_table');
				$(this).find('.table_body_wrap').height($(this).height() - head_table.height());
				body_table.width(head_table.width());
				
				head_table.find('th').each(function(i){
					if($(this).attr("style")==undefined || $(this).attr("style").indexOf("width") <0 ) {
						var wid = $(this).width();
						$(this).width(wid);
						body_table.find('tr').each(function(){
							$(this).find('td:eq('+i+')').width(wid);
						});						
					}
				});
				
				head_table.find('th').each(function(i){
					var th_wid = $(this).innerWidth();
					body_table.find('tr').each(function(){
						$(this).find('td:eq('+i+')').children('span').innerWidth(th_wid);
					});
				});
				
			});
		}
		countTableSize();
		$(window).on('resize', countTableSize);
		
		$('.tablewidget_wrap').each(function(){
			var table_obj = $(this).find('.tablewidget');
			$(this).find(".collist_list input[type='checkbox']").change(function() {
				var obj_list = table_obj.find('.'+$(this).val()); 
			    if(this.checked) {
			        //Do stuff
			        obj_list.show();
			    } else {
			    	obj_list.hide();
			    }
			});
		});
		
		$.fn.editable.defaults.mode = 'popup';
		$.fn.editable.defaults.emptytext =  'Пусто';
		$.fn.editable.defaults.toggle = 'dblclick';
		$.fn.editable.defaults.showbuttons = false;
		$.fn.editable.defaults.onblur = 'submit';
		
		$('.tablewidget').each(function(){
			var index = $(this).index();
			$(this).attr('id', 'tablewidget_'+index);			
			
			$(this).find('.body_table td').each(function(i){
				$(this).children('span').attr('id', 'tablewidget_td_'+index+'_'+i );
				
				var table_editable = $(this);
				$(this).children('span').dblclick(function(e){
					// get data from server
					e.preventDefault();
					var data_editable = {};
					
					var current_obj = $(this);
					var data = {'id': table_editable.data('strnum'), 'name': table_editable.attr('name'), 'type': table_editable.data('type') } ;
					var data_type = table_editable.data('type');
					
					$.ajax({
					    url : '/tablewidget/tablewidgetfield',
					    method : 'post',
					    dataType : 'json', 
					    data : data,
					    success : function(data) {
					    	//console.log(JSON.stringify(data));
					    	if(data['message']) {
					    		alert(data.message);
					    		return;
					    	}
					    	if(!data['data']) {
					    		alert('No data!!!');
					    		return;
					    	}
					    	
					    	    	
					       if(data.data.readonly==1) {
					       	current_obj.text('Uneditable');
					       } else {
					       	 if(data.data.select) { // type == select
					       	 	if(data_type=='checklist') {
					       	 		//------------------
					       	 		 /*display checklist as comma-separated values*/ 
					       	 		 var object_check = {
					       	 		 	 display: function(value, sourceData) {
									    //display checklist as comma-separated values
									    var html = [],
									      checked = $.fn.editableutils.itemsByValue(value, sourceData);
									
									    if (checked.length) {
									      $.each(checked, function(i, v) {
									        html.push($.fn.editableutils.escape(v.text));
									      });
									      $(this).html(html.join(', '));
									    } else {
									      $(this).empty();
									    }
									  }
					       	 		 };
					       	 		
					       	 		$.extend( data.data.select, object_check );					       	 		
								  //----------------
								}
					       	 		$.extend( data_editable, data.data.select );
					       	 	}
					       	 
					       	 //-----read params from data attribute-----------------------
					       	 $.extend( data_editable,current_obj.parent().data());
					       	 
					       	 // url of ajax save
					       	 $.extend( data_editable,{
					       	 	 savenochange : true,
					       	 	 send : 'always',
							     /*ajaxOptions: {
									//type: 'put',
									type: 'post',
									dataType: 'json',
									contentType: 'application/json'
								},*/
								/*params: function(params) { 
									//return JSON.stringify(params); 
									var data = {};
									data[params.name] = params.value;
									return JSON.stringify(data);
								},*/
					       	 	 url : '/tablesavedata/tablesave',
					       	 	 //pk: 1,  
					       	     success: function(response, newValue) {
					       	     	//console.log('success');
					       	     	//console.log(response);
							        //if(response.status == 'error') {
						        	if(response.status == 'error') {
										return response.msg; //msg will be shown in editable form
									}
									
									if(response.status == 'OK') {
									     if(response.new_file) { // file upload field
									    	$(this).html('<a href="'+response.new_file+'">'+response.new_file+'</a>');
									     }
										return true;
									}
							    },
						        error: function(response, newValue) {
						        	//console.log('error');
						        	//console.log(response);
							        if(response.status === 500) {
							            return 'Service unavailable. Please try later.';
							        } else {
							            return response.msg;
							        }
							    }
							    
					       	 });
					       	  
					       	 //---/--read params from data attribute----------------------
					       	 
					       	 // time type 
					       	 if(data_type=='time') {
								$.extend( data_editable,{'inputclass':'editable_time_field'});
					       	 } else if(data_type == 'date') {
							 
								if(!(typeof data_editable.mode === "undefined") && data_editable.mode=='inline') {
									// datepicker onsubmit -  save value in table <td>
									var object_date = {
											onClose : function(dateText, datepickerInstance) {
												var $form = $(this).closest('form');
												setTimeout(function() {
												   $form.submit();
											   }, 20);
											},										
									};
									$.extend( data_editable.datepicker,object_date);
								}
								
					       	 	$.extend( data_editable,{'inputclass':'editable_date_field'});
					       	 	
					       	 	//console.log(JSON.stringify(data));
					       	 } else if(data_type == 'textarea') {
					       	 	$.extend( data_editable,{'showbuttons':true, 'inputclass':'input-large editable_textarea_field', 'mode':'popup'});
					       	 }  else if(data_type == 'select2') {
					       	 	//$.extend( data_editable,{'onblur':'ignore'});
					       	 	$.extend( data_editable,{'showbuttons':true});
					       	 } else if(data_type == 'checklist') {
					       	 	$.extend( data_editable,{'showbuttons':true, 'mode':'popup'});
					       	 } else if(data_type == 'radiolist') {
					       	 	//console.log(data_editable);
					       	 	if(typeof data_editable.showbuttons === "undefined") {
					       	 		$.extend( data_editable,{'showbuttons':true});	
					       	 	}
					       	 	if(!(typeof data_editable.instring === "undefined") && data_editable.instring==true) {
					       	 		$.extend( data_editable,{'inputclass':'editable_inlineradiolist'});	
					       	 	}					       	 	
					       	 	
					       	 	$.extend( data_editable,{'mode':'popup'});
					       	 } else if(data_editable.inputclass=='editable_int_field' || data_editable.inputclass=='editable_real_field' || data_editable.inputclass=='editable_percent_field' || data_editable.inputclass=='editable_price_field') {
					       	 } else if(data_type == 'file') {
								$.extend( data_editable,{display: function(value, response) {
								  return false;   //disable this method
								},});	
					       	 }
							 
				       	 
					       	 // --- table cell width -----------------
					       	 if(table_editable.width() < 80) {
					       	 	$.extend( data_editable,{'mode':'popup'});
					       	 }
					       	 // -/-- table cell width -----------------
					       	 
					       	 //console.log(data_editable);
					       	 current_obj.editable(data_editable);	
							 current_obj.editable('show');	
							 
							 if(data_type == 'select2') {
								// select2 needs buttons  - without them it closes after each selection (buttons exist but hidden)
								$('.select2-container').closest('.control-group').find('.editable-buttons').hide();
							 }
							 
							 //-----datepicker inline------------
							 else if(data_type=='date') {
								if(!(typeof data_editable.mode === "undefined") && data_editable.mode=='inline') {
									$('.hasDatepicker:not(.editable_time_field)').inputmask("dd.mm.yyyy", {
												placeholder: "дд:мм:гггг",
												onincomplete: function(){
												   $(this).val('');
											   },
									});
									$('.hasDatepicker:not(.editable_time_field)').prop('selectionStart', 0).prop('selectionEnd', 0);
								}
							 }
							 //----/-datepicker inline-----------
							 else if(data_type=='time') {
							 }
							 
							 //-------numeric masks------------------------
							 else if(data_editable.inputclass=='editable_int_field' || data_editable.inputclass=='editable_real_field' || data_editable.inputclass=='editable_percent_field' || data_editable.inputclass=='editable_price_field') {
								var mask_obj = getRealNumberMask(data_editable);
								if(mask_obj) {
									//console.log(mask_obj);
									$('.'+data_editable.inputclass).inputmask(mask_obj);									
								}
								$('.'+data_editable.inputclass).prop('selectionStart', 0).prop('selectionEnd', 0);
							 }
							 //----/--numeric masks------------------------
							 else if(data_type=='file') {
					       	 	if(!(typeof data_editable.inputclass === "undefined") && data_editable.inputclass == 'editable_imageupload_field') {
									$('.editable-file .image_upload').val(1);
					       	 	}
					       	 	
					       	 	if(!(typeof data_editable.filetypes === "undefined")) {
									$('.editable-file .file_type').val(data_editable.filetypes);
					       	 	}
								
					       	 } else if(data_type=='radiolist') {
					       	 	if(!(typeof data_editable.inputclass === "undefined") && data_editable.inputclass == 'editable_inlineradiolist') {
					       	 		$('.editable_inlineradiolist').on('change',function(){
									  $(this).closest('form').submit();
									});
					       	 	}
					       	 }	
					        }
					    },
					    error : function(e, status) {
					        console.log(status + ' - ' + e.statusText);
					    }
					});
					
					//return false;					
				});
			});
		});
});
		