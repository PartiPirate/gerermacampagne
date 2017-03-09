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

<?php

// TODO set template id
$taskModels = $taskBo->getTaskModels(1);

?>

	<div id="sendMailDiv" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title new-title">Créer un nouveau message</h4>
					<h4 class="modal-title answer-title">Réponse à un message</h4>
				</div>
				<form id="sendMailForm" method="post" class="form-horizontal">

					<input type="hidden" name="fromId" id="fromId" value="" />
					<input type="hidden" name="fromType" id="fromType" value="" />
					<input type="hidden" name="toId" id="toId" value="" />
					<input type="hidden" name="toType" id="toType" value="user" />

					<div class="modal-body" style="display: none;">
						<p>jflkjsdfljgmlsdf.</p>
					</div>

					<fieldset>
						<legend>Destinataires</legend>

						<div class="form-group has-feedback">
							
							<label class="col-md-1 control-label" for="fromLabel">De :</label>
							<label class="col-md-5 control-label force-text-left" id="fromLabel"></label>
							<label class="col-md-1 control-label" for="toSelect">&Agrave; :</label>
							<label class="col-md-5 control-label force-text-left" id="toLabel"></label>
							<div class="col-md-5" id="toSelect">
								<select id="toIds" name="toIds[]" class="form-control" multiple="multiple">
									<optgroup label="Personnes">
<?php	$users = array();
		foreach($userCampaigns as $userCampaign) { 
			$actors = $campaignBo->getRighters($userCampaign["cam_id"], array("listHead", "candidate", "substitute", "representative", "charteredAccountant"));
			foreach($actors as $actor) {
				$users[$actor["use_id"]] = $actor["add_entity"] ? $actor["add_entity"] : $actor["use_login"];
			}
	 	} ?>
<?php	foreach($users as $userId => $userLabel) { ?>
										<option value="user-<?php echo $userId; ?>"><?php echo $userLabel; ?></option>
<?php 	} ?>
									</optgroup>
									<optgroup label="Campagnes">
<?php	foreach($userCampaigns as $userCampaign) { ?>
										<option value="campaign-<?php echo $userCampaign["cam_id"]; ?>">&laquo;&nbsp;<?php echo $userCampaign["cam_name"]; ?>&nbsp;&raquo;</option>
										<option value="candidate-<?php echo $userCampaign["cam_id"]; ?>">candidate of &laquo;&nbsp;<?php echo $userCampaign["cam_name"]; ?>&nbsp;&raquo;</option>
										<option value="representative-<?php echo $userCampaign["cam_id"]; ?>">representative of &laquo;&nbsp;<?php echo $userCampaign["cam_name"]; ?>&nbsp;&raquo;</option>
<?php 	} ?>
									</optgroup>
									<optgroup label="Partis Politiques">
<?php	$parties = array();
		foreach($userCampaigns as $userCampaign) { 
			$parties[$userCampaign["ppa_id"]] = $userCampaign["ppa_name"];
	 	} ?>
<?php	foreach($parties as $partyId => $partyLabel) { ?>
										<option value="party-<?php echo $partyId; ?>">&laquo;&nbsp;<?php echo $partyLabel; ?>&nbsp;&raquo;</option>
<?php 	} ?>
									</optgroup>
								</select>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>Message</legend>

						<div class="form-group has-feedback">
							
							<label class="col-md-3 control-label" for="subject">Sujet :</label>
							<div class="col-md-8">
								<input id="subject" name="subject" type="text" class="form-control" >
							</div>
						</div>

						<div class="form-group has-feedback">
							
							<label class="col-md-3 control-label" for="message">Message :</label>
							<div class="col-md-8">
								<textarea id="message" name="message" class="form-control" rows="10"></textarea>
							</div>
						</div>

					</fieldset>

				</form>

				<div class="modal-footer">
					<button id="closeButton" type="button" class="btn btn-default">Fermer</button>
					<button id="sendMailButton" type="button" class="btn btn-primary">Envoyer</button>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>