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
					<h4 class="modal-title">Envoyer un mail collectif</h4>
				</div>
				<form id="sendMailForm" method="post" class="form-horizontal">

					<input type="hidden" name="fromId" id="fromId" value="" />
					<input type="hidden" name="fromType" id="fromType" value="" />
					<input type="hidden" name="toId" id="toId" value="" />
					<input type="hidden" name="toType" id="toType" value="user" />

					<input type="hidden" name="toTaskStatus" id="toTaskStatus" value="user" />

					<div class="modal-body">
						<p>Envoyer un mail collectif aux différents participants, suivant qu'ils ont fait des tâches ou non.</p>
					</div>

					<fieldset>
						<legend>Destinataires</legend>

						<div class="form-group has-feedback">
							<div class="col-md-12 text-center">
								<div id="toTypeButtons" class="btn-group" role="group">
									<button value="candidate" type="button" class="btn btn-default active"><?php echo lang("send_mail_to_candidates"); ?></button>
									<button value="representative" type="button" class="btn btn-default"><?php echo lang("send_mail_to_representatives"); ?></button>
								</div>
							</div>
						</div>

						<div class="form-group has-feedback">
							<div class="col-md-12 text-center">
								<div id="toTaskStatusButtons" class="btn-group" role="group">
									<button value="status-all" type="button" class="btn btn-default active"><?php echo lang("send_mail_to_status_all"); ?></button>
									<button value="status-done" type="button" class="btn btn-default"><?php echo lang("send_mail_to_status_done"); ?></button>
									<button value="status-not-done" type="button" class="btn btn-default"><?php echo lang("send_mail_to_status_not_done"); ?></button>
								</div>
							</div>
						</div>

						<div class="form-group has-feedback" id="task-list" style="display: none;">
							
							<label class="col-md-3 control-label" for="dependencies">les tâches suivantes :</label>
							<div class="col-md-9">
								<div class="checkbox">
<?php 	foreach($taskModels as $task) { ?>
									<label for="task-<?php echo $task["tmo_id"]; ?>">
										<input type="checkbox"
											value="<?php echo $task["tmo_label"]; ?>"
											name="toTasks[]"
											id="task-<?php echo $task["tmo_id"]; ?>">
									<?php
										if (isLanguageKey($task["tmo_label"])) {
											echo lang($task["tmo_label"]);
										}
										else {
											echo $task["tmo_label"];
										}

									?>
								    </label>
<?php 	}?>
								</div>
							</div>

						</div>
						
					</fieldset>

					<fieldset>
						<legend>Message</legend>

						<div class="form-group has-feedback">
							
							<label class="col-md-3 control-label" for="dependencies">Sujet :</label>
							<div class="col-md-8">
								<input id="subject" name="subject" type="text" class="form-control" >
							</div>
						</div>

						<div class="form-group has-feedback">
							
							<label class="col-md-3 control-label" for="dependencies">Message :</label>
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