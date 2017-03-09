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
function openAddPartyDocumentDialog() {
	var partyId = $(this).parents(".partyDocumentsDiv").attr("aria-party-id");
	openAddDocumentDialog("party", partyId);
}

function openAddCampaignDocumentDialog() {
	var campaignId = $(this).parents(".campaignDocumentsDiv").attr("aria-campaign-id");
	openAddDocumentDialog("campaign", campaignId);
}

function openAddDocumentDialog(target, targetId) {
	$("#addDocumentDiv #target").val(target);
	$("#addDocumentDiv #targetId").val(targetId);

	$("#addDocumentDiv").modal('show');
}

function progressHandlingFunction(e) {
    if (e.lengthComputable){
        $('progress').attr({value:e.loaded, max:e.total});
        console.log(e.loaded / e.total);
    }
}

$(function() {
	$("*[class*='image_']").colorbox({rel: 'images',
										maxWidth:"75%",
										maxHeight:"75%",
										photo: true,
										current: current,
										previous: previous,
										next: next,
										close: close,
										xhrError: xhrError,
										imgError: imgError});
	$("*[class*='text_']").colorbox({rel: 'images',
										maxWidth:"75%",
										maxHeight:"75%",
										photo: false,
										current: current,
										previous: previous,
										next: next,
										close: close,
										xhrError: xhrError,
										imgError: imgError});

	$("#addDocumentDiv #closeButton").click(function() {
		$("#addDocumentDiv").modal('hide');
	});

	$("#addDocumentDiv #addDocumentButton").click(function() {
    	$("#addDocumentDiv #addDocumentButton").attr("disabled", "disabled");

	    var formData = new FormData($('#addDocumentForm')[0]);
	    $.ajax({
	        url: 'do_addDocument.php',  //Server script to process data
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
        		$("#addDocumentDiv").modal('hide');
            	$("#addDocumentDiv #addDocumentButton").removeAttr("disabled");

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

	$(".addPartyDocumentButton").click(openAddPartyDocumentDialog);
	$("#addCampaignDocumentButton").click(openAddCampaignDocumentDialog);
});