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

function resizeWindow() {
	$(".theme-showcase").css("min-height", ($(window).height() - 94) + "px");
}

$(function() {
	resizeWindow();

	 $(window).resize(function() {
			resizeWindow();
     });

	 $('[data-toggle="tooltip"]').tooltip();
});