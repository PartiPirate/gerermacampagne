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
	<!-- Document formular -->

	<div id="addDocumentDiv" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Ajouter un document</h4>
				</div>
				<form id="addDocumentForm" method="post" class="form-horizontal">

					<input type="hidden" name="target" id="target" value="" />
					<input type="hidden" name="targetId" id="targetId" value="" />

					<div class="modal-body">
						<p>Renseignez les informations pour ce document</p>
					</div>

					<fieldset>
						<legend>Document</legend>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="label">Libellé</label>
							<div class="col-md-8">
								<input id="label" name="label" type="text" class="form-control" placeholder="Libellé du document" >
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="checkFile">Document</label>
							<div class="col-md-8">
								<input id="documentFile" name="documentFile" value="" data-show-upload="false" type="file" placeholder=""
									class="form-control file input-md" data-show-preview="false">
								<span id="checkStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
							</div>
						</div>

					</fieldset>

				</form>

				<div class="modal-footer">
					<button id="closeButton" type="button" class="btn btn-default">Fermer</button>
					<button id="addDocumentButton" type="button" class="btn btn-primary">Ajouter</button>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>