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
	<!-- Invoice formular -->

	<div id="addInvoiceDiv" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Ajouter une facture</h4>
				</div>
				<form id="addInvoiceForm" method="post" class="form-horizontal">

					<input type="hidden" name="campaignId" value="<?php echo $campaign["cam_id"]; ?>" />

<?php
					if (count($onlyQuotations)) {?>
					<fieldset>
						<!-- Multiple Radios -->
						<div class="form-group">
							<input type="hidden" id="invoiceSource" name="invoiceSource" />
							<label class="col-md-3 control-label" for="invoiceSourceRadios"></label>
							<div class="col-md-8">
								<label class="radio-inline" for="directInvoiceRadio"><input type="radio" name="invoiceSourceRadios" id="directInvoiceRadio" value="directInvoice">Facture directe</label>
								<label class="radio-inline" for="fromQuotationRadio"><input type="radio" name="invoiceSourceRadios" id="fromQuotationRadio" value="fromQuotation">&Agrave; partir d'un devis</label>
							</div>
						</div>
					</fieldset>
<?php 				}
					else {	?>
					<input type="hidden" id="invoiceSource" name="invoiceSource" value="directInvoice" />
					<input type="hidden" name="invoiceSourceRadios" id="directInvoiceRadio" value="directInvoice">
<?php 				}	?>

					<div class="modal-body">
						<p>Renseignez les informations pour cette facture</p>
					</div>

					<fieldset id="invoiceProviderFieldset">
						<legend>Fournisseur</legend>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="firstname">Identité</label>
							<div class="col-md-4">
								<input id="firstname" name="firstname" type="text" class="form-control" placeholder="Prénom" >
								<span id="amountStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
							</div>
							<div class="col-md-4">
								<input id="lastname" name="lastname" type="text" class="form-control" placeholder="Nom" >
								<span id="amountStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="line1">Adresse</label>
							<div class="col-md-8">
								<input id="line1" name="line1" type="text" class="form-control" placeholder="Première ligne..." >
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="line2"></label>
							<div class="col-md-8">
								<input id="line2" name="line2" type="text" class="form-control" placeholder="Deuxième ligne..." >
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="zipCode">Ville</label>
							<div class="col-md-3">
								<input id="zipCode" name="zipCode" type="text" class="form-control" placeholder="Code postal" >
							</div>
							<div class="col-md-5">
								<input id="city" name="city" type="text" class="form-control" placeholder="Ville" >
							</div>
						</div>

					</fieldset>


					<fieldset id="invoiceFieldset">
						<legend>Facture</legend>

						<div class="form-group has-feedback" id="quotationSelectDiv">
							<label class="col-md-3 control-label" for="quotationSelect">Devis</label>
							<div class="col-md-4">
								<select id="quotationSelect" name="quotationSelect" class="form-control">
									<option value="" aria-amount="" data-date=""></option>
<?php 	foreach($onlyQuotations as $quotation) {?>
									<option value="<?php echo $quotation["bin_id"]; ?>" aria-amount="<?php echo $quotation["bin_amount"]; ?>" data-date="<?php echo $quotation["bin_transaction_date"]; ?>"><?php echo $quotation["bin_label"]; ?></option>
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

						<div class="form-group has-feedback" id="invoiceLabelDiv">
							<label class="col-md-3 control-label" for="label">Libellé</label>
							<div class="col-md-8">
								<input id="label" name="label" type="text" class="form-control" placeholder="Libellé de la transaction" >
							</div>
						</div>

						<div class="form-group has-feedback" id="invoiceCodeDiv">
							<label class="col-md-3 control-label" for="code">Imputation</label>
							<div class="col-md-8">
								<select id="code" name="code" class="form-control">
									<option value="6051"><?php echo lang("code_6051"); ?></option>
									<option value="6060"><?php echo lang("code_6060"); ?></option>
									<option value="6132"><?php echo lang("code_6132"); ?></option>
									<option value="6400"><?php echo lang("code_6400"); ?></option>
									<option value="6210"><?php echo lang("code_6210"); ?></option>
									<option value="6211"><?php echo lang("code_6211"); ?></option>
									<option value="6226"><?php echo lang("code_6226"); ?></option>
									<option value="6229"><?php echo lang("code_6229"); ?></option>
									<option value="6230"><?php echo lang("code_6230"); ?></option>
									<option value="6237"><?php echo lang("code_6237"); ?></option>
									<option value="6235"><?php echo lang("code_6235"); ?></option>
									<option value="6240"><?php echo lang("code_6240"); ?></option>
									<option value="6254"><?php echo lang("code_6254"); ?></option>
									<option value="6257"><?php echo lang("code_6257"); ?></option>
									<option value="6260"><?php echo lang("code_6260"); ?></option>
									<option value="6262"><?php echo lang("code_6262"); ?></option>
									<option value="6280"><?php echo lang("code_6280"); ?></option>
									<option value="6600"><?php echo lang("code_6600"); ?></option>
								</select>
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="checkFile">Facture</label>
							<div class="col-md-8">
								<input id="invoiceFile" name="invoiceFile" value="" data-show-upload="false" type="file" placeholder=""
									class="form-control file input-md" data-show-preview="false">
								<span id="checkStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
							</div>
						</div>

					</fieldset>

				</form>

				<div class="modal-footer">
					<button id="closeButton" type="button" class="btn btn-default">Fermer</button>
					<button id="addInvoiceButton" type="button" class="btn btn-primary">Ajouter</button>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>