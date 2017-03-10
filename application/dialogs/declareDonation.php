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
	<!-- Donation formular -->

	<div id="declareDonationDiv" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Déclarer un don</h4>
				</div>
				<form id="declareDonationForm" method="post" class="form-horizontal">

					<input type="hidden" name="campaignId" value="<?php echo $campaign["cam_id"]; ?>" />

					<div class="modal-body">
						<p>Renseignez les informations pour ce don</p>
					</div>

					<fieldset>
						<legend>Personne</legend>

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


					<fieldset>
						<legend>Don</legend>

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

						<div class="form-group has-feedback">
							<label class="col-md-3 control-label" for="label">Libellé</label>
							<div class="col-md-8">
								<input id="label" name="label" type="text" class="form-control" placeholder="Libellé du don" >
							</div>
						</div>

						<div class="form-group has-feedback" id="donationCodeDiv">
							<label class="col-md-3 control-label" for="donation-code">Imputation</label>
							<div class="col-md-8">
								<select id="donation-code" name="code" class="form-control">
									<option value="7010"><?php echo lang("code_7010"); ?></option>
									<option value="7021"><?php echo lang("code_7021"); ?></option>
									<option value="7022"><?php echo lang("code_7022"); ?></option>
									<option value="7023"><?php echo lang("code_7023"); ?></option>
									<option value="7025"><?php echo lang("code_7025"); ?></option>
									<option value="7031"><?php echo lang("code_7031"); ?></option>
								</select>
							</div>
						</div>

						<div class="form-group has-feedback" id="donationCodeDiv">
							<label class="col-md-3 control-label" for="paymentTypeButtons">Mode de règlement</label>
							<div class="col-md-8" id="paymentTypeDiv">
								<input type="hidden" name="paymentType" id="paymentType">
								<div id="paymentTypeButtons" class="btn-group" role="group">
									<button value="cash" type="button" class="btn btn-default"><?php echo lang("payment_type_cash"); ?></button>
									<button value="check" type="button" class="btn btn-default active"><?php echo lang("payment_type_check"); ?></button>
									<button value="cb" type="button" class="btn btn-default"><?php echo lang("payment_type_cb"); ?></button>
									<button value="transfer" type="button" class="btn btn-default"><?php echo lang("payment_type_transfer"); ?></button>
								</div>
							</div>
						</div>

						<div class="form-group has-feedback" id="checkFileDiv">
							<label class="col-md-3 control-label" for="checkFile">Chèque</label>
							<div class="col-md-8">
								<input id="checkFile" name="checkFile" value="" data-show-upload="false" type="file" placeholder=""
									class="form-control file input-md" data-show-preview="false">
								<span id="checkStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
							</div>
						</div>

					</fieldset>

				</form>

				<div class="modal-footer">
					<button id="closeButton" type="button" class="btn btn-default">Fermer</button>
					<button id="declareDonationButton" type="button" class="btn btn-primary">Ajouter</button>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>