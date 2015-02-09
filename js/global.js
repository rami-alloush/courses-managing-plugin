(function( $ ) {
  "use strict";
	$(function() {
	 
		$( ".mydatepicker" ).datepicker({dateFormat: "yy-mm-dd"}); //datepicker({changeYear : true}) // .This is input field	
		// $( "#part_code_source" ).autocomplete({source: cmp_localized_vars.DataToSendToJS});
		// var cars = ["Saab", "Volvo", "BMW"]; $( "#part_code_source" ).autocomplete({source: cars}); // Prove Array Concept
		$( "#tabs" ).tabs();
		$( "#tabs-edit" ).tabs();
		$( "#tabs-choose" ).tabs();
	});

}(jQuery));