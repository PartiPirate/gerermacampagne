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
/* global showPage */

function progressHandlingFunction(e) {
    if (e.lengthComputable){
        $('progress').attr({value:e.loaded, max:e.total});
        console.log(e.loaded / e.total);
    }
}

function removeAffiliation(affiliationId) {
	var table = $("#waitingAffiliationsTable");
	table.find("tr[aria-id="+affiliationId+"]").remove();

	var nav = table.siblings("nav");

	if (nav.length != 0) {
		var currentPage = nav.find("li.active").text();

		showPage(table, currentPage);
	}

	var badge = $("#affiliationsMenuItem .badge");
	var value = badge.text() - 1;
	badge.text(value);
	if (value == 0) {
		badge.hide();
	}

}

function removeCampaign(campaignId) {
	$(".campaign[data-campaign-id="+campaignId+"]").remove();
}

function addSendMailHandlers() {
	$(".btn-send-mail").click(function() {
		
		$("#sendMailDiv input#fromId").val($(this).data("from-id"));
		$("#sendMailDiv input#fromType").val($(this).data("from-type"));
		$("#sendMailDiv input#toId").val("");
//		$("#sendMailDiv input#toType").val("candidate");

		$("#sendMailDiv input[name='toTasks[]']").removeAttr("checked");

		$("#sendMailDiv button[value='candidate']").click();
		$("#sendMailDiv button[value='status-all']").click();

		$("#sendMailDiv input#subject").val("");
		$("#sendMailDiv textarea#message").val("");

		$("#sendMailDiv").modal("show");
	});

	$("#sendMailDiv #sendMailButton").click(function(event) {
		
	    var formData = new FormData($('#sendMailDiv form')[0]);
	    $.ajax({
	        url: 'do_sendMessage.php',  //Server script to process data
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
		    	$("#sendMailDiv").modal('hide');
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

	$("#sendMailDiv #closeButton").click(function(event) {
    	$("#sendMailDiv").modal('hide');
	});
	
	$("#toTypeButtons button").click(function(e) {
		$("#toTypeButtons button").removeClass("active");
		$(this).addClass("active");

		$("#sendMailDiv input#toType").val($(this).val());
	});	
	
	$("#toTaskStatusButtons button").click(function(e) {
		$("#toTaskStatusButtons button").removeClass("active");
		$(this).addClass("active");

		var value = $(this).val();

		if (value == "status-all") {
			$("#task-list").hide();
		}
		else  {
			$("#task-list").show();
		}

		$("#sendMailDiv input#toTaskStatus").val($(this).val());
	});	
}

function addCampaignHandlers() {
	$(".campaign").on("click", "p.editable, h3.editable", function(event) {
		var campaignId = $(this).parents(".campaign").data("campaign-id");
		var property = $(this).data("property");

		var span = $(this);
		span.data("label", span.html());

		var input = $("<input value='' class='text-left' style='width: calc(100% - 24px); height: 22px; margin-left: -2px; margin-top: -2px; margin-bottom: -4px;'>");
		input.val(span.data("label"));
		
		var closerButton = $("<button class='btn btn-danger btn-xs btn-left-straight' style='margin-top: -4px; margin-bottom: -3px;'><span class='glyphicon glyphicon-remove'></span></button>");

		span.html("");
		span.append(input);
		span.append(closerButton);
		
		var updater = function(event) {
			if (event.type != "keydown" && (event.target == input.get(0) || event.target == span.get(0))) {
				event.stopPropagation();
				return;
			}

			$("*").off("click", updater);

			if (event.target == closerButton.get(0) || event.target == closerButton.find("span").get(0)) {
				closer();
				
				event.stopPropagation();
				return;
			}

			var value = input.val();

//			console.log("update ?");

			if (value != span.data("label")) {
				var form = {property: property, value: value, campaignId: campaignId};

//				console.log(form);

				$.post("do_updateCampaign.php", form, function(data) {
				
					input.remove();
				
					span.html(value);
					span.data("label", value);
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
	$(".acceptButton").click(function() {
		var affiliationId = $(this).parents("tr").attr("aria-id");

		$.post("do_acceptAffiliation.php", {affiliationId : affiliationId}, function() {
			removeAffiliation(affiliationId);
		}, "json");
	});
	$(".refuseButton").click(function() {
		var affiliationId = $(this).parents("tr").attr("aria-id");

		$.post("do_refuseAffiliation.php", {affiliationId : affiliationId}, function() {
			removeAffiliation(affiliationId);
		}, "json");
	});
	$(".btn-reject").click(function() {
		var campaign = $(this).parents(".campaign");
		var campaignId = campaign.data("campaign-id");
		var rejectCode = campaign.data("reject-code");
	
		$.post("do_rejectAffiliation.php", {campaignId : campaignId, rejectCode: rejectCode}, function() {
			removeCampaign(campaignId);
		}, "json");
	});
	
	$("#filterButtons button").click(function(e) {
		$("#filterButtons button").removeClass("active");
		$(this).addClass("active");

		var filterBy = $(this).val();
		
		if (filterBy == "no") {
		    $("*[data-template-id]").show();
		}
		else {
		    $("*[data-template-id]").hide();
		    $("*[data-template-id="+filterBy+"]").show();
		}
	});  	
	
	$("#modeButtons button").click(function(e) {
		$("#modeButtons button").removeClass("active");
		$(this).addClass("active");

		var modeBy = $(this).val();

		$(".campaign").removeClass("mode-text").removeClass("mode-graphic").addClass(modeBy);
	});
	
	addSendMailHandlers();
	addCampaignHandlers();
});
