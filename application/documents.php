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
require_once("engine/bo/DocumentBo.php");

$parties = $administratedParties;
$documents = array();
$documentBo = DocumentBo::newInstance($connection);

if ($campaign) {

	$documents = $documentBo->getDocuments($campaign, array("sorts" => array(array("field" => "doc_id", "direction" => "DESC"))));
//	$partyDocuments = $documentBo->getPartyDocuments($campaign, array("sorts" => array(array("field" => "doc_id", "direction" => "DESC"))));

	if (false) {
		$parties[] = $campaign;
	}
}

foreach($parties as $key => $party) {
	$parties[$key]["documents"] = $documentBo->getPartyDocuments($party, array("sorts" => array(array("field" => "doc_id", "direction" => "DESC"))));
}

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_documents"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("documents_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

	<?php 		if ($campaign) {?>

	<div class="panel panel-default campaignDocumentsDiv" aria-campaign-id="<?php echo $campaign["cam_id"]; ?>">
		<div class="panel-heading">
			<h3 class="panel-title clearfix vertical-middle">
				<?php echo str_replace("{campaign}", $campaign["cam_name"], lang("documents_list_title")); ?>
				<button id="addCampaignDocumentButton" type="button" class="btn btn-default btn-xs pull-right">Ajouter un document</button>
			</h3>
		</div>

		<?php 	if (!count($documents)) {?>
		<div class="panel-body"><?php echo lang("documents_list_none"); ?></div>
		<?php 	} else { ?>

		<table class="table" aria-do-not-paginate="true">
			<thead>
				<tr>
					<th><?php echo lang("documents_list_name"); ?></th>
					<th style="width: 250px;"><?php echo lang("documents_list_task"); ?></th>
					<th style="width: 180px;"><?php echo lang("documents_list_type"); ?></th>
					<th style="width: 150px;"><?php echo lang("documents_list_actions"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php 	foreach($documents as $document) { ?>
				<tr>
					<td><?php echo $document["doc_name"]; ?></td>
					<td><?php if ($document["tas_label"]) { echo lang($document["tas_label"]); } ?></td>
					<td><?php 	if (isLanguageKey($document["doc_label"])) {
									echo lang($document["doc_label"]);
								}
								else  if (isLanguageKey("document_type_" . $document["doc_label"])) {
									echo lang("document_type_" . $document["doc_label"]);
								}
								else {
									echo $document["doc_label"];
								} ?></td>
					<td>
						<a href="<?php echo $document["doc_path"]; ?>" title="<?php echo $document["doc_name"]; ?>" class="<?php echo str_replace("/", "_", $document["doc_mime_type"]); ?>"><?php echo lang("documents_list_action_see"); ?></a>
						<a href="download.php?document=<?php echo str_replace("documents/", "", $document["doc_path"]); ?>"><?php echo lang("documents_list_action_download"); ?></a>
					</td>
				</tr>
			<?php 	}?>
			</tbody>
		</table>

		<?php 	}?>

	</div>

	<?php }?>

	<?php foreach($parties as $key => $party) { ?>
	<div class="panel panel-default partyDocumentsDiv" aria-party-id="<?php echo $party["ppa_id"]; ?>" >
		<div class="panel-heading">
			<h3 class="panel-title clearfix vertical-middle">
				<?php echo str_replace("{party}", $party["ppa_name"], lang("documents_party_list_title")); ?>
				<button type="button" class="btn btn-default btn-xs pull-right addPartyDocumentButton">Ajouter un document</button>
			</h3>
		</div>

		<?php 	if (!count($party["documents"])) {?>
		<div class="panel-body"><?php echo lang("documents_party_list_none"); ?></div>
		<?php 	} else { ?>

		<table class="table" aria-do-not-paginate="true">
			<thead>
				<tr>
					<th><?php echo lang("documents_list_name"); ?></th>
					<th style="width: 180px;"><?php echo lang("documents_list_type"); ?></th>
					<th style="width: 150px;"><?php echo lang("documents_list_actions"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php 	foreach($party["documents"] as $document) { ?>
				<tr>
					<td><?php 	echo $document["doc_name"]; ?></td>
					<td><?php 	if (isLanguageKey($document["doc_label"])) {
									echo lang($document["doc_label"]);
								}
								else  if (isLanguageKey("document_type_" . $document["doc_label"])) {
									echo lang("document_type_" . $document["doc_label"]);
								}
								else {
									echo $document["doc_label"];
								} ?></td>
					<td>
						<a href="<?php echo $document["doc_path"]; ?>" title="<?php echo $document["doc_name"]; ?>" class="<?php echo str_replace("/", "_", $document["doc_mime_type"]); ?>"><?php echo lang("documents_list_action_see"); ?></a>
						<a href="download.php?document=<?php echo str_replace("documents/", "", $document["doc_path"]); ?>"><?php echo lang("documents_list_action_download"); ?></a>
					</td>
				</tr>
			<?php 	}?>
			</tbody>
		</table>

		<?php 	}?>

	</div>

	<?php 		}?>

	<?php
				include("dialogs/addDocument.php");
	?>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<div class="lastDiv"></div>

<script type="text/javascript">
// colorbox internationalization

var current = "Image {current} sur {total}";
var previous = "Pr&eacute;c&eacute;dente";
var next = "Suivante";
var close = "Fermer";
var xhrError = "Le contenu n'a pas pu &ecirc;tre lu.";
var imgError = "L'image n'a pas pu &ecirc;tre lue.";
</script>
<?php include("footer.php");?>
</body>
</html>