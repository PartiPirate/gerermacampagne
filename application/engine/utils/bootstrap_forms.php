<?php /*
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

function addAlertDialog($id, $text, $level = "default") {
	$formElement = "";
	$formElement .= "<div id='$id' class='alert alert-$level otbHidden' role='alert'>$text</div>";

	return $formElement;
}

function addPagination($numberOfElements, $numberOfElementsPerPage) {

	if ($numberOfElements < $numberOfElementsPerPage) return "";

	$formElement = "";

	$formElement .= "<nav class=\"text-center\">";
	$formElement .= "	<ul class=\"pagination\">";
	$formElement .= "		<li class=\"disabled\"><a href=\"#\"><span aria-hidden=\"true\">&laquo;</span><span class=\"sr-only\">Previous</span> </a></li>";
	$formElement .= "		<li class=\"active\"><a href=\"#\">1</a></li>";

	for($page = 2; $page <= ceil($numberOfElements / $numberOfElementsPerPage); $page++) {
		$formElement .= "		<li><a href=\"#\">$page</a></li>";
	}

	$formElement .= "		<li><a href=\"#\"><span aria-hidden=\"true\">&raquo;</span><span class=\"sr-only\">Next</span> </a></li>";
	$formElement .= "	</ul>";
	$formElement .= "</nav>";

	return $formElement;
}
?>