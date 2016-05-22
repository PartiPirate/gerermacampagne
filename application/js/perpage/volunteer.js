var donateUrl = "";
var joinUrl = "";

$(function() {
	function changeStatus(id, status, message) {
		var glyphStatus = $("#" + id + "Status");

		glyphStatus.removeClass("otbHidden");

		if (status == "success") {
			glyphStatus.removeClass("glyphicon-remove");
			glyphStatus.addClass("glyphicon-ok");
		}
		else {
			glyphStatus.removeClass("glyphicon-ok");
			glyphStatus.addClass("glyphicon-remove");
		}

		glyphStatus.parents(".has-feedback").removeClass("has-success").removeClass("has-error").addClass("has-" + status);
	}

	function check_firstname() {
		if ($("#firstname").val() == "") {
			changeStatus("firstname", "error", "");
			return false;
		}
		changeStatus("firstname", "success", "");
		return true;
	}

	function check_lastname() {
		if ($("#lastname").val() == "") {
			changeStatus("lastname", "error", "");
			return false;
		}
		changeStatus("lastname", "success", "");
		return true;
	}

	function check_xxx() {
	    var mailRegExp = new RegExp("^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$");

		if ($("#xxx").val() == "") {
			changeStatus("xxx", "error", "");
			return false;
		}

		if (mailRegExp.test($("#xxx").val().toUpperCase()) === false) {
			changeStatus("xxx", "error", "");
			return false;
		}

		changeStatus("xxx", "success", "");
		return true;
	}

	function check_confirmationMail() {
		if ($("#xxx").val() != $("#confirmationMail").val()) {
			changeStatus("confirmationMail", "error", "");
			return false;
		}
		changeStatus("confirmationMail", "success", "");
		return true;
	}

	function check(form) {
		var status = true;

		form.find("input").each(function() {

			if (form.find("#" + $(this).attr("id") + "Status").length) {
				if (!eval("check_" + $(this).attr("id") + "();")) {
					status = false;
				}
			}
		});

		return status;
	}

	function progressHandlingFunction(e) {
	    if (e.lengthComputable){
//	        $('progress').attr({value:e.loaded, max:e.total});
//	        console.log(e.loaded / e.total);
	    }
	}

	function submit(form) {
		if (!check(form)) return;

		$("#volunteerVeil").show();

	    var formData = new FormData(form[0]);
	    $.ajax({
	        url: 'do_volunteer.php',  //Server script to process data
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
    			$("#volunteerVeil").hide();
        		data = JSON.parse(data);

        		if (data.ko) {

        		}
        		else {
	    			$("#main").hide();
	    			$("#response").show();
        		}
	        },
	        data: formData,
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	}

	$("#candidateButtons button").click(function() {
		if ($(this).hasClass("active")) {
			$(this).removeClass("active");
		} else {
			$(this).addClass("active");
		}

		var candidateValue = "";
		var candidateSeparator = "";

		$(".photo-element").hide();

		$("#candidateButtons button.active").each(function() {
			candidateValue += candidateSeparator;

			if ($(this).val() == "candidate") {
				$(".photo-element").show();
			}

			candidateValue += $(this).val();
			candidateSeparator = ",";
		});

		$("#candidateInput").val(candidateValue);
	});

	$("#sexButtons button").click(function() {
		if (!$(this).hasClass("active")) {
			$("#sexButtons button").removeClass("active");
			$(this).addClass("active");

			$("#sexInput").val($(this).val());
		}
	});

	$('#confirmationMail').bind('paste', function(event) {
		event.preventDefault();
	});

	$("#volunteerForm").submit(function(event) {
		event.preventDefault();
		submit($("#volunteerForm"));
	});

	$("#icandidateButton").click(function(event) {
		event.preventDefault();
		submit($("#volunteerForm"));
	});

	$(".ijoinButton").click(function(event) {
		event.preventDefault();
		window.location.replace(joinUrl);
	});

	$(".idonateButton").click(function(event) {
		event.preventDefault();
		window.location.replace(donateUrl);
	});

	$("input[type=checkbox]").click(function(event) {
		if ($(this).attr("checked")) {
			$(this).removeAttr("checked");
		}
		else {
			$(this).attr("checked", "checked");
		}
	});
});