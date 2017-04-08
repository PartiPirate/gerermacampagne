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
require_once("engine/bo/AddressBo.php");
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");

$userBo = UserBo::newInstance($connection);
$addressBo = AddressBo::newInstance($connection);
$dbuser = $userBo->get(SessionUtils::getUserId($_SESSION));

// print_r($dbuser);

$address = $addressBo->getById($dbuser["use_address_id"]);

// print_r($address);

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<?php 	if ($user) {?>
		<li><a href="mypage.php"><?php echo $user; ?></a></li>
		<?php 	}?>
		<li class="active"><?php echo lang("breadcrumb_mycoordinates"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("mycoordinates_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

	<form class="form-horizontal" id="updateActorForm">
		<fieldset>
			<legend>Identité</legend>

			<div class="form-group has-feedback company-name-group" style="display: none;">
				<label class="col-md-3 control-label" for="companyName">Raison social</label>
				<div class="col-md-8">
					<input id="companyName" name="companyName" type="text" class="form-control" placeholder="Raison sociale" >
				</div>
			</div>

			<div class="form-group has-feedback">
				<label class="col-md-3 control-label" for="firstname">Identité</label>
				<div class="col-md-8">
					<input id="entity" name="entity" type="text" class="form-control" placeholder="Identité" value="<?php echo @$address["add_entity"]; ?>" >
				</div>
<!--
				<div class="col-md-4">
					<input id="firstname" name="firstname" type="text" class="form-control" placeholder="Prénom" >
				</div>
				<div class="col-md-4">
					<input id="lastname" name="lastname" type="text" class="form-control" placeholder="Nom" >
				</div>
-->				
			</div>

		</fieldset>

		<fieldset>
			<legend>Adresse</legend>

			<div class="form-group has-feedback">
				<label class="col-md-3 control-label" for="line1">Adresse</label>
				<div class="col-md-8">
					<input id="line1" name="line1" type="text" class="form-control" placeholder="Première ligne..." value="<?php echo @$address["add_line_1"]; ?>" >
				</div>
			</div>

			<div class="form-group has-feedback">
				<label class="col-md-3 control-label" for="line2"></label>
				<div class="col-md-8">
					<input id="line2" name="line2" type="text" class="form-control" placeholder="Deuxième ligne..." value="<?php echo @$address["add_line_2"]; ?>" >
				</div>
			</div>

			<div class="form-group has-feedback">
				<label class="col-md-3 control-label" for="zipCode">Ville</label>
				<div class="col-md-3">
					<input id="zipCode" name="zipCode" type="text" class="form-control" placeholder="Code postal" value="<?php echo @$address["add_zip_code"]; ?>" >
				</div>
				<div class="col-md-5">
					<input id="city" name="city" type="text" class="form-control" placeholder="Ville" value="<?php echo @$address["add_city"]; ?>" >
				</div>
			</div>
			
			<!-- TODO -->
			<input id="countryId" name="countryId" type="hidden" class="form-control" placeholder="Identifiant Pays" value="<?php echo @$address["add_country_id"]; ?>" >

			<input id="userId" name="userId" type="hidden" value="<?php echo @$dbuser["use_id"]; ?>" >
			<input id="userCode" name="userCode" type="hidden" value="<?php echo @$dbuser["use_code"]; ?>" >
			<input id="addressId" name="addressId" type="hidden" value="<?php echo @$address["add_id"]; ?>" >
			<input id="addressCode" name="addressCode" type="hidden" value="<?php echo @$address["add_code"]; ?>" >

			<!-- Button (Double) -->
			<div class="form-group">
				<label class="col-md-4 control-label" for="saveCoordinatesButton"></label>
				<div class="col-md-8">
					<button id="saveCoordinatesButton" name="saveCoordinatesButton" class="btn btn-default"><?php echo lang("mycoordinates_save"); ?></button>
				</div>
			</div>
		</fieldset>
	</form>

	<?php echo addAlertDialog("error_cant_change_informationAlert", lang("error_cant_change_information"), "danger"); ?>
	<?php echo addAlertDialog("ok_operation_successAlert", lang("ok_operation_success"), "success"); ?>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<script type="text/javascript">
var userLanguage = '<?php echo SessionUtils::getLanguage($_SESSION); ?>';

</script>
<?php include("footer.php");?>
</body>
</html>