/* global $ */
/* global tasks */
/* global $lang */

function saveResponseHandler(data) {
	if (data.ok) {
		window.location.reload(true);
	}
}

function lang(key) {
	if ($lang[key]) {
		return $lang[key];
	}

	return "$lang[\"" + key + "\"] = \"\";";
}

function progressHandlingFunction(e) {
    if (e.lengthComputable){
        $('progress').attr({value:e.loaded, max:e.total});
//        console.log(e.loaded / e.total);
    }
}

function addHandlers() {

	$("#saveCoordinatesButton").click(function(event) {
		event.preventDefault();
		event.stopImmediatePropagation();

    	$("#saveCoordinatesButton").attr("disabled", "disabled");

    	$.post("do_updateActor.php", $("#updateActorForm").serialize(), function(data) {

			if (data.ok) {
				$("#ok_operation_successAlert").show().delay(2000).fadeOut(1000);
			}
			else {
				$("#error_cant_change_informationAlert").show().delay(2000).fadeOut(1000);
			}

	    	$("#saveCoordinatesButton").removeAttr("disabled");
    	}, "json");
	});

	$("#functionButtons button").click(function(e) {
		$("#functionButtons button").removeClass("active");
		$(this).addClass("active");

		var functionInput = $(this).val();

		$("#functionInput").val(functionInput);

		if (functionInput == "charteredAccountant") {
			$(".pseudo-group").hide();
			$(".company-name-group").show();
		}
		else {
			$(".pseudo-group").show();
			$(".company-name-group").hide();
		}
	});
	
}

$(function() {

	addHandlers();

	$(".alert-success").hide();

});