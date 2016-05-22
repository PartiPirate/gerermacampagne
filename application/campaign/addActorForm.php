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
<div id="addActorDiv" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Ajouter un acteur</h4>
				</div>
				<form id="addActorForm" method="post" class="form-horizontal">

					<input type="hidden" name="campaignId" value="<?php echo $campaign["cam_id"]; ?>" />

					<div class="modal-body">
						<p>Renseignez les informations pour cet acteur</p>
					</div>

					<fieldset>
						<legend>Identité</legend>

						<div class="form-group has-feedback pseudo-group">
							<label class="col-md-3 control-label" for="firstname">Pseudo</label>
							<div class="col-md-4">
								<input id="pseudo" name="pseudo" type="text" class="form-control" placeholder="Pseudo" >
							</div>
						</div>

						<div class="form-group has-feedback company-name-group" style="display: none;">
							<label class="col-md-3 control-label" for="companyName">Raison social</label>
							<div class="col-md-8">
								<input id="companyName" name="companyName" type="text" class="form-control" placeholder="Raison sociale" >
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="firstname">Identité</label>
							<div class="col-md-4">
								<input id="firstname" name="firstname" type="text" class="form-control" placeholder="Prénom" >
							</div>
							<div class="col-md-4">
								<input id="lastname" name="lastname" type="text" class="form-control" placeholder="Nom" >
							</div>
						</div>

						<!-- Email input-->
						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="mail">Mail</label>
							<div class="col-md-6">
								<input id="mail" name="mail" value="" type="email"
									placeholder="Mail" class="form-control input-md">
								<span id="mailStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
								<p id="mailHelp" class="help-block otbHidden"></p>
							</div>
			 			</div>

						<!-- Telephone inputs-->
						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="mail">Téléphones</label>
							<div class="col-md-4">
								<input id="telephone" name="telephone" value="" type="telephone"
									placeholder="Téléphone" class="form-control input-md">
								<span id="telephoneStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
								<p id="telephoneHelp" class="help-block otbHidden"></p>
							</div>
							<div class="col-md-4">
								<input id="fax" name="fax" value="" type="telephone"
									placeholder="Fax" class="form-control input-md">
								<span id="faxStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
								<p id="faxHelp" class="help-block otbHidden"></p>
							</div>
						</div>

					</fieldset>

					<fieldset>
						<legend>Fonction</legend>

						<!-- Fonction input-->
						<div class="form-group">
							<label class="col-md-3 control-label" for="functionInput">Poste</label>
							<div class="col-md-8">
								<input id="functionInput" name="functionInput"
									value="" type="hidden">
								<div id="functionButtons" class="btn-group" role="group" aria-label="...">
									<button value="listHead" type="button" class="btn btn-default active"><?php echo lang("rights_listHead"); ?></button>
									<button value="candidate" type="button" class="btn btn-default"><?php echo lang("rights_candidate"); ?></button>
									<button value="substitute" type="button" class="btn btn-default"><?php echo lang("rights_substitute"); ?></button>
									<button value="representative" type="button" class="btn btn-default"><?php echo lang("rights_representative"); ?></button>
									<button value="charteredAccountant" type="button" class="btn btn-default"><?php echo lang("rights_charteredAccountant"); ?></button>
								</div>
							</div>
						</div>

					</fieldset>

					<fieldset>
						<legend>Adresse</legend>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="line1">Adresse</label>
							<div class="col-md-8">
								<input id="line1" name="line1" type="text" class="form-control" placeholder="Première ligne..." >
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="line2"></label>
							<div class="col-md-8">
								<input id="line2" name="line2" type="text" class="form-control" placeholder="Deuxième ligne..." >
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="zipCode">Ville</label>
							<div class="col-md-3">
								<input id="zipCode" name="zipCode" type="text" class="form-control" placeholder="Code postal" >
							</div>
							<div class="col-md-5">
								<input id="city" name="city" type="text" class="form-control" placeholder="Ville" >
							</div>
						</div>

					</fieldset>

				</form>

				<div class="modal-footer">
					<button id="closeAddActorButton" type="button" class="btn btn-default">Fermer</button>
					<button id="addActorButton" type="button" class="btn btn-primary">Ajouter</button>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
