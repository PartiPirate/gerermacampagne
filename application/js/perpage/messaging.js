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
/* global showdown */

function progressHandlingFunction(e) {
    if (e.lengthComputable){
        $('progress').attr({value:e.loaded, max:e.total});
        console.log(e.loaded / e.total);
    }
}

function getActiveMessageBox() {
    return $(".message-box.list-group-item-success");
}

function setRead(messageId, messageCode) {
    var form = {};
    form["mes_id"] = messageId;
    form["mes_code"] = messageCode;
    
    $.post("do_setReadMessage.php", form, function(data) {
        if (data.ok) {
            $(".message-header[data-id=" + messageId + "]").removeClass("not-read");
        }
    }, "json");
}

function addSendMailHandlers() {
	$(".btn-new-mail").click(function() {
		$(".new-title").show();
		$(".answer-title").hide();

        var messageBox = getActiveMessageBox();

        if (messageBox.hasClass("inbox")) { // answer to the "from"
    		$("#sendMailDiv input#fromId").val(messageBox.data("to-id"));
    		$("#sendMailDiv input#fromType").val(messageBox.data("to-type"));
        }
        else if (messageBox.hasClass("outbox")) { // answer to the "to"
    		$("#sendMailDiv input#fromId").val(messageBox.data("from-id"));
    		$("#sendMailDiv input#fromType").val(messageBox.data("from-type"));
        }

		$("#sendMailDiv input#toId").val("");
		$("#sendMailDiv input#toType").val("");

		$("#sendMailDiv label#toLabel").html("");
		$("#sendMailDiv label#toLabel").hide();
		$("#sendMailDiv select#toIds").show();

		$("#sendMailDiv label#fromLabel").html(messageBox.text());

		$("#sendMailDiv input#subject").val("");
		$("#sendMailDiv textarea#message").val("");

		$("#sendMailDiv").modal("show");
	});
    
	$(".btn-answer-mail").click(function() {
		$(".new-title").hide();
		$(".answer-title").show();

        var messageBox = getActiveMessageBox();
        var message = $(this).parents(".message");
        
        if (messageBox.hasClass("inbox")) { // answer to the "from"
    		$("#sendMailDiv input#fromId").val(message.data("to-id"));
    		$("#sendMailDiv input#fromType").val(message.data("to-type"));
    		$("#sendMailDiv input#toId").val(message.data("from-id"));
    		$("#sendMailDiv input#toType").val(message.data("from-type"));

    		$("#sendMailDiv label#fromLabel").html(message.data("to-label"));
    		$("#sendMailDiv label#toLabel").html(message.data("from-label"));
        }
        else if (messageBox.hasClass("outbox")) { // answer to the "to"
    		$("#sendMailDiv input#fromId").val(message.data("from-id"));
    		$("#sendMailDiv input#fromType").val(message.data("from-type"));
    		$("#sendMailDiv input#toId").val(message.data("to-id"));
    		$("#sendMailDiv input#toType").val(message.data("to-type"));

    		$("#sendMailDiv label#fromLabel").html(message.data("from-label"));
    		$("#sendMailDiv label#toLabel").html(message.data("to-label"));
        }

		$("#sendMailDiv label#toLabel").show();
		$("#sendMailDiv select#toIds").hide();

		$("#sendMailDiv input#subject").val("Re: " + message.find(".subject").text());
		
		var text = message.find(".message-body").text();
		
        const regex = /^(.*)$/gm;
        const subst = '> $1';

        // The substituted value will be contained in the result variable
        text = text.replace(regex, subst);		
		
		$("#sendMailDiv textarea#message").val(text);

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

$(function() {
    $("#messages").on("click", ".message-header", function() {
    });
    
    $("#messages").on("click", ".message-header", function() {
        $(".message-header").removeClass("list-group-item-success");
        $(this).addClass("list-group-item-success");

        var messageId = $(this).data("id");
        $(".message").hide();
        $(".message[data-id=" + messageId + "]").show();
        
        // if message-header has "not-read" class, remove it, send a call to remove it into the DB, only if you're a receiver
        if ($(this).hasClass("not-read") && getActiveMessageBox().hasClass("inbox")) {
            setRead(messageId, $(this).data("code"));
            //// remove not-read class on message-header
            //$(this).removeClass("not-read");
        }
    });
    
    $("#message-boxes").on("click", ".message-box", function() {
        $(".message-box").removeClass("list-group-item-success");
        $(this).addClass("list-group-item-success");
        
        $(".message-box-label").text($(this).text());

        var fromId = $(this).data("from-id");
        var fromType = $(this).data("from-type");
        var toId = $(this).data("to-id");
        var toType = $(this).data("to-type");
        
        var numberOfMessages = 0;
        
        $(".message").hide();

        $(".message-header").hide();
        $(".message-header").each(function() {
            if ($(this).data("from-id") == fromId && $(this).data("from-type") == fromType) {
                numberOfMessages++;
                $(this).show();
                $(this).removeClass("list-group-item-success");
            }
            else if ($(this).data("to-id") == toId && $(this).data("to-type") == toType) {
                numberOfMessages++;
                $(this).show();
                $(this).removeClass("list-group-item-success");
            }
        })
        
        if ($(this).hasClass("inbox")) {
            $(".column-from").show();
            $(".column-to").hide();
        }
        else {
            $(".column-from").hide();
            $(".column-to").show();
        }
        
        if (numberOfMessages) {
            $("#no-message").hide();
        }
        else {
            $("#no-message").show();
        }
    });

    $(".message").each(function() {
        var converter = new showdown.Converter(),
            text      = $(this).find(".message-body").text(),
            html      = converter.makeHtml(text);

        $(this).find(".message-body-html").html(html);
    });

    addSendMailHandlers();

    // We click on the first box, your inbox
    $(".message-box").eq(0).click();
});