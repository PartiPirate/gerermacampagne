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
require_once("engine/bo/AddressBo.php");
include_once("engine/utils/bootstrap_forms.php");
require_once("engine/utils/SessionUtils.php");

header("Access-Control-Allow-Origin: *");
// GET ONLY
header('Access-Control-Allow-Methods: GET');
//header('Access-Control-Allow-Methods: GET, POST');

$connection = openConnection();
$addressBo = AddressBo::newInstance($connection);

$page = $_SERVER["SCRIPT_NAME"];
if (strrpos($page, "/") !== false) {
	$page = substr($page, strrpos($page, "/") + 1);
}
$page = str_replace(".php", "", $page);

$language = SessionUtils::getLanguage($_SESSION);

if (isset($_REQUEST["departments"])) {
	$departments = $_REQUEST["departments"];
}
else {
	$departments = array();
}

$stats = $addressBo->getCandidatureStats($departments);

if (isset($_REQUEST["json"])) {
	header('content-type: application/json; charset=utf-8');
	echo json_encode($stats);
	exit();
}

?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo lang("volunteer_widget_title"); ?></title>

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

table thead tr th {
	padding: 0 3px 0 3px;
}

.text-strong {
	font-weight: bold;
}

.text-em {
	font-style: italic;
}
</style>
</head>
<body>

	<table aria-do-not-paginate="true">
		<thead>
			<tr>
				<th><?php echo lang("volunteer_department"); ?></th>
				<th><?php echo lang("volunteer_circonscription"); ?></th>
				<th><?php echo lang("rights_candidate"); ?></th>
				<th><?php echo lang("rights_substitute"); ?></th>
				<th><?php echo lang("rights_representative"); ?></th>
				<th><?php echo lang("volunteer_total"); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
					$total = array("candidate" => 0, "substitute" => 0, "representative" => 0, null => 0);
					$mtotal = array("candidate" => 0, "substitute" => 0, "representative" => 0, null => 0);

					foreach($stats as $department => $circonscriptions) {
						foreach($circonscriptions as $circonscription => $positions) {

							if (!$department) continue;

							$extraClass = "";

							if ($circonscription) {
								$total["candidate"] += @$positions["candidate"];
								$total["substitute"] += @$positions["substitute"];
								$total["representative"] += @$positions["representative"];
								$total[null] += @$positions[null];

								$mtotal["candidate"] += @$positions["candidate"];
								$mtotal["substitute"] += @$positions["substitute"];
								$mtotal["representative"] += @$positions["representative"];
								$mtotal[null] += @$positions[null];
							}
							else {
								$extraClass .= " text-em";
								$positions = $total;
							}
?>
			<tr>
				<td class="text-right"><?php echo $department; ?></td>
				<?php if ($circonscription) {?>
				<td class="text-right"><?php echo $circonscription; ?></td>
				<?php } else {?>
				<td class="text-right text-strong total"><?php echo lang("volunteer_total"); ?></td>
				<?php }?>
				<td class="text-right <?php echo $extraClass; ?>"><?php echo @$positions["candidate"] ? @number_format($positions["candidate"], 3) : ""; ?></td>
				<td class="text-right <?php echo $extraClass; ?>"><?php echo @$positions["substitute"] ? @number_format($positions["substitute"], 3) : ""; ?></td>
				<td class="text-right <?php echo $extraClass; ?>"><?php echo @$positions["representative"] ? @number_format($positions["representative"], 3) : ""; ?></td>
				<td class="text-right text-em"><?php echo @$positions[null] ? @number_format($positions[null], 3) : ""; ?></td>
			</tr>

			<?php
							if (!$circonscription) {
								$total = array("candidate" => 0, "substitute" => 0, "representative" => 0, null => 0);
							}
						}
					}?>

			<tr class="total">
				<td class="text-right"></td>
				<td class="text-right text-strong"><?php echo lang("volunteer_total"); ?></td>
				<td class="text-right text-em"><?php echo @$mtotal["candidate"] ? @number_format($mtotal["candidate"], 1) : ""; ?></td>
				<td class="text-right text-em"><?php echo @$mtotal["substitute"] ? @number_format($mtotal["substitute"], 1) : ""; ?></td>
				<td class="text-right text-em"><?php echo @$mtotal["representative"] ? @number_format($mtotal["representative"], 1) : ""; ?></td>
				<td class="text-right text-em"><?php echo @$mtotal[null] ? @number_format($mtotal[null], 1) : ""; ?></td>
			</tr>

		</tbody>
	</table>



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