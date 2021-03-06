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
require_once("engine/bo/BookInlineBo.php");

$documents = array();

if ($campaign) {
	$bookInlineBo = BookInlineBo::newInstance($connection);

	$inlines = $bookInlineBo->getInlines($campaign, array("sorts" => array(array("field" => "bin_transaction_date", "direction" => "DESC"))));

	$bin = $bout = $cin = $cout = 0;

	$onlyQuotations = array();
	$unpaidInvoices = array();

	foreach($inlines as $inline) {
// 		print_r($inline);
// 		echo "<br />\n";

		if ($inline["bin_type"] == "quotation") {
//			echo "Invoice <br />\n";

			$hasInvoice = false;
			foreach($inline["documents"] as $document) {
				if ($document["ido_type"] == "invoice") {
					$hasInvoice = true;
					break;
				}
			}

			if (!$hasInvoice) {
				$onlyQuotations[] = $inline;
			}
		}
		else if ($inline["bin_type"] == "invoice") {
			if (!$inline["payment"]["ipa_id"]) {
				$unpaidInvoices[] = $inline;
			}
		}

		if ($inline["bin_book"] == "ballot" && $inline["bin_column"] == "input") {
			$bin += $inline["bin_amount"];
		}
		else if ($inline["bin_book"] == "ballot" && $inline["bin_column"] == "output") {
			if ($inline["bin_type"] == "invoice") {
				$bout += $inline["bin_amount"];
			}
		}
		else if ($inline["bin_book"] == "campaign" && $inline["bin_column"] == "input") {
			$cin += $inline["bin_amount"];
		}
		else if ($inline["bin_book"] == "campaign" && $inline["bin_column"] == "output") {
			if ($inline["bin_type"] == "invoice") {
				$cout += $inline["bin_amount"];
			}		
		}
	}

	$bdiff = $bin - $bout;
	$cdiff = $cin - $cout;

	$hasCharteredAccountantSearchTask = $taskBo->getTaskByLabel($campaign, "search_chartered_accountant");
}

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_books"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("books_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

	<input id="campaignId" value="<?php echo $campaign["cam_id"]; ?>" type="hidden">

	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo lang("books_resumed_title"); ?></h3>
			</div>
			<form class="form-horizontal">
				<fieldset>
					<div class="form-group has-feedback input-sm margin-bottom-0">
						<label class="col-md-4 control-label"><?php echo lang("books_list_ballot_in"); ?></label>
						<div class="col-md-8">
							<p class="form-control-static <?php echo ($bdiff < 0) ? "text-danger" : "text-success"; ?>"><?php echo $bin; ?>&euro;</p>
						</div>
					</div>
					<div class="form-group has-feedback input-sm margin-bottom-0">
						<label class="col-md-4 control-label"><?php echo lang("books_list_ballot_out"); ?></label>
						<div class="col-md-8">
							<p class="form-control-static <?php echo ($bdiff < 0) ? "text-danger" : "text-success"; ?>"><?php echo $bout; ?>&euro;</p>
						</div>
					</div>
					<div class="form-group has-feedback input-sm margin-bottom-0">
						<label class="col-md-4 control-label"><?php echo lang("books_list_campaign_in"); ?></label>
						<div class="col-md-8">
							<p class="form-control-static <?php echo ($cdiff < 0) ? "text-danger" : "text-success"; ?>"><?php echo $cin; ?>&euro;</p>
						</div>
					</div>
					<div class="form-group has-feedback input-sm margin-bottom-0">
						<label class="col-md-4 control-label"><?php echo lang("books_list_campaign_out"); ?></label>
						<div class="col-md-8">
							<p class="form-control-static <?php echo ($cdiff < 0) ? "text-danger" : "text-success"; ?>"><?php echo $cout; ?>&euro;</p>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo lang("books_actions_title"); ?></h3>
			</div>

			<ul class="list-group">
				<li class="list-group-item" <?php if (!$hasCharteredAccountantSearchTask) {

					echo " data-toggle=\"tooltip\" data-placement=\"left\" title=\"".lang("books_action_donation_warning")."\" ";

				}?>>
					<a href="#declareDonationDiv" data-toggle="modal" data-target="#declareDonationDiv">Déclarer un don</a>
					<span class="badge"><a href="#declareDonationDiv" class="color-inherit" data-toggle="modal" data-target="#declareDonationDiv"><span class="glyphicon glyphicon-plus"></span></a></span>
				</li>
				<li class="list-group-item">
					<a href="#addQuotationDiv" data-toggle="modal" data-target="#addQuotationDiv">Ajouter un devis</a>
					<span class="badge"><a href="#addQuotationDiv" class="color-inherit" data-toggle="modal" data-target="#addQuotationDiv"><span class="glyphicon glyphicon-plus"></span></a></span>
				</li>
				<li class="list-group-item">
					<a href="#addInvoiceDiv" class="btn-add-invoice" data-toggle="modal" data-target="#addInvoiceDiv">Ajouter une facture</a>
					<span class="badge"><a href="#addInvoiceDiv" class="color-inherit btn-add-invoice" data-toggle="modal" data-target="#addInvoiceDiv"><span class="glyphicon glyphicon-plus"></span></a></span>
				</li>
			</ul>
		</div>
	</div>

	<div class="clearfix"></div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo str_replace("{campaign}", $campaign["cam_name"], lang("books_list_title")); ?></h3>
		</div>

		<?php 	if (!count($inlines)) {?>
		<div class="panel-body"><?php echo lang("books_list_none"); ?></div>
		<?php 	} else { ?>

		<table class="table" id="inline-table" aria-do-not-paginate="true">
			<thead>
				<tr>
					<th><?php echo lang("books_list_name"); ?></th>
					<th class="text-center" style="width: 110px;"><?php echo lang("books_list_documents"); ?></th>
					<th style="width: 110px;"><?php echo lang("books_list_date"); ?></th>
					<th class="text-center" style="width: 110px;"><?php echo lang("books_list_code"); ?></th>
					<th class="text-center" style="width: 120px;"><?php echo lang("books_list_ballot_in"); ?></th>
					<th class="text-center" style="width: 120px;"><?php echo lang("books_list_ballot_out"); ?></th>
					<th class="text-center" style="width: 120px;"><?php echo lang("books_list_campaign_in"); ?></th>
					<th class="text-center" style="width: 120px;"><?php echo lang("books_list_campaign_out"); ?></th>
					<th class="text-center" style="width: 110px;"><?php echo lang("books_list_actions"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php 	foreach($inlines as $inline) { ?>
				<tr class="inline" data-id="<?php echo $inline["bin_id"]; ?>" data-inline-code="<?php echo $inline["bin_secure_code"]; ?>">
					<td>
						<span class="inline-label" data-inline-id="<?php echo $inline["bin_id"]; ?>"><?php echo $inline["bin_label"]; ?></span>
						<?php //echo $inline["bin_type"]; ?>
					</td>
					<td>
						<?php foreach($inline["documents"] as $document) {
								if (!$document["ido_type"]) continue; ?>
							<span class="badge pull-right"><?php echo lang("document_type_" . $document["ido_type"]); ?></span>
							<span class="pull-right">&nbsp;</span>
						<?php }?>
					</td>
					<td><?php 	echo $inline["bin_transaction_date"]; ?></td>
					<td class="text-right"><acronym title="<?php
						if (isLanguageKey("code_" . $inline["bin_code"])) {
							$title = lang("code_" . $inline["bin_code"]);
						}
						else {
							$title = $inline["bin_code"];
						}
						
						echo $title;
					?>"><?php 	echo $inline["bin_code"]; ?></acronym></td>
					
					<?php 
					
						$amount = number_format($inline["bin_amount"], 2);
					
						$formatedAmount = "<span class=\"amount\" data-inline-id=\"" . $inline["bin_id"] . "\">";
						$formatedAmount .= $amount;
						$formatedAmount .= "</span>&euro;";
						
						if ($inline["bin_type"] == "quotation") {
							$formatedAmount = "(" . $formatedAmount . ")";
						}
					
					?>
					
					<td class="text-right"><?php if ($inline["bin_book"] == "ballot" && $inline["bin_column"] == "input") { echo $formatedAmount; }?></td>
					<td class="text-right"><?php if ($inline["bin_book"] == "ballot" && $inline["bin_column"] == "output") { echo $formatedAmount; }?></td>
					<td class="text-right"><?php if ($inline["bin_book"] == "campaign" && $inline["bin_column"] == "input") { echo $formatedAmount; }?></td>
					<td class="text-right"><?php if ($inline["bin_book"] == "campaign" && $inline["bin_column"] == "output") { echo $formatedAmount; }?></td>
					<td class="text-center">
						<!--
						<pre>
						<?php print_r($inline); ?>
						</pre>
						-->
						<?php	if ($inline["bin_type"] == "quotation") {?>
						<button class="btn btn-primary btn-xs btn-set-invoice" 
							data-inline-id="<?php echo $inline["bin_id"]; ?>" ><span class="glyphicon glyphicon-euro"></span> <?php echo lang("books_actions_set_invoice"); ?></button>
						<?php	} ?>
						<?php	if ($inline["bin_type"] == "invoice" && !$inline["payment"]["ipa_id"]) {?>
						<button class="btn btn-success btn-xs btn-pay-invoice" 
							data-inline-id="<?php echo $inline["bin_id"]; ?>" ><span class="glyphicon glyphicon-euro"></span> <?php echo lang("books_actions_pay"); ?></button>
						<?php	} ?>
						<button class="btn btn-danger btn-xs btn-remove-inline" 
							data-inline-id="<?php echo $inline["bin_id"]; ?>" ><span class="glyphicon glyphicon-remove"></span> <?php echo lang("books_actions_delete"); ?></button>
					</td>
				</tr>
			<?php 	}?>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="text-right"><?php echo number_format($bin, 2); ?>&euro;</td>
					<td class="text-right"><?php echo number_format($bout, 2); ?>&euro;</td>
					<td class="text-right"><?php echo number_format($cin, 2); ?>&euro;</td>
					<td class="text-right"><?php echo number_format($cout, 2); ?>&euro;</td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="text-center <?php echo ($bdiff < 0) ? "text-danger" : "text-success"; ?>" colspan="2"><strong><?php echo number_format($bdiff, 2); ?>&euro;</strong></td>
					<td class="text-center <?php echo ($cdiff < 0) ? "text-danger" : "text-success"; ?>" colspan="2"><strong><?php echo number_format($cdiff, 2); ?>&euro;</strong></td>
					<td></td>
				</tr>
			</tfoot>
		</table>

		<?php 		//echo addPagination(count($inlines), 5); ?>

		<?php 	}?>

	</div>

	<?php
				include("dialogs/removeInline.php");
				include("dialogs/declareDonation.php");
				include("dialogs/addQuotation.php");
				include("dialogs/addInvoice.php");
				include("dialogs/payInvoice.php");
	?>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<div class="lastDiv"></div>

<script type="text/javascript">
var userLanguage = '<?php echo SessionUtils::getLanguage($_SESSION); ?>';
</script>
<?php include("footer.php");?>
</body>
</html>