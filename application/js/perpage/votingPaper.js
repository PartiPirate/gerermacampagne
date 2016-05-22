function changePaperFormat() {
	var votingPaper = $("#votingPaper");
	var paperFormat = $("#paperFormat option:selected").val();

	switch(paperFormat) {
		case "105x148" :
			votingPaper.css({width: "148mm", height: "105mm"});
			break;
		case "210x148" :
			votingPaper.css({width: "148mm", height: "210mm"});
			break;
		case "210x297" :
			votingPaper.css({width: "210mm", height: "297mm"});
			break;
	}
}

function getVotingPaperCode() {
	var clone = $("#votingPaper").clone();
	clone.find("div").attr("class", "");
	clone.find("div").css("border", "");

	return clone.html();
}

function progressHandlingFunction(e) {
    if (e.lengthComputable){
        $('progress').attr({value:e.loaded, max:e.total});
//        console.log(e.loaded / e.total);
    }
}


function createVotingPaperPdf() {
	$("#votingPaperCode").val(getVotingPaperCode());

	$('#votingForm').submit();

	return;

    var formData = new FormData($('#votingForm')[0]);
    $.ajax({
        url: "do_createVotingPaperPdf.php",  //Server script to process data
        type: 'POST',
        xhr: function() {  // Custom XMLHttpRequest
            var myXhr = $.ajaxSettings.xhr();
            if(myXhr.upload){ // Check if upload property exists
                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
            }
            return myXhr;
        },
        //Ajax events
//        beforeSend: beforeSendHandler,
        success: function(data) {
//    		data = JSON.parse(data);
//
//    		if (data.ok) {
//    		}
        },
//        error: errorHandler,
        // Form data
        data: formData,
        //Options to tell jQuery not to process data or worry about content-type.
        cache: false,
        contentType: false,
        processData: false
    });
}

function sendVotingPaperForm() {
	$("#votingPaperCode").val(getVotingPaperCode());

    var formData = new FormData($('#votingForm')[0]);
    $.ajax({
        url: "do_saveVotingPaper.php",  //Server script to process data
        type: 'POST',
        xhr: function() {  // Custom XMLHttpRequest
            var myXhr = $.ajaxSettings.xhr();
            if(myXhr.upload){ // Check if upload property exists
                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
            }
            return myXhr;
        },
        //Ajax events
//        beforeSend: beforeSendHandler,
        success: function(data) {
    		data = JSON.parse(data);

    		if (data.ok) {
    			$("#votingPaperId").val(data.votingPaperId);
    		}
        },
//        error: errorHandler,
        // Form data
        data: formData,
        //Options to tell jQuery not to process data or worry about content-type.
        cache: false,
        contentType: false,
        processData: false
    });
}

var selectedId = null;
var selectedIds = [];
var containerIds = 0;

function normalizeSelection(selection) {
	if (selection.height < 0) {
		selection.top = selection.top + selection.height;
		selection.height = -selection.height;
	}

	if (selection.width < 0) {
		selection.left = selection.left + selection.width;
		selection.width = -selection.width;
	}

	return selection;
}

function getContainer() {

	while($("#container_" + containerIds).length) {
		containerIds++;
	}

	var container = $("<div id='container_"+containerIds+"' class='undrop' style='border: 1px black dotted; '></div>");

	return container;
}

function drawRectangle(selection) {
	if (!isInRectangleMode()) return;

	selection = normalizeSelection(selection);

	var container = getContainer();
	var children = $("<rectangle class='votingPaper-rectangle'></rectangle>");
	children.css({
					width : selection.width + "px",
					height : selection.height + "px",
					backgroundColor: $("#backgroundColor span.color").css("background-color")
				});
	container.css({
		width : (2 + selection.width) + "px",
		height : (2 + selection.height) + "px"
	});

	container.append(children);

	$("#votingPaper").append(container);

	var dPos = $("#votingPaper").offset();
	var pos = selection;

	container.css({
		position: "absolute",
		top: (pos.top - dPos.top - 2) + "px",
		left: (pos.left - dPos.left - 2) + "px",
	});

	container.find("rectangle").click(selectRectangle);

	addContainerHoverHandler(container);
	addContainerDraggable(container);
}

function addContainerDraggable(container) {
	container.draggable({
		containment: "parent",
		distance: 10
	});
}

function addContainerResizable(container) {
//	container.find("img, rectangle").resizable();
}

function addContainerHoverHandler(container) {
	container.hover(function() {
		if (!isInSelectionMode()) return;

		$(this).prepend($("<span class='text-center' id='removeButton'>&times;</span>"));
		$(this).find("#removeButton").css({	position: "absolute",
											display: "inline-block",
											background: "white",
											color: "black",
											border: "1px dotted black",
											width: "20px", height: "20px",
											cursor: "pointer",
											left: ($(this).width() - 28) + "px", top: "-1px"});
		$(this).find("#removeButton").click(function() {
			$(this).parent().remove();
		});
	}, function() {
		$(this).find("#removeButton").remove();
	});
}

function selectRange(selection) {
	$("#votingPaper div").removeClass("selected").css("borderStyle", "dotted");
	selectedIds = [];

	selection = normalizeSelection(selection);

	$("#votingPaper div").each(function() {
		var offset = $(this).offset();
		var width = $(this).width();
		var height = $(this).height();

		if (
				offset.top >= selection.top && offset.top <= selection.top + selection.height
			&&	offset.left >= selection.left && offset.left <= selection.left + selection.width
			) {
			selectedIds[selectedIds.length] = $(this).attr("id");
			$(this).addClass("selected").css("borderStyle", "solid");
			return;
		}

		if (
				offset.top + height >= selection.top && offset.top + height <= selection.top + selection.height
			&&	offset.left >= selection.left && offset.left <= selection.left + selection.width
			) {
			selectedIds[selectedIds.length] = $(this).attr("id");
			$(this).addClass("selected").css("borderStyle", "solid");
			return;
		}

		if (
				offset.top >= selection.top && offset.top<= selection.top + selection.height
			&&	offset.left + width >= selection.left && offset.left + width <= selection.left + selection.width
			) {
			selectedIds[selectedIds.length] = $(this).attr("id");
			$(this).addClass("selected").css("borderStyle", "solid");
			return;
		}

		if (
				offset.top + height >= selection.top && offset.top + height <= selection.top + selection.height
			&&	offset.left + width >= selection.left && offset.left + width <= selection.left + selection.width
			) {
			selectedIds[selectedIds.length] = $(this).attr("id");
			$(this).addClass("selected").css("borderStyle", "solid");
			return;
		}

	});

	// On peut faire du spécifique pour les images et les textes
	if (selectedIds.length == 1) {
		$("#votingPaper #" + selectedIds[0] + " img").each(selectImage);
		$("#votingPaper #" + selectedIds[0] + " span").each(selectText);
	}
	else if (selectedIds.length > 1) {
		$("#layerTools button").attr("disabled", "disabled");
		$("#textTools").hide();
		$("#multipleTools button").removeAttr("disabled");
		$("#imageTools").hide();
	}
	else {
		$("#layerTools button").attr("disabled", "disabled");
		$("#textTools").hide();
		$("#multipleTools button").attr("disabled", "disabled");
		$("#imageTools").hide();
	}
}

function selectRectangle() {
	if (!isInSelectionMode()) return;

	var rectangle = $(this);
	selectedId = $(this).parent().attr("id");

	$("#votingPaper div").removeClass("selected").css("borderStyle", "dotted");
	$(this).parent().addClass("selected").css("borderStyle", "solid");

	$("#widthInput").val(rectangle.css("width"));
	$("#heightInput").val(rectangle.css("height"));

	$("#layerTools button").removeAttr("disabled");
	$("#textTools").hide();
	$("#multipleTools button").attr("disabled", "disabled");
	$("#imageTools").hide();
	$("#colorTools #foregroundColor").attr("disabled", "disabled");
	$("#colorTools #backgroundColor").removeAttr("disabled");
	$("#colorTools #backgroundColor span.color").css({backgroundColor: rectangle.css("background-color")});
}

function selectImage() {
	if (!isInSelectionMode()) return;

	var image= $(this);
	selectedId = $(this).parent().attr("id");

	$("#votingPaper div").removeClass("selected").css("borderStyle", "dotted");
	$(this).parent().addClass("selected").css("borderStyle", "solid");

	$("#widthInput").val(image.css("width"));
	$("#heightInput").val(image.css("height"));

	$("#layerTools button").removeAttr("disabled");
	$("#textTools").hide();
	$("#multipleTools button").attr("disabled", "disabled");
	$("#imageTools").show();

	$("#colorTools #foregroundColor").attr("disabled", "disabled");
	$("#colorTools #backgroundColor").removeAttr("disabled");
	$("#colorTools #backgroundColor span.color").css({backgroundColor: image.css("background-color")});
}

function selectText() {
	if (!isInSelectionMode()) return;

	var text = $(this);
	selectedId = $(this).parent().attr("id");

	$("#votingPaper div").removeClass("selected").css("borderStyle", "dotted");
	$(this).parent().addClass("selected").css("borderStyle", "solid");

	$("#textInput").val(text.text());
	$("#fontFamilyInput").val(text.css("fontFamily"));
	$("#fontSizeInput").val(text.css("fontSize"));

	$("#layerTools button").removeAttr("disabled");
	$("#textTools").show();
	$("#multipleTools button").attr("disabled", "disabled");
	$("#imageTools").hide();

	$("#colorTools #backgroundColor").removeAttr("disabled");
	$("#colorTools #backgroundColor span.color").css({backgroundColor: text.css("background-color")});

	$("#colorTools #foregroundColor").removeAttr("disabled");
	$("#colorTools #foregroundColor span.color").css({backgroundColor: text.css("color")});

	$("#textInput").focus();
}

function inPaperDrop(event, ui) {
	if (ui.draggable.hasClass("undrop")) {
		return;
	}

	var children = ui.helper.children();
	var container = getContainer();
	container.append(children);

	$(this).append(container);

	var pos = ui.helper.offset(), dPos = $(this).offset();

	container.css({
		position: "absolute",
		top: (pos.top - dPos.top) + "px",
		left: (pos.left - dPos.left) + "px",
	});

	container.find("span").css({
		fontSize: "12px",
		fontFamily: "ubuntu"
	}).click(selectText).each(selectText);

	container.find("img").css({
		maxWidth: "",
		maxHeight: ""
	});
	container.find("img").each(function() {
		$(this).attr("aria-width", $(this).width());
		$(this).attr("aria-height", $(this).height());

		$(this).css({
			maxWidth: $("#votingPaper").css("width"),
			maxHeight: $("#votingPaper").css("height")
		});
	});
	container.find("img").click(selectImage).each(selectImage);

	addContainerHoverHandler(container);
	addContainerDraggable(container);
}

function initHandlers() {
	var container = $("#votingPaper div");

	container.addClass("undrop");
	container.css({
		border: "1px black dotted"
	});

	container.find("span").click(selectText);
	container.find("img").click(selectImage);
	container.find("rectangle").click(selectRectangle);
	addContainerDraggable(container);
	addContainerHoverHandler(container);
	addContainerResizable(container);

	$("#alignLeftButton").click(function() {
		var minLeft = 9999;
		var selecteds = $("#votingPaper .selected");
		selecteds.each(function() {
			var left = $(this).position().left;
			if (left < minLeft) minLeft = left;
		});
		selecteds.each(function() {
			$(this).css({left: minLeft});
		});
	});
	$("#alignCenterButton").click(function() {
		var maxRight = 0;
		var selecteds = $("#votingPaper .selected");
		selecteds.each(function() {
			var right = $(this).position().left + $(this).width() / 2;
			maxRight += right;
		});
		maxRight /= selecteds.length;
		selecteds.each(function() {
			$(this).css({left: maxRight - $(this).width() / 2});
		});
	});
	$("#alignRightButton").click(function() {
		var maxRight = 0;
		var selecteds = $("#votingPaper .selected");
		selecteds.each(function() {
			var right = $(this).position().left + $(this).width();
			if (right > maxRight) maxRight = right;
		});
		selecteds.each(function() {
			$(this).css({left: maxRight - $(this).width()});
		});
	});
	$("#alignTopButton").click(function() {
		var minTop = 9999;
		var selecteds = $("#votingPaper .selected");
		selecteds.each(function() {
			var top = $(this).position().top;
			if (top < minTop) minTop = top;
		});
		selecteds.each(function() {
			$(this).css({top: minTop});
		});
	});
	$("#alignMiddleButton").click(function() {
		var maxBottom = 0;
		var selecteds = $("#votingPaper .selected");
		selecteds.each(function() {
			var bottom = $(this).position().top + $(this).height() / 2;
			maxBottom += bottom;
		});
		maxBottom /= selecteds.length;
		selecteds.each(function() {
			$(this).css({top: maxBottom - $(this).height() / 2});
		});
	});
	$("#alignBottomButton").click(function() {
		var maxBottom = 0;
		var selecteds = $("#votingPaper .selected");
		selecteds.each(function() {
			var bottom = $(this).position().top + $(this).height();
			if (bottom > maxBottom) maxBottom = bottom;
		});
		selecteds.each(function() {
			$(this).css({top: maxBottom - $(this).height()});
		});
	});
	$("#alignBetweenButton").click(function() {});


	$("#bottomButton").click(function() {
		var element = $("#" + selectedId);
		element.detach();
		$("#votingPaper").prepend(element);
	});

	$("#topButton").click(function() {
		var element = $("#" + selectedId);
		element.detach();
		$("#votingPaper").append(element);
	});


	$("#bottomerButton").click(function() {
		var element = $("#" + selectedId);
		var previous = element.prev();

		if (previous.length) {
			element.detach();
			previous.before(element);
		}
	});

	$("#toperButton").click(function() {
		var element = $("#" + selectedId);
		var next = element.next();

		if (next.length) {
			element.detach();
			next.after(element);
		}
	});

	$("#fontSizeInput, #fontFamilyInput, #textInput").keyup(function() {
		if (!selectedId) return;

		var textSpan = $("#" + selectedId + " span");

		textSpan.text($("#textInput").val());
		textSpan.css("fontFamily", $("#fontFamilyInput").val());
		textSpan.css("fontSize", $("#fontSizeInput").val());

		if (textSpan.hasClass("votingPaper-mutable")) {
			textSpan.attr("aria-text", textSpan.text());
		}
	});

	$("#fontSizeInput, #fontFamilyInput, #textInput").change(function() {
		if (!selectedId) return;

		$("#" + selectedId + " span").text($("#textInput").val());
		$("#" + selectedId + " span").css("fontFamily", $("#fontFamilyInput").val());
		$("#" + selectedId + " span").css("fontSize", $("#fontSizeInput").val());
	});

	$("#widthInput, #heightInput").keyup(function() {
		if (!selectedId) return;

		$("#" + selectedId + " img").css("width", $("#widthInput").val());
		$("#" + selectedId + " img").css("height", $("#heightInput").val());
	});

	$("#initialSizeButton").click(function() {
		if (!selectedId) return;

		var image = $("#" + selectedId + " img");
		var width = image.attr("aria-width");
//		var height = image.attr("aria-height");

		image.css({
					width: width + "px",
					height: ""
				});

		$("#widthInput").val(image.css("width"));
		$("#heightInput").val(image.css("height"));
	});

	$("#basicTools button").click(function(e) {
		$("#basicTools button").removeClass("active");
		$(this).addClass("active");
	});

	$("#basicTools button").get(0).click();
}

function isInRectangleMode() {
	return $("#rectangleButton").hasClass("active");
}

function isInSelectionMode() {
	return $("#selectionButton").hasClass("active");
}

function isInTextMode() {
	return $("#textButton").hasClass("active");
}

function initTextHandlers() {
	var votingPaperDiv = $("#votingPaper");
	votingPaperDiv.click(function(event) {
		if(!isInTextMode()) return;

		$("#selectionButton").click();

		var children = $("<span class=\"votingPaper-mutable\">Nouveau texte</span>");
		children.attr("aria-text", children.text());

		var container = getContainer();
		container.append(children);

		$(this).append(container);

		var dPos = $(this).offset();
		var pos = {top: event.pageY, left: event.pageX};

		container.css({
			position: "absolute",
			top: (pos.top - dPos.top) + "px",
			left: (pos.left - dPos.left) + "px",
		});

		container.find("span").css({
			fontSize: "12px",
			fontFamily: "ubuntu"
		}).click(selectText).each(selectText);

		container.find("img").css({
			maxWidth: "",
			maxHeight: ""
		});
		container.find("img").each(function() {
			$(this).attr("aria-width", $(this).width());
			$(this).attr("aria-height", $(this).height());

			$(this).css({
				maxWidth: $("#votingPaper").css("width"),
				maxHeight: $("#votingPaper").css("height")
			});
		});
		container.find("img").click(selectImage).each(selectImage);

		addContainerHoverHandler(container);
		addContainerDraggable(container);
	});
}

function initSelectorHandlers() {

	var votingPaperDiv = $("#votingPaper");
	var selection = {};

	votingPaperDiv.mousedown(function(event) {
		if (!isInSelectionMode() && !isInRectangleMode()) return;
		if($(event.target).attr("id") != "votingPaper") return;

		selection.top = event.pageY;
		selection.left = event.pageX;

		$("body").css({	"-webkit-touch-callout": "none",
					    "-webkit-user-select": "none",
					    "-khtml-user-select": "none",
					    "-moz-user-select": "none",
					    "-ms-user-select": "none",
					    "user-select": "none"});

		$("body").append("<div id=\"selector\" style=\"cursor: none; position: fixed; width: 100%; height: 100%; top: 0; left: 0; background: rgba(0, 0, 0, 0);\"></div>");
		$("body").append("<div id=\"selected\" style=\"position: fixed; top: 0; left: 0; border: 1px dotted black;\"></div>");

		$("body #selector, body #selected").mouseup(function(event) {
			$("body #selector").remove();
			$("body #selected").remove();

			$("body").css({	"-webkit-touch-callout": "",
						    "-webkit-user-select": "",
						    "-khtml-user-select": "",
						    "-moz-user-select": "",
						    "-ms-user-select": "",
						    "user-select": ""});

			if (isInSelectionMode()) {
				selectRange(selection);
			}
			else {
				drawRectangle(selection);
			}
		});
		$("body #selector, body #selected").mousemove(function(event) {
			selection.width = event.pageX - selection.left;
			selection.height = event.pageY - selection.top;

			var x = selection.width > 0 ? selection.left : selection.left + selection.width;
			var y = selection.height > 0 ? selection.top : selection.top + selection.height;

			$("body #selected").offset({left: x, top: y});
			$("body #selected").width(Math.abs(selection.width));
			$("body #selected").height(Math.abs(selection.height));
		});
	});
}

function initGrayPicker() {
	var grayPickerContainer = $("#grayPickerContainer");
	grayPickerContainer.css({position: "absolute"});

	var grayPicker = $("#grayPicker");

	var zoom = 6;

	grayPicker.css({width: (16 * zoom) + "px", height: (16 * zoom) + "px", position: "relative"});

	for(var y = 0; y < 16; y++) {
		for(var x = 0; x < 16; x++) {
			var color = $("<div style=\"display: inline-block\"></div>");
			color.css({width: zoom + "px", height: zoom + "px", position: "absolute",
						border: "solid 1px #000000",
						top: (y*zoom)+"px",
						left: (x*zoom)+"px"});

			var grayValue = 255 - (x + y * 16);
			if (grayValue < 16) {
				grayValue = "0" + grayValue.toString(16);
			}
			else {
				grayValue = grayValue.toString(16);
			}

			color.css({background: "#" + grayValue + grayValue + grayValue, borderColor: "#" + grayValue + grayValue + grayValue});

			grayPicker.append(color);
		}
	}

	grayPicker.find("div").hover(function() {
		$(this).css({borderColor: "#000000"});
	}, function() {
		var targetId = $("#colorTarget").val();
		var selectedColor = $("#" + targetId + " span.color").css("background-color");

		if (selectedColor == $(this).css("background-color")) {
			$(this).css({borderColor: "#ffffff"});
		}
		else {
			$(this).css({borderColor: $(this).css("background-color")});
		}
	});

	grayPicker.find("div").click(function() {
		var targetId = $("#colorTarget").val();
		grayPickerContainer.hide();
		grayPickerContainer.offset({left: 0, top: 0});

		$("#" + targetId + " span.color").css({"background-color" : $(this).css("background-color")});
		$("#" + targetId).change();

		$("#colorTarget").val("");
	});

	$(".grayPicker").click(function() {
		if ($("#colorTarget").val() == $(this).attr("id")) {
			grayPickerContainer.hide();
			return;
		}

		var buttonOffset = $(this).offset();
		grayPickerContainer.css({left: buttonOffset.left + "px", top: (buttonOffset.top + 40) + "px"});
		grayPickerContainer.show();
		$("#colorTarget").val($(this).attr("id"));
		var selectedColor = $(this).find(".color").css("background-color");
		grayPicker.find("div").each(function() {
			if (selectedColor == $(this).css("background-color")) {
				$(this).css({borderColor: "#ffffff"});
			}
			else {
				$(this).css({borderColor: $(this).css("background-color")});
			}
		});
	});

	$("#foregroundColor").change(function() {
		if (!selectedId) return;

		$("#" + selectedId).children().css({color: $(this).find(".color").css("background-color")});
	});

	$("#backgroundColor").change(function() {
		if (!selectedId) return;

		$("#" + selectedId).children().css({backgroundColor: $(this).find(".color").css("background-color")});
	});
}

$(function() {
	$("#saveVotingPaperButton").click(sendVotingPaperForm);
	$("#createVotingPaperPdfButton").click(createVotingPaperPdf);

	$("#paperFormat").change(function() {
		changePaperFormat();
	});

	$("#votingPaper").droppable({
		drop: inPaperDrop
	});

	$("#itemDiv li").draggable({
		opacity: 0.7,
		helper: "clone"
	});

	$("#imageSelect").change(function() {
		$("#imageUl img").attr("src", $("#imageSelect").val());
	});

	$("#layerTools button").attr("disabled", "disabled");
	$("#textTools").hide();
	$("#multipleTools button").attr("disabled", "disabled");
	$("#imageTools").hide();

	changePaperFormat();
	initHandlers();
	initSelectorHandlers();
	initTextHandlers();

	initGrayPicker();
});