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
	<!-- Pay Invoice formular -->

	<div id="payInvoiceDiv" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Signaler un paiment de facture</h4>
				</div>
				<form id="payInvoiceForm" method="post" class="form-horizontal">

					<input type="hidden" name="campaignId" value="<?php echo $campaign["cam_id"]; ?>" />

					<div class="modal-body">
						<p>Renseignez les informations pour le règlement d'une facture</p>
					</div>

					<fieldset id="invoiceFieldset">
						<legend>Règlement</legend>

						<div class="form-group has-feedback" id="invoiceSelectDiv">
							<label class="col-md-3 control-label" for="invoiceSelect">Facture</label>
							<div class="col-md-4">
								<select id="invoiceSelect" name="invoiceSelect" class="form-control">
									<option value="" aria-amount="" data-date=""></option>
<?php 	foreach($unpaidInvoices as $invoice) {?>
									<option value="<?php echo $invoice["bin_id"]; ?>" aria-amount="<?php echo $invoice["bin_amount"]; ?>" data-date="<?php echo $invoice["bin_transaction_date"]; ?>"><?php echo $invoice["bin_label"]; ?></option>
<?php 	}?>
								</select>
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="amount">Montant et date</label>
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon" id="euro-addon">&euro;</span>
									<input id="amount" name="amount" type="text" class="form-control" placeholder="" aria-describedby="euro-addon">
									<span id="amountStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
								</div>
							</div>
							<div class="col-md-4">
				                <div class='input-group date'>
				                    <input id='inlineDate' name="inlineDate" type='text' class="form-control" placeholder="YYYY-MM-DD"
				                    	data-date-format="YYYY-MM-DD"/>
				                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				                </div>
					        </div>
						</div>

						<div class="form-group has-feedback" id="invoiceCodeDiv">
							<label class="col-md-3 control-label" for="code">Imputation</label>
							<div class="col-md-8">
								<select id="payment_type" name="payment_type" class="form-control">
									<option value="DA"><?php echo lang("code_DA"); ?></option>
									<option value="DB"><?php echo lang("code_DB"); ?></option>
									<option value="DC"><?php echo lang("code_DC"); ?></option>
								</select>
							</div>
						</div>

<!--
						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="checkFile">Facture</label>
							<div class="col-md-8">
								<input id="invoiceFile" name="invoiceFile" value="" data-show-upload="false" type="file" placeholder=""
									class="form-control file input-md" data-show-preview="false">
								<span id="checkStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
							</div>
						</div>
-->
					</fieldset>

				</form>

				<div class="modal-footer">
					<button id="closeButton" type="button" class="btn btn-default">Fermer</button>
					<button id="payInvoiceButton" type="button" class="btn btn-primary">Payer</button>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>