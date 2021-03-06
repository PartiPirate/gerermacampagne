 /*
    Copyright 2016 Cédric Levieux, Parti Pirate

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

var tweetPerPage = 5;

function showPage(table, page) {
	var counter = 0;
	var minIndex = (page - 1) * tweetPerPage - 1;
	var maxIndex = page * tweetPerPage;
	table.find("tbody tr").each(function() {
		if (minIndex < counter && counter < maxIndex) {
			$(this).show();
		}
		else {
			$(this).hide();
		}
		counter++;
	});
}

$(function() {
	$("table").each(function() {
		if ($(this).attr("aria-do-not-paginate") && $(this).attr("aria-do-not-paginate") == "true") return;
		showPage($(this), 1);
	});

	$(".pagination li a").click(function(e) {
		e.preventDefault();

		var text = $(this).text();
		var page = -1;
		var currentPage = $(this).parents("nav").find("li.active").text();
		var length = $(this).parents("nav").find("li").length;

		if ($.isNumeric(text)) {
			page = text;
		}
		else if (text.indexOf("Previous") != -1) {
			page = currentPage - 1;
		}

		else if (text.indexOf("Next") != -1) {
			page = currentPage - (-1);
		}

		if (page < 1) page = 1;
		if (page > length - 2) page = length -2;

		var counter = 0;

		$(this).parents("nav").find("li").each(function() {
			if (counter == page) {
				$(this).addClass("active");
			}
			else if (counter == 0) {
				if (page == 1) {
					$(this).addClass("disabled");
				}
				else {
					$(this).removeClass("disabled");
				}
			}
			else if (counter == length - 1) {
				if (page == length - 2) {
					$(this).addClass("disabled");
				}
				else {
					$(this).removeClass("disabled");
				}
			}
			else {
				$(this).removeClass("active");
			}

			counter++;
		});

		var table = $(this).parents("nav").siblings("table").eq(0);

		showPage(table, page);
	});
});