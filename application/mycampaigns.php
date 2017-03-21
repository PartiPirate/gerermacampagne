<?php /*
	Copyright 2016-2017 Cédric Levieux, Parti Pirate

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

$myCampaigns = array();
if ($user) {
	$myCampaigns = $campaignBo->getCampaigns(array("userId" => $userId, "withRights" => true));
}

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_mycampaigns"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("mycampaigns_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

<div class="clearfix"></div>

<br />

	<?php 		foreach($myCampaigns as $myCampaign) {


					$myCampaign["actors"] = $campaignBo->getRighters($myCampaign["cam_id"], array("listHead", "candidate"));
					$myCampaign["tasks"] = $taskBo->getTasks($myCampaign["cam_id"]);

//					print_r($myCampaign);

		?>

		<div class="col-md-6 campaign mode-text" 
			data-template-id="<?php echo $myCampaign["cte_id"]; ?>" 
			data-campaign-id="<?php echo $myCampaign["cam_id"]; ?>"
			data-party-id="<?php echo $administratedParty["ppa_id"]; ?>"
			data-reject-code="<?php echo $myCampaign["cam_reject_code"]; ?>"
		>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo $myCampaign["cam_name"]; ?></h3>
				</div>

				<form class="form-horizontal">
					<fieldset>
					
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-12 mode-text">
							<label class="col-md-4 control-label" for="campaignTemplateInput"><?php echo lang("campaign_property_template"); ?></label>
							<div class="col-md-8">
								<p class="form-control-static"><?php echo $myCampaign["cte_label"]; ?></p>
							</div>
						</div>
					
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-6 mode-text">
							<label class="col-md-6 control-label" for="electoralDistrictInput"><?php echo lang("campaign_property_electoralDistrict"); ?></label>
							<div class="col-md-6">
								<p class="form-control-static"><?php echo $myCampaign["cam_electoral_district"]; ?></p>
							</div>
						</div>

						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-6 mode-text">
							<label class="col-md-6 control-label" for="electoralDistrictInput"><?php echo lang("campaign_property_party"); ?></label>
							<div class="col-md-6">
								<p class="form-control-static"><?php echo $myCampaign["ppa_name"]; ?></p>
							</div>
						</div>

						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-6 mode-text">
							<label class="col-md-6 control-label" for="startDateInput"><?php echo lang("campaign_property_startDate"); ?></label>
							<div class="col-md-6">
								<p class="form-control-static"><?php 
									$date = new DateTime($myCampaign["cam_start_date"]);
									$date = $date->format(lang("date_format"));
									echo $date;
//									print_r($date);
//									echo $myCampaign["cam_start_date"]; 
								?></p>
							</div>
						</div>
						<?php 	if ($myCampaign["cam_finish_date"] != "0000-00-00") {?>
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-6 mode-text">
							<label class="col-md-6 control-label" for="finishDateInput"><?php echo lang("campaign_property_finishDate"); ?></label>
							<div class="col-md-6">
								<p class="form-control-static"><?php 
                                                                        $date = new DateTime($myCampaign["cam_finish_date"]);
                                                                        $date = $date->format(lang("date_format"));
                                                                        echo $date;
//									echo $myCampaign["cam_finish_date"]; 
								?></p>
							</div>
						</div>
						<?php 	}?>

						<?php	$separator = "";
								$candidates = "";

//								print_r($myCampaign["actors"]);

								foreach($myCampaign["actors"] as $actor) {
									$candidates .= $separator;
									$candidates .= $actor["add_entity"];
									$separator = ", ";
								}?>
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-12 mode-text">
							<label class="col-md-3 control-label" for="finishDateInput"><?php echo lang("campaign_property_candidates"); ?></label>
							<div class="col-md-9">
								<p class="form-control-static"><?php echo $candidates; ?></p>
							</div>
						</div>

						<?php 	$task = null;
								$dones = 0;

								foreach($myCampaign["tasks"] as $pcTask) {
									if ($task == null && $pcTask["tas_status"] == "inProgress") {
										$task = $pcTask;
									}
									else if ($pcTask["tas_status"] == "done") {
										$dones++;
									}	
								}

								if ($task) {?>
						<div class="form-group has-feedback input-sm margin-bottom-0 col-md-12 mode-text">
							<label class="col-md-4 control-label" for="finishDateInput"><?php echo lang("campaign_property_firstTask"); ?></label>
							<div class="col-md-8">
								<p class="form-control-static"><?php echo lang($task["tas_label"]); ?></p>
							</div>
						</div>
								<?php 	}?>
								
						<div class="margin-bottom-0 input-sm col-md-12 mode-graphic">
							<label class="col-md-4 control-label" style="margin-top: -5px;"><?php echo $myCampaign["cam_name"]; ?></label>
							<div class="col-md-8 progress" style="padding: 0;" title="<?php echo $dones; ?> / <?php echo count($myCampaign["tasks"]); ?>">
								<?php 
									$width = $dones / count($myCampaign["tasks"]) * 100;
									$width = round($width);
								?>
								<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $width; ?>%;">
									<?php echo $dones; ?> / <?php echo count($myCampaign["tasks"]); ?>
								</div>
							</div>
						</div>
						

					</fieldset>
				</form>
<!--
				<div class="panel-footer text-right">
					<button type="button" class="btn btn-danger btn-xs btn-reject"><?php echo lang("affiliation_button_remove"); ?> <span class="glyphicon glyphicon-remove"></span></button>
				</div>
-->
			</div>
		</div>

	<?php 		}?>

	<div class="clearfix"></div>

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
