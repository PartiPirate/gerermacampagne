/* global $ */

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

function saveButtonHandler(event) {
	event.preventDefault();
	var myform = {
					name: $("#nameInput").val(),
					electoralDistrict: $("#electoralDistrictInput").val(),
					right: $("#rightInput").val(),
					startDate: $("#startDateInput").val(),
					finishDate: $("#finishDateInput").val()
				};
	$.post("do_saveCampaign.php", myform, saveResponseHandler, "json");
}

function getTask(taskId) {

	for(var index = 0; index < tasks.length; ++index) {
		var task = tasks[index];

		if (task.tas_id == taskId) {
			return task;
		}
	}

	return null;
}

function progressHandlingFunction(e) {
    if (e.lengthComputable){
        $('progress').attr({value:e.loaded, max:e.total});
//        console.log(e.loaded / e.total);
    }
}

function openTaskConfirmDialog(task) {
	if (task.tas_form == "doAddCharteredAccountant") {
		$("#addActorDiv").modal("show");
		$("#functionButtons button[value=charteredAccountant]").click();

		return;
	}

	var html = "";
	html += "	<div id=\"doTask-"+task.tas_id+"\" class=\"modal fade\">\n";
	html += "	  <div class=\"modal-dialog\">\n";
	html += "	    <div class=\"modal-content\">\n";
	html += "	      <div class=\"modal-header\">\n";
	html += "	        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
	html += "	        <h4 class=\"modal-title\">"+lang(task.tas_label)+"</h4>\n";
	html += "	      </div>\n";
	html += "		<form id=\"formPanel\" method=\"post\" class=\"form-horizontal\">\n";

	html += "			<input type=\"hidden\" name=\"taskId\" value=\""+task.tas_id+"\" />";
	html += "			<input type=\"hidden\" name=\"campaignId\" value=\""+task.tas_campaign_id+"\" />";

	html += "	      <div class=\"modal-body\">\n";
	html += "	        <p>Vous avez effectuez cette tâche avec succès ?</p>\n";
	html += "	      </div>\n";

	if (task.tas_documents.length) {

		html += "			<fieldset>\n";
		html += "				<legend>Renseignez les documents en relation avec cette tâche</legend>\n";

		for(var index = 0; index < task.tas_documents.length; ++index) {

			html += "			<div class=\"form-group has-feedback\">\n";
			html += "				<label class=\"col-md-4 control-label\" for=\"documentInput-"+index+"\">"+lang(task.tas_documents[index].label)+"</label>\n";
			html += "				<div class=\"col-md-6\">\n";
			html += "					<input id=\"documentInput-"+index+"\" name=\"documentInput-"+index+"\" value=\"\" data-show-upload=\"false\" type=\"file\"\n";
			html += "						placeholder=\"\" class=\"form-control file input-md\" data-show-preview=\"false\">\n";
			html += "					<span id=\"documentStatus-"+index+"\" class=\"glyphicon glyphicon-ok form-control-feedback otbHidden\" aria-hidden=\"true\"></span>\n";
			html += "				</div>\n";
			html += "			</div>\n";

		}

		html += "			</fieldset>\n";
	}

	html += "		</form>\n";

	html += "	      <div class=\"modal-footer\">\n";
	html += "	        <button id=\"closeButton\" type=\"button\" class=\"btn btn-default\">Close</button>\n";
	html += "	        <button id=\"doButton\" type=\"button\" class=\"btn btn-primary\">Do</button>\n";
	html += "	      </div>\n";
	html += "	    </div>\n";
	html += "	  </div>\n";
	html += "	</div>\n";

	var dialog = $(html);

	dialog.find("#closeButton").click(function(event) {
		$(dialog).modal('hide');
	});

	var phpForm = "do_" + task.tas_form + ".php";

	dialog.find("#doButton").click(function(event) {
    	dialog.find("#doButton").attr("disabled", "disabled");

	    var formData = new FormData($('#formPanel')[0]);
	    $.ajax({
	        url: phpForm,  //Server script to process data
	        type: 'POST',
	        xhr: function() {  // Custom XMLHttpRequest
	            var myXhr = $.ajaxSettings.xhr();
	            if(myXhr.upload){ // Check if upload property exists
	                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
	            }
	            return myXhr;
	        },
	        //Ajax events
//	        beforeSend: beforeSendHandler,
	        success: function(data) {
        		data = JSON.parse(data);
	        	$(dialog).modal('hide');
	        	if (data.ok) {
	        		window.location.reload(true);
	        	}
	        },
//	        error: errorHandler,
	        // Form data
	        data: formData,
	        //Options to tell jQuery not to process data or worry about content-type.
	        cache: false,
	        contentType: false,
	        processData: false
	    });

//		$(dialog).modal('hide');
	});

	$(dialog).modal({
		keyboard : false
	});

	$(dialog).on('hidden.bs.modal', function (e) {
		dialog.remove();
	});

	$(dialog).modal('show');
}

function doneTaskClickHandler(event) {
	event.preventDefault();

	var taskId = $(this).attr("id").replace("doneTask-", "");
	var task = getTask(taskId);

	if (task == null) return;

	openTaskConfirmDialog(task);
//	alert(task.tas_label);
}

function askAffiliation(campaignId, ppId) {
	var myform = {"campaignId": campaignId, "politicalPartyId": ppId};
//	console.log(myform);

	$.post("do_askAffiliation.php", myform, function(data) {

	}, "json");
}

$(function() {

	/* BEGIN Remove actor */

	var removeActorHandler = function(event) {
		event.preventDefault();

		var myform = {};
		myform["campaignId"] = $("#campaignId").val();
		myform["rightId"] = $(this).attr("id").replace("removeActor-", "");

    	$.post("do_removeActor.php", myform, function(data) {
    		$.get("index.php", null, function(data) {
    			var prev = $("#actorListContainer").prev();
    			$("#actorListContainer").remove();
    			prev.after($(data).find("#actorListContainer"));
    			$(".removeActorButton").click(removeActorHandler);
    		}, "html");
    	}, "json");
	};

	$(".removeActorButton").click(removeActorHandler);

	/* END Remove actor */

	/* BEGIN Add actor */

	$("#closeAddActorButton").click(function() {
		$("#addActorDiv").modal("hide");
	});

	$("#addActorButton").click(function() {
    	$("#addActorDiv #addActorButton").attr("disabled", "disabled");

    	$.post("do_addActor.php", $("#addActorForm").serialize(), function(data) {
    		$.get("index.php", null, function(data) {
    			var prev = $("#actorListContainer").prev();
    			$("#actorListContainer").remove();
    			prev.after($(data).find("#actorListContainer"));
    			$(".removeActorButton").click(removeActorHandler);
        		$("#addActorDiv").modal("hide");
            	$("#addActorDiv #addActorButton").removeAttr("disabled");
    		}, "html");
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

	/* END Add actor */

	/* BEGIN Add task */

	$("#closeAddTaskButton").click(function() {
		$("#addTaskDiv").modal("hide");
	});

	$("#addTaskButton").click(function() {
    	$("#addTaskDiv #addTaskButton").attr("disabled", "disabled");

    	$.post("do_addTask.php", $("#addTaskForm").serialize(), function(data) {
    		window.location.reload(true);

//    		$.get("index.php", null, function(data) {
//    			var prev = $("#actorListContainer").prev();
//    			$("#actorListContainer").remove();
//    			prev.after($(data).find("#actorListContainer"));
//    			$(".removeTaskButton").click(removeTaskHandler);
//        		$("#addTaskDiv").modal("hide");
//            	$("#addTaskDiv #addTaskButton").removeAttr("disabled");
//    		}, "html");

    	}, "json");
	});

	var setValidators = function() {
		var validators = "[";
		var separator = "";

		$("#validatorButtons button.active").each(function() {
			validators += separator;
			validators += "\"";
			validators += $(this).val();
			validators += "\"";

			separator = ",";
		});

		validators += "]";

		$("#validatorInput").val(validators);
	};

	$("#validatorButtons button").click(function(e) {
		$(this).toggleClass("active");

//		$("#functionButtons button").removeClass("active");
//		$("#functionInput").val($(this).val());

		setValidators();
	});
	setValidators();

	$("#afterTaskId").change(function() {
		$("#order-after").click();
	});

	/* END Add task */

	$('input[data-date-format]').parent("div").datetimepicker({
    	language: userLanguage
	});

	$("#rightButtons button").click(function(e) {
		$("#rightButtons button").removeClass("active");
		$(this).addClass("active");
		$("#rightInput").val($(this).val());
	});

	$(".alert-success").hide();

	$("#saveButton").click(saveButtonHandler);

	$("a[id*='doneTask-']").click(doneTaskClickHandler);

	$("#politicalChoice").change(function() {
		var ppId = $("#politicalChoice option:selected").val();
		var campaignId = $("#campaignId").val();

//		console.log("ppId: " + ppId);
		askAffiliation(campaignId, ppId);
	});

	if (window.location.hash) {
		$(window.location.hash).animate({backgroundColor: "#a0ffa0"}, 500).animate({backgroundColor: ""}, 500);
	}

	$("a[href*='#task']").click(function() {
		$($(this).attr("href")).animate({backgroundColor: "#a0ffa0"}, 500).animate({backgroundColor: ""}, 500);
	});

	$("#filterButtons button").click(function(e) {
		$("#filterButtons button").removeClass("active");
		$(this).addClass("active");
		var filterBy = $(this).val();

		if (filterBy == "no") {
			$(".task").show();
		}
		else if (filterBy == "done") {
			$(".task").each(function() {
				if ($(this).find(".status.glyphicon-ok").length != 0) {
					$(this).show();
				}
				else {
					$(this).hide();
				}
			});
		}
		else if (filterBy == "emergency") {
			$(".task").hide();
			$(".task.alert-danger").show();
		}
		else if (filterBy == "representative") {
			$(".task").each(function() {
				if ($(this).data("righters").indexOf("representative") != -1) {
					$(this).show();
				}
				else {
					$(this).hide();
				}
			});
		}
		else if (filterBy == "candidate") {
			$(".task").each(function() {
				if ($(this).data("righters").indexOf("listHead") != -1) {
					$(this).show();
				}
				else if ($(this).data("righters").indexOf("candidate") != -1) {
					$(this).show();
				}
				else {
					$(this).hide();
				}
			});
		}
	});

});