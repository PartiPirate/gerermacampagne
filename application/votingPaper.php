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
require_once("engine/bo/VotingPaperBo.php");

if ($campaign) {

//	print_r($campaign);

	$campaign["actors"] = $campaignBo->getRighters($campaign["cam_id"], array("listHead", "candidate", "substitute"));
	$campaign["listHeads"] = $campaignBo->getRighters($campaign["cam_id"], array("listHead"));
	$campaign["candidates"] = $campaignBo->getRighters($campaign["cam_id"], array("candidate"));
	$campaign["substitutes"] = $campaignBo->getRighters($campaign["cam_id"], array("substitute"));

	$documentBo = DocumentBo::newInstance($connection);
	$documents = $documentBo->getDocuments($campaign, array("like_doc_mime_type" => "%image%", "sorts" => array(array("field" => "doc_name", "direction" => "ASC"))));
	$partyDocuments = $documentBo->getPartyDocuments($campaign, array("like_doc_mime_type" => "%image%", "sorts" => array(array("field" => "doc_name", "direction" => "ASC"))));

//	echo "<br>";
//	print_r($documents);
//	echo "<br>";
//	print_r($partyDocuments);
//	echo "<br>";
}

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_votingPaper"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("votingPaper_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

	<form id="votingForm" class="form-horizontal" action="do_createVotingPaperPdf.php" target="downloadFrame">
		<fieldset>

			<input id="campaignId" name="campaignId" value="<?php echo $campaign["cam_id"]; ?>" type="hidden" />
			<input id="templateId" name="templateId" value="<?php echo $campaign["cam_campaign_template_id"]; ?>" type="hidden" />
			<input id="politicalPartyId" name="politicalPartyId" value="" type="hidden" />
			<input id="votingPaperType" name="type" value="campaign" type="hidden" />
			<input id="votingPaperId" name="votingPaperId" value="<?php echo $votingPaper["vpa_id"]; ?>" type="hidden" />
			<input id="votingPaperCode" name="votingPaperCode" type="hidden" />

			<!-- Form Name -->
			<legend><?php echo lang("votingPaper_form_legend"); ?></legend>

			<div class="form-group has-feedback" id="paperFormatDiv">
				<label class="col-md-3 control-label" for="quotationSelect">Format du papier</label>
				<div class="col-md-4">
					<select id="paperFormat" name="paperFormat" class="form-control">
						<option value="105x148" selected="selected">105 x 148 (A6)</option>
						<option value="210x148">210 x 148 (A5)</option>
						<option value="210x297">210 x 297 (A4)</option>
					</select>
				</div>
			</div>

			<div class="text-center">
<?php				foreach($administratedParties as $index => $party) { ?>
				<button type="button" class="btn btn-info btn-save-voting-paper-party" data-party-id="<?php echo $party["ppa_id"]; ?>">Sauver ce bulletin pour &laquo; <?php echo $party["ppa_name"]; ?> &raquo;</button>
<?php				}?>

				<button id="saveVotingPaperButton" type="button" class="btn btn-default">Sauver ce bulletin</button>
				
				
<?php				$foundCampaignParty = false;
					foreach($administratedParties as $index => $party) { 
						if ($campaign["cam_political_party_id"] == $party["ppa_id"]) $foundCampaignParty = true; ?>
				<button type="button" class="btn btn-info btn-load-voting-paper-party" data-party-id="<?php echo $party["ppa_id"]; ?>">Charger le bulletin pour &laquo; <?php echo $party["ppa_name"]; ?> &raquo;</button>
<?php				} ?>
<?php				if (!$foundCampaignParty && $campaign["cam_political_party_id"] && $campaign["cam_political_party_date"]  && $campaign["cam_political_party_date"] != "0000-00-00 00:00:00") { ?>
				<button type="button" class="btn btn-info btn-load-voting-paper-party" data-party-id="<?php echo $campaign["ppa_id"]; ?>">Charger le bulletin pour &laquo; <?php echo $campaign["ppa_name"]; ?> &raquo;</button>
<?php				} ?>

				<button id="loadVotingPaperButton" type="button" class="btn btn-default">Afficher le dernier bulletin sauvegardé</button>
				
				<button id="createVotingPaperPdfButton" type="button" class="btn btn-default">Créer un pdf</button>
			</div>

		</fieldset>
	</form>

	<br />

	<div class="btn-toolbar" role="toolbar" aria-label="...">

		<div id="basicTools" class="btn-group" role="group">
			<button id="selectionButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg3 wysiwyg-selection" title="Selection"></span></button>
			<button id="rectangleButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg3 wysiwyg-rectangle" title="Ajouter rectangle"></span></button>
			<button id="textButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg3 wysiwyg-text" title="Ajouter texte"></span></button>
		</div>

		<div id="layerTools" class="btn-group" role="group">
			<button id="bottomButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg wysiwyg-bottom" title="Mettre tout en dessous"></span></button>
			<button id="bottomerButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg wysiwyg-bottomer" title="Redescendre"></span></button>
			<button id="toperButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg wysiwyg-toper" title="Remonter"></span></button>
			<button id="topButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg wysiwyg-top" title="Mettre tout au dessus"></span></button>
		</div>
		<div id="multipleTools" class="btn-group" role="group">
			<button id="alignLeftButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg2 wysiwyg-toleft" title="Aligner à gauche"></span></button>
			<button id="alignCenterButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg2 wysiwyg-tocenter" title="Aligner au centre"></span></button>
			<button id="alignRightButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg2 wysiwyg-toright" title="Aligner à droite"></span></button>
			<button id="alignTopButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg2 wysiwyg-totop" title="Aligner en haut"></span></button>
			<button id="alignMiddleButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg2 wysiwyg-tomiddle" title="Aligner en milieu"></span></button>
			<button id="alignBottomButton" type="button" class="btn btn-default wysiwyg-btn"><span class="wysiwyg2 wysiwyg-tobottom" title="Aligner en bas"></span></button>
			<!--
			<button id="alignBetweenButton" type="button" class="btn btn-default">Aligner entre eux</button>
			 -->
		</div>
		<div id="textTools" class="otbHidden btn-group" role="group">
			<input id="textInput" style="padding: 6px 12px 7px; text-align: left;" class="btn btn-default" />
			<select id="fontFamilyInput" type="button" style="padding: 7px 12px 8px;" class="btn btn-default">
				<option value="ubuntu">Ubuntu</option>
			</select>
			<select id="fontSizeInput" type="button" style="padding: 7px 12px 8px;" class="btn btn-default">
				<option value="8px">8px</option>
				<option value="1Opx">10px</option>
				<option value="12px">12px</option>
				<option value="14px">14px</option>
				<option value="16px">16px</option>
				<option value="18px">18px</option>
				<option value="20px">20px</option>
				<option value="24px">24px</option>
				<option value="28px">28px</option>
				<option value="32px">32px</option>
			</select>
		</div>

		<div id="colorTools" class="btn-group" role="group">
			<div id="foregroundColor" class="btn btn-default grayPicker">
				<span class="color" style="display: inline-block; width: 16px; height: 16px; background-color: #7F7F7F; border: 1px solid black; position: relative; top: 3px;"></span>
				<span class="caret"></span>
			</div>
			<div id="backgroundColor" class="btn btn-default grayPicker">
				<span class="color" style="display: inline-block; width: 16px; height: 16px; background-color: #7F7F7F; border: 1px solid black; position: relative; top: 3px;"></span>
				<span class="caret"></span>
			</div>
		</div>

		<div id="grayPickerContainer" class="btn btn-default otbHidden" style="z-index: 1000;">
			<div id="grayPicker"></div>
			<input id="colorTarget" type="hidden" />
		</div>

	</div>

	<br />

	<div class="clearfix">

		<div id="itemDiv" style="float: right; border: grey solid 1px; width: 300px;">

			Informations :<br/>
			<ul style="padding-left: 5px;">
			<?php 	if ($campaign["ppa_name"]) {?>
				<li style="list-style-type: none;"><span class="votingPaper-text" data-text="politicalParty"><?php echo $campaign["ppa_name"]; ?></span></li>
			<?php 	}?>
				<li style="list-style-type: none;"><span class="votingPaper-text" data-text="campaignName"><?php echo $campaign["cam_name"]; ?></span></li>
				<li style="list-style-type: none;"><span class="votingPaper-text" data-text="electoralDistrict">Circonscription <?php echo $campaign["cam_electoral_district"]; ?></span></li>
			<?php 	if ($campaign["cam_start_date"] != "0000-00-00") {?>
				<li style="list-style-type: none;"><span class="votingPaper-text" data-text="firstTurn"><?php echo $campaign["cam_start_date"]; ?></span></li>
			<?php 	}?>
			<?php 	if ($campaign["cam_finish_date"] != "0000-00-00") {?>
				<li style="list-style-type: none;"><span class="votingPaper-text" data-text="secondTurn"><?php echo $campaign["cam_finish_date"]; ?></span></li>
			<?php 	}?>
			</ul>

			<?php 	if (count($campaign["listHeads"])) {?>
			Têtes de liste :<br/>
			<ul style="padding-left: 5px;">
			<?php 		foreach($campaign["listHeads"] as $index => $actor) {	?>
				<li style="list-style-type: none;"><span class="votingPaper-text" data-person="listHead" data-position="<?php echo $index?>" ><?php echo $actor["add_entity"]; ?></span></li>
			<?php 		}?>
			</ul>
			<?php 	}?>

			<?php 	if (count($campaign["candidates"])) {?>
			Candidat-e-s :<br/>
			<ul style="padding-left: 5px;">
			<?php 		foreach($campaign["candidates"] as $index => $actor) {	?>
				<li style="list-style-type: none;"><span class="votingPaper-text" data-person="candidate" data-position="<?php echo $index?>" ><?php echo $actor["add_entity"]; ?></span></li>
			<?php 		}?>
			</ul>
			<?php 	}?>

			<?php 	if (count($campaign["substitutes"])) {?>
			Suppléant-e-s :<br/>
			<ul style="padding-left: 5px;">
			<?php 		foreach($campaign["substitutes"] as $index => $actor) {	?>
				<li style="list-style-type: none;"><span class="votingPaper-text" data-person="substitute" data-position="<?php echo $index?>" ><?php echo $actor["add_entity"]; ?></span></li>
			<?php 		}?>
			</ul>
			<?php 	}?>

			Image : <select id="imageSelect" style="width: 100%;">
				<option value=""></option>
			<?php 	if (count($documents)) {	?>
				<optgroup label="Vos images">
			<?php 		foreach($documents as $document) {	?>
					<option value="<?php echo $document["doc_path"]?>"><?php
//						echo $document["doc_name"];

						if (isLanguageKey($document["doc_label"])) {
							echo lang($document["doc_label"]);
						}
						else  if (isLanguageKey("document_type_" . $document["doc_label"])) {
							echo lang("document_type_" . $document["doc_label"]);
						}
						else {
							echo $document["doc_label"];
						}

						?></option>
			<?php 		}	?>
				</optgroup>
			<?php	}?>

			<?php 	if (count($partyDocuments)) {	?>
				<optgroup label="Les images de votre parti">
			<?php 	foreach($partyDocuments as $document) {	?>
					<option value="<?php echo $document["doc_path"]?>"><?php

					if (isLanguageKey($document["doc_label"])) {
						echo lang($document["doc_label"]);
					}
					else  if (isLanguageKey("document_type_" . $document["doc_label"])) {
						echo lang("document_type_" . $document["doc_label"]);
					}
					else {
						echo $document["doc_label"];
					}

					?></option>
			<?php 		}	?>
				</optgroup>
			<?php	}?>

			</select>
			<br/>
			<ul style="padding-left: 5px;" id="imageUl">
				<li style="list-style-type: none;"><img class="votingPaper-img" src="" style="max-width: 250px; max-height: 100px;" /></li>
			</ul>

			<div id="imageTools" class="otbHidden">
				<hr/>
				Largeur : <input id="widthInput"><br />
				Hauteur : <input id="heightInput"><br />
				<button id="initialSizeButton" type="button" class="btn btn-default">Remettre à la taille initial</button>
			</div>
		</div>
		<div id="votingPaper" style="position: relative; box-sizing: content-box; display: inline-block; border-width: 2px; border-color: grey; border-style: inset; width: 148mm; height: 105mm;">
		</div>

	</div>

	<br />

	<div class="well well-sm">
		<p>
			<ul>
				<li>
			Taille des bulletins :
				<a href="http://www.legifrance.gouv.fr/affichCodeArticle.do?cidTexte=LEGITEXT000006070239&idArticle=LEGIARTI000006354476&dateTexte=&categorieLien=cid">http://www.legifrance.gouv.fr/affichCodeArticle.do?cidTexte=LEGITEXT000006070239&idArticle=LEGIARTI000006354476&dateTexte=&categorieLien=cid</a>
				</li>
				<li>
			Ordre alphatique des noms en cas de binôme :
				<a href="http://www.legifrance.gouv.fr/affichCodeArticle.do?cidTexte=LEGITEXT000006070239&idArticle=LEGIARTI000006353447&dateTexte=&categorieLien=cid">http://www.legifrance.gouv.fr/affichCodeArticle.do?cidTexte=LEGITEXT000006070239&idArticle=LEGIARTI000006353447&dateTexte=&categorieLien=cid</a>
				</li>
			</ul>
		</p>
	</div>


	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<iframe id="downloadFrame" name="downloadFrame" class="otbHidden"></iframe>
<div class="lastDiv"></div>

<script type="text/javascript">
</script>
<?php include("footer.php");?>
</body>
</html>