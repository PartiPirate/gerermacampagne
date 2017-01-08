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
$page = "activate";
include_once("header.php");
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");

$userBo = UserBo::newInstance(openConnection());

$activationStatus = true;
$mail = "";
$code = "";

if (!isset($_REQUEST["mail"])) {
	$activationStatus = false;
}
else {
	$mail = $_REQUEST["mail"];
}

if (!isset($_REQUEST["code"])) {
	$activationStatus = false;
}
else {
	$code = $_REQUEST["code"];
}

if ($activationStatus) {
//	$activationStatus = $userBo->activate($mail, $code);

	$user = $userBo->getUserByMail($mail);
	if ($user) {
		$activationStatus = ($user["use_activation_key"] == $code);
	}
	else {
		$activationStatus = false;
	}
}

$activation = "default";
if ($activationStatus) {
	$activation = "success";
}
else {
	$activation = "danger";
}


?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?> </a></li>
		<li class="active"><?php echo lang("breadcrumb_activation"); ?></li>
	</ol>

	<div class="well well-sm">
		<p>
			<?php echo lang("activation_guide"); ?>
		</p>
	</div>

	<div id="panel-<?php echo $activation; ?>" class="panel panel-<?php echo $activation; ?>">
		<div class="panel-heading">
			<?php echo lang("activation_title"); ?>
		</div>
		<div class="panel-body"><?php echo lang("activation_information_" . $activation); ?></div>
	</div>

<?php
	if ($activation == "success") {?>

	<div id="panel-final" class="panel panel-success" style="display: none;">
		<div class="panel-heading">
			<?php echo lang("activation_title"); ?>
		</div>
		<div class="panel-body"><?php echo lang("activation_information_final"); ?></div>
	</div>

	<div id="panel-danger" class="panel panel-danger" style="display: none;">
		<div class="panel-heading">
			<?php echo lang("activation_title"); ?>
		</div>
		<div class="panel-body"><?php echo lang("activation_information_danger"); ?></div>
	</div>
	
	<form id="formPanel" class="form-horizontal" action="do_activate.php" method="post">
		<fieldset>

			<input id="code" name="code" value="<?php echo $code; ?>" type="text" class="mailForm"/>
			<input id="mail" name="mail" value="<?php echo $mail; ?>" type="text" class="mailForm" />

			<!-- Form Name -->
			<legend><?php echo lang("activation_form_legend"); ?></legend>


			<!-- Text input-->
			<div class="form-group has-feedback">
				<label class="col-md-4 control-label" for="userLoginInput"><?php echo lang("connect_form_loginInput"); ?></label>
				<div class="col-md-6">
					<input id="userLoginInput" name="login" value="<?php echo $user["use_login"]; ?>" type="text" 
						placeholder="" class="form-control input-md">
					<span id="userLoginStatus"
						class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
					<p id="userLoginHelp" class="help-block otbHidden"></p>
				</div>
			</div>

			<!-- Password input-->
			<div class="form-group has-feedback">
				<label class="col-md-4 control-label" for="userPasswordInput"><?php echo lang("connect_form_passwordInput"); ?></label>
				<div class="col-md-6">
					<input id="userPasswordInput" name="password" value="" type="password"
						placeholder="" class="form-control input-md">
					<span id="passwordStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
				</div>
			</div>


			<!-- Password input-->
			<div class="form-group has-feedback">
				<label class="col-md-4 control-label" for="userConfirmationInput"><?php echo lang("connect_form_confirmationInput"); ?></label>
				<div class="col-md-6">
					<input id="userConfirmationInput" name="confirmation" value="" type="password"
						placeholder="" class="form-control input-md">
					<span id="confirmationStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
				</div>
			</div>

			<!-- Button (Double) -->
			<div class="form-group">
				<div class="col-md-12 text-center">
					<button id="btn-activate" type="submit" class="btn btn-primary" disabled="disabled"><?php echo lang("common_save"); ?></button>
				</div>
			</div>
		</fieldset>
	</form>
<?php
	} ?>
</div>

<div class="lastDiv"></div>

<?php include("footer.php");?>
<script>
</script>
</body>
</html>
