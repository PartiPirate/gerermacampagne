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
<div id="addTaskDiv" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Ajouter une tâche</h4>
				</div>
				<form id="addTaskForm" method="post" class="form-horizontal">

					<input type="hidden" name="campaignId" value="<?php echo $campaign["cam_id"]; ?>" />

					<div class="modal-body">
						<p>Renseignez les informations pour cette tâche</p>
					</div>

					<fieldset>
						<legend>Tâche</legend>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="labelInput">Label</label>
							<div class="col-md-8">
								<input id="labelInput" name="labelInput" type="text" class="form-control" placeholder="Libellé de votre tâche" >
							</div>
						</div>

						<!-- Fonction input-->
						<div class="form-group">
							<label class="col-md-3 control-label" for="functionInput">Validateur</label>
							<div class="col-md-8">

								<input id="validatorInput" name="validatorInput"
									value="[]" type="hidden">

								<div id="validatorButtons" class="btn-group" role="group" aria-label="...">
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
						<legend>Dépendances</legend>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="dependencies">Cette tâche dépend de :</label>
							<div class="col-md-8">
								<div class="checkbox">
<?php 	foreach($campaign["tasks"] as $task) { ?>
									<label for="dependancy-<?php echo $task["tas_id"]; ?>">
										<input type="checkbox"
											value="<?php echo $task["tas_id"]; ?>"
											name="dependencies[]"
											id="dependency-<?php echo $task["tas_id"]; ?>">
									<?php
										if (isLanguageKey($task["tas_label"])) {
											echo lang($task["tas_label"]);
										}
										else {
											echo $task["tas_label"];
										}

									?>
								    </label>
<?php 	}?>
								</div>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>Positionnement</legend>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="line1">Date limite</label>
							<div class="col-md-3">
				                <div class='input-group date'>
				                    <input id='limitDateInput' name='limitDateInput' type='text' class="form-control" placeholder="AAAA-MM-JJ"
				                    	data-date-format="YYYY-MM-DD"/>
				                    <span class="input-group-addon"><span
				                    	class="glyphicon glyphicon-calendar"></span>
				                    </span>
				                </div>
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="line2">Ordre</label>
							<div class="col-md-8">
								<label class="radio-inline" for="order-toEnd">
									<input id="order-toEnd" name="order" value="toEnd" type="radio"
										checked="checked"
										placeholder="" >
									En fin de liste
								</label>
								<label class="radio-inline" for="order-after">
									<input id="order-after" name="order" value="after" type="radio"
										placeholder="" >
									Après :
								</label>
								<select class="form-control"
									name="afterTaskId" id="afterTaskId"
									style="width: 50%; display: inline-block;">
									<option value=""></option>
<?php 	foreach($campaign["tasks"] as $task) { ?>
									<option value="<?php echo $task["tas_id"]; ?>"><?php
										if (isLanguageKey($task["tas_label"])) {
											echo lang($task["tas_label"]);
										}
										else {
											echo $task["tas_label"];
										}

									?></option>
<?php 	}?>
								</select>
							</div>
						</div>

					</fieldset>

				</form>

				<div class="modal-footer">
					<button id="closeAddTaskButton" type="button" class="btn btn-default">Fermer</button>
					<button id="addTaskButton" type="button" class="btn btn-primary">Ajouter</button>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
