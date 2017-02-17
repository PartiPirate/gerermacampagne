/* global $ */
/* global userLanguage */

function progressHandlingFunction(e) {
    if (e.lengthComputable){
        $('progress').attr({value:e.loaded, max:e.total});
        console.log(e.loaded / e.total);
    }
}

function checkInvoiceSource() {
	$("#invoiceProviderFieldset").hide();
	$("#invoiceFieldset").hide();
	$("#invoiceLabelDiv").hide();
	$("#quotationSelectDiv").hide();

	if ($("#invoiceSource").val() == "directInvoice") {
		$("#invoiceFieldset").show();
		$("#invoiceProviderFieldset").show();
		$("#invoiceLabelDiv").show();
	}
	else if ($("#invoiceSource").val() == "fromQuotation") {
		$("#invoiceFieldset").show();
		$("#quotationSelectDiv").show();
	}

}

$(function() {

	$("input[name=invoiceSourceRadios]").click(function() {
		$("#invoiceSource").val($(this).val());
		checkInvoiceSource();
	});

	checkInvoiceSource();

	$("#quotationSelect").change(function() {
		var selectedOption = $("#quotationSelect option:selected");

		$("#addInvoiceForm #amount").val(selectedOption.attr("aria-amount"));
		$("#addInvoiceForm #inlineDate").val(selectedOption.attr("aria-date"));
	});

	$('#addInvoiceDiv #inlineDate, #addQuotationDiv #inlineDate, #declareDonationDiv #inlineDate').parent("div").datetimepicker({
    	language: userLanguage
	});

	$("#declareDonationDiv #closeButton").click(function(event) {
    	$("#declareDonationDiv").modal('hide');
	});

	$("#addQuotationDiv #closeButton").click(function(event) {
    	$("#addQuotationDiv").modal('hide');
	});

	$("#addInvoiceDiv #closeButton").click(function(event) {
    	$("#addInvoiceDiv").modal('hide');
	});

	$("#declareDonationDiv #declareDonationButton").click(function(event) {
    	$("#declareDonationDiv #declareDonationButton").attr("disabled", "disabled");

	    var formData = new FormData($('#declareDonationForm')[0]);
	    $.ajax({
	        url: 'do_declareDonation.php',  //Server script to process data
	        type: 'POST',
	        xhr: function() {  // Custom XMLHttpRequest
	            var myXhr = $.ajaxSettings.xhr();
	            if(myXhr.upload){ // Check if upload property exists
	                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
	            }
	            return myXhr;
	        },
	        //Ajax events
	        success: function(data) {
        		data = JSON.parse(data);
	        	$("#declareDonationDiv").modal('hide');
	        	if (data.ok) {
	        		window.location.reload(true);
	        	}
	        },
	        data: formData,
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	});

	$("#addQuotationDiv #addQuotationButton").click(function(event) {
    	$("#addQuotationDiv #addQuotationButton").attr("disabled", "disabled");

	    var formData = new FormData($('#addQuotationForm')[0]);
	    $.ajax({
	        url: 'do_addQuotation.php',  //Server script to process data
	        type: 'POST',
	        xhr: function() {  // Custom XMLHttpRequest
	            var myXhr = $.ajaxSettings.xhr();
	            if(myXhr.upload){ // Check if upload property exists
	                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
	            }
	            return myXhr;
	        },
	        //Ajax events
	        success: function(data) {
        		data = JSON.parse(data);
	        	$("#addQuotationDiv").modal('hide');
	        	if (data.ok) {
	        		window.location.reload(true);
	        	}
	        },
	        data: formData,
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	});

	$("#addInvoiceDiv #addInvoiceButton").click(function(event) {
    	$("#addInvoiceDiv #addInvoiceButton").attr("disabled", "disabled");

	    var formData = new FormData($('#addInvoiceForm')[0]);
	    $.ajax({
	        url: 'do_addInvoice.php',  //Server script to process data
	        type: 'POST',
	        xhr: function() {  // Custom XMLHttpRequest
	            var myXhr = $.ajaxSettings.xhr();
	            if(myXhr.upload){ // Check if upload property exists
	                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
	            }
	            return myXhr;
	        },
	        //Ajax events
	        success: function(data) {
        		data = JSON.parse(data);
	        	$("#addInvoiceDiv").modal('hide');
	        	if (data.ok) {
	        		window.location.reload(true);
	        	}
	        },
	        data: formData,
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	});

	
	$("#inline-table").on("click", ".inline .btn-remove-inline", function(event) {
		var inlineId = $(this).data("inline-id");
		var inline = $("#inline-table .inline[data-id=" + inlineId + "]");
		var label = inline.find(".inline-label").text();
		var inlineCode = inline.data("inline-code");
		
		$("#removeInlineDiv .inline-label").text(label);
		$("#removeInlineDiv input[name=inlineId]").val(inlineId);
		$("#removeInlineDiv input[name=inlineCode]").val(inlineCode);
		$("#removeInlineDiv").modal("show");
	});

	$("#removeInlineDiv #closeButton").click(function(event) {
    	$("#removeInlineDiv").modal('hide');
	});

	$("#removeInlineDiv #removeInlineButton").click(function(event) {
    	$("#removeInlineDiv #removeInlineButton").attr("disabled", "disabled");

	    var formData = new FormData($('#removeInlineForm')[0]);
	    $.ajax({
	        url: 'do_removeInline.php',  //Server script to process data
	        type: 'POST',
	        xhr: function() {  // Custom XMLHttpRequest
	            var myXhr = $.ajaxSettings.xhr();
	            if(myXhr.upload){ // Check if upload property exists
	                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
	            }
	            return myXhr;
	        },
	        //Ajax events
	        success: function(data) {
        		data = JSON.parse(data);
	        	$("#removeInlineDiv").modal('hide');
	        	if (data.ok) {
	        		window.location.reload(true);
	        	}
	        },
	        data: formData,
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	});

});