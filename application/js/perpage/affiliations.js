/* global $ */

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
});