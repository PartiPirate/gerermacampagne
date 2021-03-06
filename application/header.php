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
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/TaskBo.php");
require_once("engine/bo/UserBo.php");
require_once("engine/bo/AddressBo.php");
include_once("engine/utils/bootstrap_forms.php");
require_once("engine/utils/SessionUtils.php");

$page = $_SERVER["SCRIPT_NAME"];
if (strrpos($page, "/") !== false) {
	$page = substr($page, strrpos($page, "/") + 1);
}
$page = str_replace(".php", "", $page);

$user = SessionUtils::getUser($_SESSION);
$userId = SessionUtils::getUserId($_SESSION);
$language = SessionUtils::getLanguage($_SESSION);

if (isset($_SESSION["administrator"]) && $_SESSION["administrator"]) {
	$isAdministrator = true;
}

if ($page == "administration" && !$isAdministrator) {
	header('Location: index.php');
}

$connection = openConnection();

$ppBo = PoliticalPartyBo::newInstance($connection);
$campaignBo = CampaignBo::newInstance($connection);
$taskBo = TaskBo::newInstance($connection);

$administratedParties = array();
$campaign = null;
$userCampaigns = array();

if ($userId) {
	$administratedParties = $ppBo->getAdministratedParties($userId);

	$waitingAffiliations = 0;

	foreach($administratedParties as $administratedParty) {
		$waitingAffiliations += $administratedParty["ppa_number_of_waiting_affiliations"];
	}

	$userCampaigns = $campaignBo->getCampaigns(array("userId" => $userId, "withRights" => true));
	$campaign = $campaignBo->getCurrentCampaign($userId);

	$userBo = UserBo::newInstance($connection);
	$addressBo = AddressBo::newInstance($connection);

	$dbuser = $userBo->get($userId);
	$address = $addressBo->getById($dbuser["use_address_id"]);
}

?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo lang("handlemycampaign_title"); ?></title>

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
<link href="css/colorbox.css" rel="stylesheet"/>
<link href="css/jquery-ui.css" rel="stylesheet">

<link rel="shortcut icon" type="image/png" href="favicon.png" />
</head>
<body>
	<nav class="navbar navbar-inverse" role="navigation">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#otb-navbar-collapse">
					<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php"><img src="images/logo.svg" style="position: relative; top: -14px; width: 48px; height: 48px; background-color: #ffffff;"
					data-toggle="tooltip" data-placement="bottom"
					title="HandleMyCampaign" /> </a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="otb-navbar-collapse">
				<ul class="nav navbar-nav">
					<li <?php if ($page == "index") echo 'class="active"'; ?>><a href="index.php"><?php echo lang("menu_index"); ?><?php if ($page == "index") echo ' <span class="sr-only">(current)</span>'; ?></a></li>
					<li <?php if ($page == "documents") echo 'class="active"'; ?>><a href="documents.php"><?php echo lang("menu_documents"); ?><?php if ($page == "documents") echo ' <span class="sr-only">(current)</span>'; ?></a></li>
					<?php 	if ($campaign) {?>
					<li <?php if ($page == "books") echo 'class="active"'; ?>><a href="books.php"><?php echo lang("menu_books"); ?><?php if ($page == "books") echo ' <span class="sr-only">(current)</span>'; ?></a></li>
					<li <?php if ($page == "votingPaper") echo 'class="active"'; ?>><a href="votingPaper.php"><?php echo lang("menu_votingPaper"); ?><?php if ($page == "votingPaper") echo ' <span class="sr-only">(current)</span>'; ?></a></li>
					<?php 	}?>
					<?php 	if (count($administratedParties)) {?>
					<li id="affiliationsMenuItem" <?php if ($page == "affiliations") echo 'class="active"'; ?>><a href="affiliations.php"><?php echo lang("menu_affiliations"); ?> <span class="badge <?php if (!$waitingAffiliations) { echo "hidden"; } ?>"><?php echo $waitingAffiliations; ?></span>  <?php if ($page == "affiliations") echo ' <span class="sr-only">(current)</span>'; ?></a></li>
					<?php 	}?>
					<li <?php if ($page == "messaging") echo 'class="active"'; ?>><a href="messaging.php"><?php echo lang("menu_messaging"); ?><?php if ($page == "messaging") echo ' <span class="sr-only">(current)</span>'; ?></a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">

					<?php 	if (count($userCampaigns) > 1) {?>
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo $campaign["cam_name"]; ?> <span
							class="caret"></span> </a>
						<ul class="dropdown-menu" role="menu">
							<?php 	foreach($userCampaigns as $userCampaign) {?>
							<li><a href="do_changeCurrentCampaign.php?id=<?php echo$userCampaign["cam_id"]; ?>">
								<?php echo $userCampaign["cam_name"]; ?>
								<?php 	if ($userCampaign["cam_id"] == $campaign["cam_id"]) {?>
									<small><span class="glyphicon glyphicon-ok "></span></small>
								<?php 	}?></a>
							</li>
							<?php 	}?>
						</ul>
					</li>
					<?php 	}?>

					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo str_replace("{language}", lang("language_$language"), lang("menu_language")); ?> <span
							class="caret"></span> </a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="do_changeLanguage.php?lang=en"><span class="flag en" title="<?php echo lang("language_en"); ?>"></span> <?php echo lang("language_en"); ?></a></li>
							<li><a href="do_changeLanguage.php?lang=fr"><span class="flag fr" title="<?php echo lang("language_fr"); ?>"></span> <?php echo lang("language_fr"); ?></a></li>
						</ul>
					</li>

					<?php 	if ($user) {?>
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							<?php	if (!$address || !$address["add_entity"]) {
										?><span class="text-warning glyphicon glyphicon-warning-sign"></span><?php
									}
							?>
							<?php echo $user; ?> <span class="caret"></span> </a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="mycoordinates.php">
							<?php	if (!$address || !$address["add_entity"]) {
										?><span class="text-warning glyphicon glyphicon-warning-sign"></span><?php
									}
							?>
								<?php echo lang("menu_mycoordinates"); ?></a></li>
							<li><a href="mypreferences.php"><?php echo lang("menu_mypreferences"); ?></a></li>
							<li><a href="myparties.php"><?php echo lang("menu_myparties"); ?></a></li>
							<li><a href="mycampaigns.php"><?php echo lang("menu_mycampaigns"); ?></a></li>
							<li class="divider"></li>
							<li><a class="logoutLink" href="do_logout.php"><?php echo lang("menu_logout"); ?></a></li>
						</ul>
					</li>
					<li><a class="logoutLink" href="do_logout.php"><span class="glyphicon glyphicon-log-out"></span><span class="sr-only">Logout</span> </a></li>
					<?php 	} else { ?>
					<li><a id="loginLink" href="connect.php"><span class="glyphicon glyphicon-log-in"></span><span class="sr-only">Login</span> </a></li>
					<?php 	}?>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container otbHidden" id="loginForm">
		<form class="form-signin" role="form">
			<h2 class="form-signin-heading text-center"><?php echo lang("login_title"); ?></h2>
			<label for="inputLogin" class="sr-only"><?php echo lang("login_loginInput"); ?></label> <input type="text" id="loginInput" class="form-control" placeholder="<?php echo lang("login_loginInput"); ?>" required
				autofocus> <label for="inputPassword" class="sr-only"><?php echo lang("login_passwordInput"); ?></label> <input type="password" id="passwordInput" class="form-control"
				placeholder="<?php echo lang("login_passwordInput"); ?>" required>
			<br />
			<button id="loginButton" class="btn btn-lg btn-primary btn-block" type="submit">
				<?php echo lang("login_button"); ?> <span class="glyphicon glyphicon-log-in"></span>
			</button>
			<p class="text-center"><a href="register.php" class="colorInherit"><?php echo lang("register_link"); ?></a></p>
			<p class="text-center"><a href="forgotten.php" class="colorInherit"><?php echo lang("forgotten_link"); ?></a></p>
		</form>
	</div>

	<div class="container otbHidden">
		<?php echo addAlertDialog("error_login_banAlert", lang("error_login_ban"), "danger"); ?>
		<?php echo addAlertDialog("error_login_badAlert", lang("error_login_bad"), "warning"); ?>
	</div>
