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
require_once("engine/bo/MessageBo.php");

$messageBo = MessageBo::newInstance($connection, $config);

//$messages = $messageBo->getByFilters(array("cte_active" => 1));

$parts = array();
$parts[] = array("id" => $userId, "type" => "user", "label" => $user);

foreach($administratedParties as $administratedParty) {
	$parts[] = array("id" => $administratedParty["ppa_id"], "type" => "party", "label" => $administratedParty["ppa_name"]);
}

foreach($userCampaigns as $userCampaign) {
	$parts[] = array("id" => $userCampaign["cam_id"], "type" => "campaign", "label" => $userCampaign["cam_name"]);
	$type = $userCampaign["uri_right"];
	switch($userCampaign["uri_right"]) {
		case "listHead":
		case "candidate":
		case "substitute":
			$type = "candidate";
			break;
		case "representative":
			break;
		case "charteredAccountant":
			break;
	}

	// TODO upgrade label
	$parts[] = array("id" => $userCampaign["cam_id"], "type" => $type, "label" => "$type of " . $userCampaign["cam_name"]);
}

//print_r($parts);

$messages = $messageBo->getByFilters(array("froms" => $parts, "tos" => $parts));

//print_r($messages);

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_messaging"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("messaging_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>
	
	<div>
		<div id="left-part" class="col-md-3">
			<div class="panel panel-default" id="message-boxes">
				<div class="panel-heading">Boîtes</div>
				<ul class="list-group">
					<li class="list-group-item">Entrants</li>
					<li class="list-group-item">
						<ul class="list-group">
<?php		foreach($parts as $part) { ?>
							<li class="list-group-item message-box inbox" data-to-id="<?php echo $part["id"]; ?>" data-to-type="<?php echo $part["type"]; ?>"><?php echo $part["label"]; ?></li>
<?php		} ?>
						</ul>
					</li>
				</ul>
				<ul class="list-group">
					<li class="list-group-item">Sortants</li>
					<li class="list-group-item">
						<ul class="list-group">
<?php		foreach($parts as $part) { ?>
							<li class="list-group-item message-box outbox" data-from-id="<?php echo $part["id"]; ?>" data-from-type="<?php echo $part["type"]; ?>"><?php echo $part["label"]; ?></li>
<?php		} ?>
						</ul>
					</li>
				</ul>
			</div>			
		</div>
		<div id="main-part" class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button class="btn btn-success btn-xs btn-new-mail pull-right" title="Créer"><span class="glyphicon glyphicon-envelope"></span></button>
					Messages : <span class="message-box-label"></span></div>
				<ul class="list-group">
					<li class="list-group-item list-group-item-info" >
						<div class="clearfix">
							<div class="col-md-3"><?php echo lang("messaging_date"); ?></div>
							<div class="col-md-3 column-from">De</div>
							<div class="col-md-3 column-to" style="display: none;">&Agrave;</div>
							<div class="col-md-6"><?php echo lang("messaging_subject"); ?></div>
						</div>
					</li>
				</ul>
				<ul class="list-group" style=" max-height: 205px; overflow-y: scroll;" id="messages">
<?php		foreach($messages as $message) { ?>
					<li class="list-group-item message-header <?php echo ($message["mes_read"] == "1" ? "" : "not-read"); ?>" style="display: none;"
							data-id="<?php echo $message["mes_id"]; ?>"  
							data-code="<?php echo $message["mes_code"]; ?>"  
							data-from-id="<?php echo $message["mes_from_id"]; ?>"  data-from-type="<?php echo $message["mes_from_type"]; ?>"  
							data-to-id="<?php echo $message["mes_to_id"]; ?>"  data-to-type="<?php echo $message["mes_to_type"]; ?>">
						<div class="clearfix">
							<div class="col-md-3"><?php echo $message["mes_date"]; ?></div>
							<div class="col-md-3 column-from"><?php echo $message["mes_from_label"]; ?></div>
							<div class="col-md-3 column-to"><?php echo $message["mes_to_label"]; ?></div>
							<div class="col-md-6"><?php echo $message["mes_subject"]; ?></div>
						</div>
					</li>
<?php		} ?>
					<li class="list-group-item list-group-item-warning text-center" id="no-message">
						Pas de message
					</li>
				</ul>
			</div>
			
			<div class="clearfix"></div>

<?php		foreach($messages as $message) { ?>
			<div class="panel panel-default message" style="display: none;"
				data-id="<?php echo $message["mes_id"]; ?>"
				data-code="<?php echo $message["mes_code"]; ?>"  
				data-from-id="<?php echo $message["mes_from_id"]; ?>" data-from-type="<?php echo $message["mes_from_type"]; ?>" data-from-label="<?php echo $message["mes_from_label"]; ?>"  
				data-to-id="<?php echo $message["mes_to_id"]; ?>" data-to-type="<?php echo $message["mes_to_type"]; ?>" data-to-label="<?php echo $message["mes_to_label"]; ?>">
				<div class="panel-heading">
					<button class="btn btn-primary btn-xs btn-answer-mail pull-right" title="Répondre"><span class="glyphicon glyphicon-envelope"></span></button>
					<span class="subject"><?php echo $message["mes_subject"]; ?></span>
				</div>
				<div class="message-body" style="display: none;"><?php echo $message["mes_message"]; ?></div>			
				<div class="panel-body message-body-html"></div>			
			</div>
<?php		} ?>
			
		</div>
	</div>
	
	<?php
				include("dialogs/sendMail.php");
	?>
	
	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<div class="lastDiv"></div>

<script type="text/javascript">
</script>
<?php include("footer.php"); ?>
</body>
</html>
