jQuery(window).load(function(){
	// Datepicker plugin
	flatpickr("input#bn_exp_date", {
	    enableTime: true,
	    //dateFormat: "d-m-Y H:i",
	    minDate: "today",
	});
	// Checkbox for adding expiration date
	jQuery('input#is_bn_exp').on('click', function(){
		if(jQuery(this).prop("checked") == true){
			jQuery('.bn-exp-date').show('');
		}else{
			jQuery('.bn-exp-date').hide('');
			jQuery('.bn-exp-date').val('');
		}
	});
	// Show date field if expiration date feature is already on
	if(jQuery('input#is_bn_exp').prop("checked") == true){
		jQuery('.bn-exp-date').show('');
	}
});