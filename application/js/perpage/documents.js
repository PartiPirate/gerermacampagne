function openAddPartyDocumentDialog() {
	partyId = $(this).parents(".partyDocumentsDiv").attr("aria-party-id");
	openAddDocumentDialog("party", partyId);
}

function openAddCampaignDocumentDialog() {
	campaignId = $(this).parents(".campaignDocumentsDiv").attr("aria-campaign-id");
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