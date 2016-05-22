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

//echo "Realpath : " . realpath(".");

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li class="active"><?php echo lang("breadcrumb_index"); ?></li>
	</ol>
	<div class="well well-sm">
		<p><?php echo lang("index_guide"); ?></p>
	</div>

	<?php 	if ($user) {
				include("currentCampaign.php");
			}
			else {
				include("connectButton.php");
			}?>

</div>

<div class="lastDiv"></div>

<script type="text/javascript">
var userLanguage = '<?php echo SessionUtils::getLanguage($_SESSION); ?>';
var $lang = <?php echo json_encode($lang); ?>;
</script>
<?php include("footer.php");?>
</body>
</html>
