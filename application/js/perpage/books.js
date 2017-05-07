/*
	Copyright 2016-2017 Cédric Levieux, Parti Pirate

	This file is part of GererMaCampagne.

    GererMaCampagne is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GererMaCampagne is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GererMaCampagne.  If not, see <http://www.gnu.org/licenses/>.
*/
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

function addModifyInlineHandlers() {
	$("#inline-table").on("click", "span.amount", function(event) {
		var inlineId = $(this).data("inline-id");
		var inline = $("#inline-table .inline[data-id=" + inlineId + "]");
		var inlineCode = inline.data("inline-code");
		var campaignId = $("#campaignId").val();

		var span = $(this);
		span.data("amount", span.html());

		var input = $("<input value='' class='text-right' style='width: 60px; height: 16px;'>");
		input.val(span.data("amount"));
		
		var closerButton = $("<button class='btn btn-danger btn-xxs btn-left-straight'><span class='glyphicon glyphicon-remove'></span></button>");

		span.html("");
		span.append(input);
		span.append(closerButton);
		
		var updater = function(event) {
			if (event.type != "keydown" && (event.target == input.get(0) || event.target == span.get(0))) {
				event.stopPropagation();
				return;
			}
			
			if (event.target == closerButton.get(0) || event.target == closerButton.find("span").get(0)) {
				closer();
				
				event.stopPropagation();
				return;
			}

			var value = input.val();

			if (value != span.data("amount")) {
				var form = {property: "bin_amount", inlineCode: inlineCode, inlineId: inlineId, value: value, campaignId: campaignId};
				$.post("do_updateInline.php", form, function(data) {
					input.remove();
				
					span.html(value);
					span.data("amount", value);
		
					$("*").off("click", updater);
				}, "json");
			}
			else {
				closer();
			}
		};
		
		var closer = function(event) {
			var value = span.data("amount");
			input.remove();
		
			span.html(value);

			$("*").off("click", updater);
		}

		$("*").on("click", updater);
		input.keydown(function(event) {
			 if(event.keyCode == 13) {
			 	event.preventDefault();
				
				updater(event);
			 }
		})
	});

	$("#inline-table").on("click", "span.inline-label", function(event) {
		var inlineId = $(this).data("inline-id");
		var inline = $("#inline-table .inline[data-id=" + inlineId + "]");
		var inlineCode = inline.data("inline-code");
		var campaignId = $("#campaignId").val();

		var span = $(this);
		span.data("label", span.html());

		var input = $("<input value='' class='text-left' style='width: 140px; height: 16px; margin-left: -2px;'>");
		input.val(span.data("label"));
		
		var closerButton = $("<button class='btn btn-danger btn-xxs btn-left-straight'><span class='glyphicon glyphicon-remove'></span></button>");

		span.html("");
		span.append(input);
		span.append(closerButton);
		
		var updater = function(event) {
			if (event.type != "keydown" && (event.target == input.get(0) || event.target == span.get(0))) {
				event.stopPropagation();
				return;
			}
			
			if (event.target == closerButton.get(0) || event.target == closerButton.find("span").get(0)) {
				closer();
				
				event.stopPropagation();
				return;
			}

			var value = input.val();

			if (value != span.data("label")) {
				var form = {property: "bin_label", inlineCode: inlineCode, inlineId: inlineId, value: value, campaignId: campaignId};
				$.post("do_updateInline.php", form, function(data) {
					input.remove();
				
					span.html(value);
					span.data("label", value);
		
					$("*").off("click", updater);
				}, "json");
			}
			else {
				closer();
			}
		};
		
		var closer = function(event) {
			var value = span.data("label");
			input.remove();
		
			span.html(value);

			$("*").off("click", updater);
		}

		$("*").on("click", updater);
		input.keydown(function(event) {
			 if(event.keyCode == 13) {
			 	event.preventDefault();
				
				updater(event);
			 }
		})
	});

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
		$("#addInvoiceForm #inlineDate").val(selectedOption.attr("data-date"));
	});

	$("#invoiceSelect").change(function() {
		var selectedOption = $("#invoiceSelect option:selected");

		$("#payInvoiceForm #amount").val(selectedOption.attr("aria-amount"));
		$("#payInvoiceForm #inlineDate").val(selectedOption.attr("data-date"));
	});

	$('#addInvoiceDiv #inlineDate, #payInvoiceDiv #inlineDate, #addQuotationDiv #inlineDate, #declareDonationDiv #inlineDate').parent("div").datetimepicker({
    	language: userLanguage
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

	$("#payInvoiceDiv #payInvoiceButton").click(function(event) {
    	$("#payInvoiceDiv #payInvoiceButton").attr("disabled", "disabled");

	    var formData = new FormData($('#payInvoiceForm')[0]);
	    $.ajax({
	        url: 'do_payInvoice.php',  //Server script to process data
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
	        	$("#payInvoiceDiv").modal('hide');
	        	if (data.ok) {
//	        		window.location.reload(true);
	        	}
	        },
	        data: formData,
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	});

	$("#payInvoiceDiv #payment_type").change(function() {
		var paymentType = $(this).val();

		$("#paymentInvoiceTypeButtons button").removeAttr("disabled");
		
		switch(paymentType) {
			case "DA":
			case "DB":
				$("#paymentInvoiceTypeButtons button[value=nature]").attr("disabled", "disabled");
				$("#paymentInvoiceTypeButtons button[value=check]").click();
				break;
			default:
				$("#paymentInvoiceTypeButtons button[value!=nature]").attr("disabled", "disabled");
				$("#paymentInvoiceTypeButtons button[value=nature]").click();
				break;
		}
	});


	$("#paymentInvoiceTypeButtons button").click(function(e) {
		$("#paymentInvoiceTypeButtons button").removeClass("active");
		$(this).addClass("active");
		$("#paymentInvoiceType").val($(this).val());
		
		/* TODO
		if ($(this).val() == "check") {
			$("#checkFileDiv").show();
		}
		else {
			$("#checkFileDiv").hide();
		}
		*/
	});

	$(".btn-add-invoice").click(function(event) {
		$("input[name=invoiceSourceRadios]").removeAttr("checked");
		if ($("input[name=invoiceSourceRadios]").length > 1) {
			$("#invoiceSource").val("");
		}
		checkInvoiceSource();
		$("#quotationSelect").val(0);
		$("#quotationSelect").change();
	});

	$("#inline-table").on("click", ".inline .btn-pay-invoice", function(event) {
		var inlineId = $(this).data("inline-id");
		var inline = $("#inline-table .inline[data-id=" + inlineId + "]");
		var label = inline.find(".inline-label").text();

		$("#payInvoiceDiv").modal("show");

		$("#invoiceSelect").val(inlineId);
		$("#invoiceSelect").change();
		$("#payInvoiceDiv #payment_type").change();
	});

	$("#inline-table").on("click", ".inline .btn-set-invoice", function(event) {
		var inlineId = $(this).data("inline-id");
		var inline = $("#inline-table .inline[data-id=" + inlineId + "]");
		var label = inline.find(".inline-label").text();

		$("#addInvoiceDiv").modal("show");
		$("#fromQuotationRadio").click();
		$("#quotationSelect").val(inlineId);
		$("#quotationSelect").change();
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

	$("#paymentTypeButtons button").click(function(e) {
		$("#paymentTypeButtons button").removeClass("active");
		$(this).addClass("active");
		$("#paymentType").val($(this).val());
		
		if ($(this).val() == "check") {
			$("#checkFileDiv").show();
		}
		else {
			$("#checkFileDiv").hide();
		}
	});

	$(".modal #closeButton").click(function(event) {
    	$(this).parents(".modal").modal('hide');
	});

	addModifyInlineHandlers();
});
