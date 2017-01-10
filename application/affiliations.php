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
include_once("header.php");
require_once("engine/bo/TemplateBo.php");

$templateBo = TemplateBo::newInstance($connection, $config);

$waitingAffiliations = $ppBo->getWaitingAffiliations($administratedParties);
$templates = $templateBo->getByFilters(array("cte_active" => 1));

foreach($administratedParties as $index => $party) {
	$administratedParties[$index]["campaigns"] = $campaignBo->getCampaigns(array("partyId" => $party["ppa_id"]));
}

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_affiliations"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("affiliations_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

<div class="clearfix"></div>

<br />

<div class="col-md-12 text-center" id="filterDiv">
	<div id="filterButtons" class="btn-group" role="group" aria-label="...">
		<button value="no" type="button" class="btn btn-default active"><?php echo lang("tasks_filter_no"); ?></button>
<?php	foreach($templates as $template) { ?>		
		<button value="<?php echo $template["cte_id"]; ?>" type="button" class="btn btn-default"><?php echo $template["cte_label"]; ?></button>
<?php	}	?>
	</div>
</div>

<div class="clearfix"></div>

<br />

	<?php 		if (count($waitingAffiliations)) {?>
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo lang("affiliations_waiting_title"); ?></h3>
			</div>
			<table class="table" id="waitingAffiliationsTable">
				<thead>
					<tr>
						<th><?php echo lang("party_property_party"); ?></th>
						<th><?php echo lang("campaign_property_name"); ?></th>
						<th><?php echo lang("campaign_property_electoralDistrict"); ?></th>
						<th><?php echo lang("affiliations_candidates"); ?></th>
						<th><?php echo lang("affiliations_actions"); ?></th>
					</tr>
				</thead>
				<tbody>
<?php 	foreach($waitingAffiliations as $affiliation) {	?>
					<tr aria-id="<?php echo $affiliation["aff_id"]; ?>" data-template-id="<?php echo $affiliation["cte_id"]; ?>">
						<td class="vertical-middle"><?php echo $affiliation["ppa_name"]; ?></td>
						<td class="vertical-middle"><?php echo $affiliation["cam_name"]; ?></td>
						<td class="vertical-middle"><?php echo $affiliation["cam_electoral_district"]; ?></td>
						<td class="vertical-middle"><?php echo $affiliation["aff_candidates"]; ?></td>
						<td class="vertical-middle">
							<button class="acceptButton btn btn-success"><?php echo lang("common_accept_button"); ?> <span class="glyphicon glyphicon-ok"></span></button>
							<button class="refuseButton btn btn-danger"><?php echo lang("common_refuse_button"); ?> <span class="glyphicon glyphicon-remove"></span></button>
						</td>
					</tr>
<?php 	} ?>
				</tbody>
			</table>
		</div>
	</div>

	<?php 		}?>


	<?php 	foreach($administratedParties as $administratedParty) {?>
	<?php 		foreach($administratedParty["campaigns"] as $partyCampaign) {


					$partyCampaign["actors"] = $campaignBo->getRighters($partyCampaign["cam_id"], array("listHead", "candidate"));
					$partyCampaign["tasks"] = $taskBo->getTasks($partyCampaign["cam_id"]);

//					print_r($partyCampaign);

		?>

		<div class="col-md-6" data-template-id="<?php echo $partyCampaign["cte_id"]; ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo $partyCampaign["cam_name"]; ?></h3>
				</div>

				<form class="form-horizontal">
					<fieldset>
					
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-12">
							<label class="col-md-4 control-label" for="campaignTemplateInput"><?php echo lang("campaign_property_template"); ?></label>
							<div class="col-md-8">
								<p class="form-control-static"><?php echo $partyCampaign["cte_label"]; ?></p>
							</div>
						</div>
					
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-6">
							<label class="col-md-6 control-label" for="electoralDistrictInput"><?php echo lang("campaign_property_electoralDistrict"); ?></label>
							<div class="col-md-6">
								<p class="form-control-static"><?php echo $partyCampaign["cam_electoral_district"]; ?></p>
							</div>
						</div>

						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-6">
							<label class="col-md-6 control-label" for="electoralDistrictInput"><?php echo lang("campaign_property_party"); ?></label>
							<div class="col-md-6">
								<p class="form-control-static"><?php echo $partyCampaign["ppa_name"]; ?></p>
							</div>
						</div>

						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-6">
							<label class="col-md-6 control-label" for="startDateInput"><?php echo lang("campaign_property_startDate"); ?></label>
							<div class="col-md-6">
								<p class="form-control-static"><?php 
									$date = new DateTime($partyCampaign["cam_start_date"]);
									$date = $date->format(lang("date_format"));
									echo $date;
//									print_r($date);
//									echo $partyCampaign["cam_start_date"]; 
								?></p>
							</div>
						</div>
						<?php 	if ($partyCampaign["cam_finish_date"] != "0000-00-00") {?>
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-6">
							<label class="col-md-6 control-label" for="finishDateInput"><?php echo lang("campaign_property_finishDate"); ?></label>
							<div class="col-md-6">
								<p class="form-control-static"><?php 
                                                                        $date = new DateTime($partyCampaign["cam_finish_date"]);
                                                                        $date = $date->format(lang("date_format"));
                                                                        echo $date;
//									echo $partyCampaign["cam_finish_date"]; 
								?></p>
							</div>
						</div>
						<?php 	}?>

						<?php	$separator = "";
								$candidates = "";

//								print_r($partyCampaign["actors"]);

								foreach($partyCampaign["actors"] as $actor) {
									$candidates .= $separator;
									$candidates .= $actor["add_entity"];
									$separator = ", ";
								}?>
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-12">
							<label class="col-md-3 control-label" for="finishDateInput"><?php echo lang("campaign_property_candidates"); ?></label>
							<div class="col-md-9">
								<p class="form-control-static"><?php echo $candidates; ?></p>
							</div>
						</div>

						<?php 	$task = null;

								foreach($partyCampaign["tasks"] as $pcTask) {
									if ($pcTask["tas_status"] == "inProgress") {
										$task = $pcTask;
										break;
									}
								}

								if ($task) {?>
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-12">
							<label class="col-md-3 control-label" for="finishDateInput"><?php echo lang("campaign_property_firstTask"); ?></label>
							<div class="col-md-9">
								<p class="form-control-static"><?php echo lang($task["tas_label"]); ?></p>
							</div>
						</div>
						<?php 	}?>

					</fieldset>
				</form>

			</div>
		</div>

	<?php 		}?>

	<div class="clearfix"></div>

	<?php 	}?>

	<?php 	}
			else {
				include("connectButton.php");
			}?>

</div>

<div class="lastDiv"></div>

<script type="text/javascript">
</script>
<?php include("footer.php");?>
</body>
</html>
