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
session_start();
include_once("config/database.php");
include_once("language/language.php");
require_once("engine/bo/PoliticalPartyBo.php");
include_once("engine/utils/bootstrap_forms.php");
require_once("engine/utils/SessionUtils.php");

$page = $_SERVER["SCRIPT_NAME"];
if (strrpos($page, "/") !== false) {
	$page = substr($page, strrpos($page, "/") + 1);
}
$page = str_replace(".php", "", $page);

$language = SessionUtils::getLanguage($_SESSION);

?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo lang("volunteer_title"); ?></title>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<link href="css/style.css" rel="stylesheet">
<link href="css/flags.css" rel="stylesheet">
<link href="css/social.css" rel="stylesheet">
<link rel="shortcut icon" type="image/png" href="favicon.png" />
<style type="text/css">
body {
	overflow-y: scroll;
}

#volunteerVeil {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(255, 255, 255, 0.7);
}

#volunteerVeil img {
	margin-top: 200px;

	-webkit-animation: myfirst 2s;
	-webkit-animation-iteration-count: infinite;
	-webkit-animation-direction : alternate;
	-webkit-animation-play-state : running;
	animation: myfirst 2s;
	animation-iteration-count: infinite;
	animation-direction : alternate;
	animation-play-state : running;
}

legend a:after {
	font-family: 'Glyphicons Halflings';
	content: "\e114";
	float: right;
	color: grey;
}

legend a.collapsed:after {
	content: "\e080";
}

/* Chrome, Safari, Opera */
@-webkit-keyframes myfirst {
    0%   {opacity: 1;}
    100% {opacity: 0.3;}
}

/* Standard syntax */
@keyframes myfirst {
    0%   {opacity: 1;}
    100% {opacity: 0.3;}
}

</style>
</head>
<body>

	<div id="main" class="container theme-showcase" role="main">

		<div class="page-header">
			<h1 class="text-center">
				<a href="<?php echo lang("volunteer_website_url");?>"><img src="images/logo_pp.png" style="float: left;" /></a>
				<?php echo lang("volunteer_h1");?>
			</h1>
		</div>

		<br />

		<?php if (lang("volunteer_guide")) {?>
		<a href="<?php echo lang("volunteer_program_url"); ?>">
		<div class="well well-sm">
			<p>
				<?php echo lang("volunteer_guide"); ?>
			</p>
		</div>
		</a>
		<?php }?>

		<form id="volunteerForm" method="post" class="form-horizontal">

			<input type="name" name="email" class="otbHidden" value="" />

			<fieldset>
				<legend>
					<a href="#identityCollapse" data-toggle="collapse" data-target="#identityCollapse" aria-expanded="true" aria-controls="identityCollapse" data-parent="form">
						<?php echo lang("volunteer_identity_legend");?>
					</a>
				</legend>

				<div class="collapse in" id="identityCollapse">
					<!-- Language input-->
					<div class="form-group">
						<label class="col-md-3 control-label" for="sexInput"><?php echo lang("volunteer_sexInput"); ?> </label>
						<div class="col-md-8">
							<input id="sexInput" name="sexInput" value="" type="hidden">
							<div id="sexButtons" class="btn-group" role="group" aria-label="...">
								<button value="male" type="button" class="btn btn-default">
									<?php echo lang("sex_male"); ?>
								</button>
								<button value="female" type="button" class="btn btn-default">
									<?php echo lang("sex_female"); ?>
								</button>
							</div>
							<p class="help-block">
								<?php echo lang("volunteer_sexHelp");?>
							</p>
						</div>
					</div>

					<div class="form-group has-feedback">
						<label class="col-md-3 control-label" for="firstname"><?php echo lang("volunteer_identity");?> </label>
						<div class="col-md-4 has-feedback">
							<input id="firstname" name="firstname" type="text" class="form-control" placeholder="Prénom"> <span id="firstnameStatus"
								class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
						</div>
						<div class="col-md-4 has-feedback">
							<input id="lastname" name="lastname" type="text" class="form-control" placeholder="Nom"> <span id="lastnameStatus"
								class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
						</div>
					</div>

					<div class="form-group has-feedback">
						<label class="col-md-3 control-label" for="xxx"><?php echo lang("volunteer_mailInput");?> </label>
						<div class="col-md-4 has-feedback">
							<input id="xxx" name="xxx" type="text" class="form-control" placeholder="<?php echo lang("volunteer_mailPlaceholder"); ?>"> <span id="xxxStatus"
								class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
							<p class="help-block">
								<?php echo lang("volunteer_mailHelp");?>
							</p>
						</div>
						<div class="col-md-4 has-feedback">
							<input id="confirmationMail" name="confirmationMail" type="text" class="form-control"
								placeholder="<?php echo lang("volunteer_confirmationMailPlaceholder"); ?>"> <span id="confirmationMailStatus"
								class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
							<p class="help-block">
								<?php echo lang("volunteer_confirmationMailHelp");?>
							</p>
						</div>
					</div>

					<div class="form-group has-feedback">
						<label class="col-md-3 control-label" for="telephone"><?php echo lang("volunteer_telephoneInput");?> </label>
						<div class="col-md-4 has-feedback">
							<input id="telephone" name="telephone" type="text" class="form-control" placeholder="<?php echo lang("volunteer_telephonePlaceholder"); ?>">
						</div>
					</div>

					<div class="form-group has-feedback">
						<label class="col-md-3 control-label" for="line1"><?php echo lang("volunteer_addressInput");?> </label>
						<div class="col-md-8">
							<input id="line1" name="line1" type="text" class="form-control" placeholder="<?php echo lang("volunteer_line1Placeholder");?>">
						</div>
					</div>

					<div class="form-group has-feedback">
						<label class="col-md-3 control-label" for="line2"></label>
						<div class="col-md-8">
							<input id="line2" name="line2" type="text" class="form-control" placeholder="<?php echo lang("volunteer_line2Placeholder");?>">
						</div>
					</div>

					<div class="form-group has-feedback">
						<label class="col-md-3 control-label" for="zipCode"><?php echo lang("volunteer_cityInput");?> </label>
						<div class="col-md-3">
							<input id="zipCode" name="zipCode" type="text" class="form-control" placeholder="<?php echo lang("volunteer_zipCodePlaceholder");?>">
						</div>
						<div class="col-md-5">
							<input id="city" name="city" type="text" class="form-control" placeholder="<?php echo lang("volunteer_cityPlaceholder");?>">
						</div>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>
					<a href="#candidatureCollapse" data-toggle="collapse" data-target="#candidatureCollapse" aria-expanded="false" aria-controls="candidatureCollapse" data-parent="form">
						<?php echo lang("volunteer_candidature_legend");?>
					</a>
				</legend>

				<div class="collapse in" id="candidatureCollapse">

					<!-- Checkbox input-->
					<div class="form-group">
						<div class="col-md-3 control-label">
							<input id="authorize" name="authorize" value="1" type="checkbox"
								placeholder="" class="input-md" >
						</div>
						<div class="col-md-8 padding-left-0">
							<label class="form-control labelForCheckbox" for="authorize"><?php echo lang("volunter_authorize"); ?> </label>
						</div>
					</div>

					<!-- Language input-->
					<div class="form-group">
						<label class="col-md-3 control-label" for="candidateInput"><?php echo lang("volunteer_candidateInput"); ?> </label>
						<div class="col-md-8">
							<input id="candidateInput" name="candidateInput" value="" type="hidden">
							<div id="candidateButtons" class="btn-group" role="group" aria-label="...">
								<button value="candidate" type="button" class="btn btn-default">
									<?php echo lang("rights_candidate"); ?>
								</button>
								<button value="substitute" type="button" class="btn btn-default">
									<?php echo lang("rights_substitute"); ?>
								</button>
								<button value="representative" type="button" class="btn btn-default">
									<?php echo lang("rights_representative"); ?>
								</button>
							</div>
							<p class="help-block">
								<?php echo lang("volunteer_candidateHelp");?>
							</p>
						</div>
					</div>

					<div class="form-group has-feedback">
						<label class="col-md-3 control-label" for="line1"><?php echo lang("volunteer_circonscriptionsInput"); ?></label>
						<div class="col-md-8">
							<input id="circonscriptions" name="circonscriptions" type="text" class="form-control"
								placeholder="<?php echo lang("volunteer_circonscriptionsPlaceholder"); ?>">
							<p class="help-block">
								<?php echo lang("volunteer_circonscriptionsHelp");?>
							</p>
						</div>
					</div>

					<div class="form-group has-feedback photo-element otbHidden">
						<label class="col-md-3 control-label" for="checkFile"><?php echo lang("volunteer_bodyshotFile"); ?></label>
						<div class="col-md-8">
							<input id="bodyshotFile" name="bodyshotFile" value="" data-show-upload="false" type="file" placeholder=""
								class="form-control file input-md" data-show-preview="false">
							<p class="help-block">
								<?php echo lang("volunteer_bodyshotHelp"); ?>
							</p>
						</div>
					</div>

				</div>
			</fieldset>

			<fieldset>
				<!-- Button (Double) -->
				<div class="form-group">
					<label class="col-md-3 control-label" for="icandidateButton"></label>
					<div class="col-md-9">
						<button id="icandidateButton" name="icandidateButton" class="btn btn-success">
							<?php echo lang("volunteer_icandidateButton"); ?>
						</button>
						<button class="btn btn-warning ijoinButton">
							<?php echo lang("volunteer_ijoinButton"); ?>
						</button>
						<button class="btn btn-danger idonateButton">
							<?php echo lang("volunteer_idonateButton"); ?>
						</button>
					</div>
				</div>

			</fieldset>

		</form>

	</div>

	<div id="response" class="container theme-showcase otbHidden" role="main">

		<div class="page-header">
			<h1 class="text-center">
				<img src="images/logo_pp.png" style="float: left;" />
				<?php echo lang("volunteer_h1");?>
			</h1>
		</div>

		<br>
		<br>

		<div class="panel panel-success">
			<div class="panel-heading"><?php echo lang("volunteer_response_header");?></div>
			<div class="panel-body"><?php echo lang("volunteer_response_body");?></div>
		</div>

		<div class="text-center">
			<button class="btn btn-success ijoinButton">
				<?php echo lang("volunteer_ijoin2Button"); ?>
			</button>
			<button class="btn btn-success idonateButton">
				<?php echo lang("volunteer_idonate2Button"); ?>
			</button>
		</div>

	</div>

	<div id="volunteerVeil" class="text-center otbHidden">
		<img src="images/logo_pp.png" />
	</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="js/jquery-1.11.1.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/bootstrap.min.js"></script>
	<script src="js/moment-with-locales.js"></script>
	<script src="js/bootstrap-datetimepicker.js"></script>
	<script src="js/user.js"></script>
	<script src="js/window.js"></script>
	<script src="js/pagination.js"></script>
	<?php
if (is_file("js/perpage/" . $page . ".js")) {
	echo "<script src=\"js/perpage/" . $page . ".js\"></script>\n";
}
?>
	<script type="text/javascript">
	var donateUrl = "<?php echo lang("volunteer_idonateUrl"); ?>";
	var joinUrl = "<?php echo lang("volunteer_ijoinUrl"); ?>";
	</script>

</body>
</html>
