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

if ($campaign) {

	$parties = $ppBo->getAdministratedParties($userId);

	$taskBo->startCampaign($campaign);

	$campaign["actors"] = $campaignBo->getRighters($campaign["cam_id"], array("listHead", "candidate", "substitute", "representative", "charteredAccountant"));
	$campaign["tasks"] = $taskBo->getTasks($campaign["cam_id"]);
	$campaign["map_tasks"] = array();

	foreach($campaign["tasks"] as $task) {
		$campaign["map_tasks"][$task["tas_id"]] = $task;
	}

	$hasListHead = false;
	$hasCandidate = false;
	$hasSubstitute = false;
	$hasRepresentative = false;

	$actorProblem = 0;

	foreach($campaign["actors"] as $actor) {
		switch($actor["uri_right"]) {
			case "listHead":
				$hasListHead = true;
				break;
			case "candidate":
				$hasCandidate = true;
				break;
			case "substitute":
				$hasSubstitute = true;
				break;
			case "representative":
				$hasRepresentative = true;
				break;
		}
	}

	if (!$hasRepresentative) $actorProblem++;
	if ($hasListHead && !($hasSubstitute || $hasCandidate)) $actorProblem++;
	if ($hasCandidate && !($hasSubstitute || $hasListHead)) $actorProblem++;
	if (!($hasListHead && $hasCandidate)) $actorProblem += 2;

	switch($actorProblem) {
		case 0:
			$actorProblemLevel = "success";
			break;
		case 1:
			$actorProblemLevel = "warning";
			break;
		default:
			$actorProblemLevel = "danger";
	}
}
?>

<?php 	if ($campaign) {?>

<script>

var tasks = <?php echo json_encode($campaign["tasks"]); ?>

</script>

<div class="col-md-6">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo lang("index_campaign_info_title"); ?></h3>
		</div>
		<form class="form-horizontal">
			<input type="hidden" id="campaignId" name="campaignId" value="<?php echo $campaign["cam_id"]; ?>" />
			<fieldset>
				<div class="form-group has-feedback input-sm margin-bottom-0">
					<label class="col-md-4 control-label" for="nameInput"><?php echo lang("campaign_property_name"); ?></label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $campaign["cam_name"]; ?></p>
					</div>
				</div>
				<?php	if ($campaign["cte_label"]) { ?>
				<div class="form-group has-feedback input-sm margin-bottom-0">
					<label class="col-md-4 control-label" for="nameInput"><?php echo lang("campaign_property_template_label"); ?></label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $campaign["cte_label"]; ?></p>
					</div>
				</div>
				<?php	} ?>
				<div class="form-group has-feedback input-sm margin-bottom-0">
					<label class="col-md-4 control-label" for="electoralDistrictInput"><?php echo lang("campaign_property_electoralDistrict"); ?></label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $campaign["cam_electoral_district"]; ?></p>
					</div>
				</div>

				<div class="form-group has-feedback input-sm margin-bottom-0">
					<label class="col-md-4 control-label" for="electoralDistrictInput"><?php echo lang("campaign_property_party"); ?></label>
					<div class="col-md-8">
						<?php 	if ($campaign["cam_political_party_date"] != "0000-00-00") {?>
						<p class="form-control-static"><?php echo $campaign["ppa_name"]; ?></p>
						<?php 	} else {?>
						<select id="politicalChoice" name="politicalChoice" class="form-control">
							<option value=""><?php echo lang("campaign_property_party_placeholder"); ?></option>
							<?php 	foreach ($parties as $party) {?>
							<option value="<?php echo $party["ppa_id"]; ?>" <?php if ($party["ppa_id"] == $campaign["cam_political_party_id"]) { echo "selected='selected'"; } ?>><?php echo $party["ppa_name"]; ?></option>
							<?php 	}?>
						</select>
						<?php 	}?>
					</div>
				</div>

				<div class="form-group has-feedback input-sm margin-bottom-0">
					<label class="col-md-4 control-label" for="startDateInput"><?php echo lang("campaign_property_startDate"); ?></label>
					<div class="col-md-8">
						<p class="form-control-static"><?php
                                                        $date = new DateTime($partyCampaign["cam_start_date"]);
                                                        $date = $date->format(lang("date_format"));
                                                        echo $date;
//							echo $campaign["cam_start_date"];
						?></p>
					</div>
				</div>
				<?php 	if ($campaign["cam_finish_date"] != "0000-00-00") {?>
				<div class="form-group has-feedback input-sm margin-bottom-0">
					<label class="col-md-4 control-label" for="finishDateInput"><?php echo lang("campaign_property_finishDate"); ?></label>
					<div class="col-md-8">
						<p class="form-control-static"><?php
                                                        $date = new DateTime($partyCampaign["cam_finish_date"]);
                                                        $date = $date->format(lang("date_format"));
                                                        echo $date;
//							echo $campaign["cam_finish_date"];
						?></p>
					</div>
				</div>
				<?php 	}?>
			</fieldset>
		</form>
	</div>
</div>
<div class="col-md-6" id="actorListContainer">
	<div class="panel panel-default panel-<?php echo $actorProblemLevel; ?>">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo lang("index_campaign_actors_title"); ?></h3>
		</div>

		<ul class="list-group" id="actorList">
			<?php	foreach($campaign["actors"] as $actor) {?>
			<li class="list-group-item vertical-middle"><?php

				if ($actor["uri_right"] == "charteredAccountant") {
					echo $actor["add_entity"];
				}
				else {
					echo $actor["use_login"];
					if ($actor["add_entity"]) {
						echo " / " . $actor["add_entity"];
					}
				}

			?>

			<?php if (count($campaign["actors"]) > 1 && ($actor["use_id"] == $userId || $campaign["uri_right"] == "listHead" || $campaign["uri_right"] == "candidate")) {?>
			<a class="btn btn-xs btn-danger removeActorButton"
				title="Supprimer le rôle de cette personne"
				id="removeActor-<?php echo $actor["uri_id"]; ?>"
				href="#" role="button"><span class="glyphicon glyphicon-remove"></span></a>
			<?php }?>
			<span class="badge"><?php echo lang("rights_" . $actor["uri_right"]) ?></span></li>
			<?php 	}?>
		</ul>
	</div>

	<?php
			// On ne peut ajouter des gens que si on est candidat ou tête de liste
			if ($campaign["uri_right"] == "candidate" || $campaign["uri_right"] == "listHead") {?>
	<div class="text-center">
		<button id="openAddActorButton" type="button" class="btn btn-default" data-toggle="modal" data-target="#addActorDiv">Ajouter un acteur</button>
	</div>
	<?php 	}?>

</div>

<div class="clearfix"></div>

<br />

<div class="col-md-12 text-center" id="filterDiv">
	<div id="filterButtons" class="btn-group" role="group" aria-label="...">
		<button value="no" type="button" class="btn btn-default active"><?php echo lang("tasks_filter_no"); ?></button>
		<button value="done" type="button" class="btn btn-default"><?php echo lang("tasks_filter_done"); ?></button>
		<button value="emergency" type="button" class="btn btn-default"><?php echo lang("tasks_filter_emergency"); ?></button>
		<button value="candidate" type="button" class="btn btn-default"><?php echo lang("tasks_filter_candidate"); ?></button>
		<button value="representative" type="button" class="btn btn-default"><?php echo lang("tasks_filter_representative"); ?></button>
	</div>
</div>

<div class="clearfix"></div>

<br />

<?php
		$now = new DateTime(date("Y-m-d"));

		foreach($campaign["tasks"] as $task) {

			$style = TaskBo::getStyle($task, $now);
			$limitDate = "";

			switch($task["tas_status"]) {
				case "inProgress" :
					if ($task["tas_limit_date"] != "0000-00-00") {
						$glyphicon = "glyphicon-calendar";
						$limitDate = new DateTime($task["tas_limit_date"]);
						$limitDate = $limitDate->format(lang("date_format"));
					}
					else {
						$glyphicon = "glyphicon-star-empty";
					}
					break;
				case "done" :
					$glyphicon = "glyphicon-ok";
					break;
			}
			?>
<div class="task alert alert-<?php echo $style; ?>"
	role="alert"
	data-righters="<?php echo join(",", $task["tas_righters"]); ?>"
	data-done="<?php echo $task["tas_form"]; ?>"
	data-id="<?php echo $task["tas_id"]; ?>"
	id="task-<?php echo $task["tas_id"]; ?>">
	<span class="status glyphicon <?php echo $glyphicon; ?>" aria-hidden="true"
			data-toggle="tooltip" data-placement="bottom" title="<?php echo $limitDate; ?>"></span>
	<?php echo isLanguageKey($task["tas_label"]) ? lang($task["tas_label"]) : $task["tas_label"]; ?>

	<?php
// 			echo "Has right : " . TaskBo::hasRightToDoTask($userId, $campaign, $task) . "<br >";
// 			echo "Has possibility : " . TaskBo::hasPossibilityToDoTask($task, $campaign["map_tasks"]) . "<br >";

			if ($style != "success" &&
					TaskBo::hasRightToDoTask($userId, $campaign, $task) &&
					TaskBo::hasPossibilityToDoTask($task, $campaign["map_tasks"])) {?>
		<a class="btn btn-xs btn-success pull-right"
			title="Cliquez pour indiquer que vous avez réalisé la tâche"
			data-toggle="tooltip" data-placement="top"
			id="doneTask-<?php echo $task["tas_id"]; ?>"
			href="#" role="button"><span class="glyphicon glyphicon-ok"></span></a>
	<?php 	}?>
	<div class="clearfix"></div>

	<?php	//print_r($task);
			if (count($task["tas_dependencies"])) {?>
			<p class="text-right">
	<?php 		$separator = '<span class="glyphicon glyphicon-tags"></span>&nbsp;';
				foreach($task["tas_dependencies"] as $parentTask) {
					echo $separator; ?>
						<a href="#task-<?php echo $parentTask; ?>"
							class="<?php if ($campaign["map_tasks"][$parentTask]["tas_status"] == "done") { echo "text-success"; } ?>"><?php
								$label = $campaign["map_tasks"][$parentTask]["tas_label"];

								if (isLanguageKey($label)) {
									$label = lang($label);
								}

								echo $label;
							?></a>
	<?php 			$separator = ", ";
				}?>
			</p>
	<?php 	}?>
</div>
<?php 	}?>

<div class="col-md-12 text-center">
	<button id="openAddTaskButton"
		data-toggle="modal" data-target="#addTaskDiv"
		type="button" class="btn btn-success"><?php echo lang("campaign_add_task_button"); ?></button>
</div>

<div class="col-md-12 text-center">
	<a href="do_downloadCalendar.php?campaignId=<?php echo $campaign["cam_id"]; ?>"><?php echo lang("tasks_calendar"); ?></a>
	-
	<a href="do_downloadCalendar.php?role=candidate&campaignId=<?php echo $campaign["cam_id"]; ?>"><?php echo lang("tasks_calendar_candidate"); ?></a>
	-
	<a href="do_downloadCalendar.php?role=representative&campaignId=<?php echo $campaign["cam_id"]; ?>"><?php echo lang("tasks_calendar_representative"); ?></a>
</div>

	<!-- Add actor formular -->
	<?php include "campaign/addActorForm.php"; ?>

	<!-- Add task formular -->
	<?php include "campaign/addTaskForm.php"; ?>



<?php 	} else { ?>
<div class="col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo lang("index_nocampaign_title"); ?></h3>
		</div>
		<div class="panel-body"><?php echo lang("index_nocampaign_content"); ?></div>

		<form class="form-horizontal">
			<fieldset>
				<!-- Form Name -->
				<legend>
					<?php 	echo lang("index_nocampaign_form_legend"); ?>
				</legend>

				<div class="form-group has-feedback">
					<label class="col-md-4 control-label" for="nameInput"><?php echo lang("index_nocampaign_form_nameInput"); ?></label>
					<div class="col-md-8">
						<input id="nameInput" name="nameInput" value="" type="text"
							placeholder="" class="form-control input-md">
						<span id="nameStatus"
							class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
						<p id="nameHelp" class="help-block otbHidden"></p>
					</div>
				</div>

				<div class="form-group has-feedback">
					<label class="col-md-4 control-label" for="electoralDistrictInput"><?php echo lang("index_nocampaign_form_electoralDistrictInput"); ?></label>
					<div class="col-md-8">
						<input id="electoralDistrictInput" name="electoralDistrictInput" value="" type="text"
							placeholder="" class="form-control input-md">
						<span id="electoralDistrictStatus"
							class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
						<p id="electoralDistrictHelp" class="help-block otbHidden"></p>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4 control-label" for="listHeadButton"><?php echo lang("index_nocampaign_form_rightInput");?></label>
					<div class="col-md-8">
						<div id="rightButtons" class="btn-group" role="group" aria-label="...">
							<button value="listHead" type="button" class="btn btn-default active">Tête de liste</button>
							<button value="substitute" type="button" class="btn btn-default">Suppléant</button>
							<button value="candidate" type="button" class="btn btn-default">Candidat</button>
							<button value="representative" type="button" class="btn btn-default">Mandataire</button>
						</div>
						<input id="rightInput" name="rightInput" value="listHead" type="hidden">
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4 control-label" for="startDateInput"><?php echo lang("index_nocampaign_form_startDateInput");?></label>
					<div class="col-md-3">
		                <div class='input-group date'>
		                    <input id='startDateInput' type='text' class="form-control" placeholder="<?php echo lang("index_nocampaign_form_startDatePlaceHolder");?>"
		                    	data-date-format="YYYY-MM-DD"/>
		                    <span class="input-group-addon"><span
		                    	class="glyphicon glyphicon-calendar"></span>
		                    </span>
		                </div>
			        </div>
				</div>

				<div class="form-group">
					<label class="col-md-4 control-label" for="finishDateInput"><?php echo lang("index_nocampaign_form_finishDateInput");?></label>
					<div class="col-md-3">
		                <div class='input-group date'>
		                    <input id='finishDateInput' type='text' class="form-control" placeholder="<?php echo lang("index_nocampaign_form_finishDatePlaceHolder");?>"
		                    	data-date-format="YYYY-MM-DD"/>
		                    <span class="input-group-addon"><span
		                    	class="glyphicon glyphicon-calendar"></span>
		                    </span>
		                </div>
			        </div>
				</div>

				<div class="form-group">
					<label class="col-md-4 control-label" for="saveButton"></label>
					<div class="col-md-8">
						<button id="saveButton" name="saveButton" class="saveButton btn btn-default"><?php echo lang("index_nocampaign_form_save"); ?></button>
					</div>
				</div>

			</fieldset>
		</form>
	</div>
</div>

<?php 	}?>
