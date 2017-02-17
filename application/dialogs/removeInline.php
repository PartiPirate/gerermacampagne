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
?>
	<!-- Donation formular -->

	<div id="removeInlineDiv" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Supprimer une ligne comptable</h4>
				</div>
				<form id="removeInlineForm" method="post" class="form-horizontal">

					<input type="hidden" name="campaignId" value="<?php echo $campaign["cam_id"]; ?>" />
					<input type="hidden" name="inlineId" value="" />
					<input type="hidden" name="inlineCode" value="" />

					<div class="modal-body">
						<p>Supprimer la ligne comptable &laquo; <span class="inline-label"></span> &raquo;</p>
					</div>

				</form>

				<div class="modal-footer">
					<button id="closeButton" type="button" class="btn btn-default">Fermer</button>
					<button id="removeInlineButton" type="button" class="btn btn-danger">Supprimer</button>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>