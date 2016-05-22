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

$parties = $ppBo->getAdministratedParties($userId);

$parties = array_merge(array(-1 => array("ppa_id" => 0, "ppa_name" => "")), $parties);
foreach($parties as $key => $party) {
	if ($key == 0) {
		$parties[$key]["administrators"] = array(array("use_id" => $userId, "use_login" => $user));
	}
	else {
		$parties[$key]["administrators"] = $ppBo->getAdministrators($party["ppa_id"]);
	}
}

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?> </a></li>
		<?php 	if ($user) {?>
		<li><a href="mypage.php"><?php echo $user; ?></a></li>
		<?php 	}?>
		<li class="active"><?php echo lang("breadcrumb_myparties"); ?></li>
	</ol>

	<div class="well well-sm">
		<p>
			<?php echo lang("myparties_guide"); ?>
		</p>
	</div>

	<?php 	if ($user) {?>

	<div id="configurationTabs" role="tabpanel">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<?php 	foreach($parties as $party) {
						if ($party["ppa_id"] != 0) {?>
			<li role="presentation"><a href="#<?php echo str_replace(" ", "_", $party["ppa_name"]); ?>" aria-controls="<?php echo $party["ppa_name"]; ?>" role="tab" data-toggle="tab"><?php echo $party["ppa_name"]; ?></a></li>
				<?php 	} else {?>
			<li role="presentation"><a href="#new" aria-controls="new" role="tab" data-toggle="tab"><?php echo lang("myparties_newPartyTabLabel"); ?></a></li>
			<?php 		}
					}?>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<?php foreach($parties as $party) {?>
			<div role="tabpanel" class="tab-pane" id="<?php echo $party["ppa_id"] == 0 ? "new" : str_replace(" ", "_", $party["ppa_name"]); ?>">

				<form class="form-horizontal">
					<fieldset>
						<!-- Form Name -->
						<legend>
							<?php 	if ($party["ppa_id"] != 0) {
										echo str_replace("{party}", $party["ppa_name"], lang("myparties_existingparty_form_legend"));
									}
									else  {
										echo lang("myparties_newparty_form_legend");
									}?>
						</legend>

						<input type="hidden" name="partyIdInput" id="partyIdInput" value="<?php echo $party["ppa_id"]; ?>"/>

						<!-- Text input-->
						<div class="form-group">
							<label class="col-md-4 control-label" for="nameInput"><?php echo lang("myparties_party_form_nameInput"); ?></label>
							<div class="col-md-6">
								<input id="nameInput" name="nameInput" value="<?php echo @$party["ppa_name"];?>" type="text"
									placeholder="" class="form-control input-md">
							</div>
						</div>

						<legend>
							<?php echo lang("myparties_administrators_form_legend"); ?>
						</legend>

						<div class="form-group">
							<label class="col-md-4 control-label" for="addUserInput"><?php echo lang("myparties_administrators_form_addUserInput"); ?> </label>
							<div class="col-md-6">
								<div class="input-group">
									<input
										id="addUserInput" name="addUserInput" value="" type="text" placeholder="" class="form-control input-md typeahead"
											data-provide="typeahead" data-items="4" data-soure=''>
									<span class="input-group-btn">
										<button type="button" class="addUserButton btn btn-default">
											<span class="glyphicon glyphicon-plus"></span>
										</button>
									</span>
								</div>
								<p class="btn-group administrators">
									<?php foreach($party["administrators"] as $administrator) {?>
									<span style="margin-right:5px; " class="label label-default"><input type="hidden" class="administratorId" value="<?php echo $administrator["use_id"]; ?>" /><?php echo $administrator["use_login"]; ?><span style="margin-left:5px; " class="glyphicon glyphicon-remove"></span></span>
									<?php }?>
								</p>
							</div>
						</div>

						<!-- Button (Double) -->
						<div class="form-group">
							<label class="col-md-4 control-label" for="savePartyButton"></label>
							<div class="col-md-8">
							<?php 	if ($party["ppa_id"] != 0) {?>
								<button id="savePartyButton" name="savePartyButton" class="savePartyButton btn btn-default"><?php echo lang("myparties_modify"); ?></button>
							<?php 	} else {?>
								<button id="savePartyButton" name="savePartyButton" class="savePartyButton btn btn-default"><?php echo lang("myparties_save"); ?></button>
							<?php 	}?>
							</div>
						</div>

					</fieldset>
				</form>

			</div>
			<?php }?>
		</div>
	</div>

	<?php 		echo addAlertDialog("ok_operation_successAlert", lang("ok_operation_success"), "success"); ?>

	<?php 	}
			else {
				include("connectButton.php");
			}?>

</div>

<div class="lastDiv"></div>

<?php include("footer.php");?>
<script>

function responseHandler(data) {
	if (data.ok) {
		$("#ok_operation_successAlert").show().delay(2000).fadeOut(1000);

		window.location.hash = "#" + data.name;
		window.location.reload(true);
	}
	else {
		$("#" + data.message + "Alert").show().delay(2000).fadeOut(1000);
	}
}

function administratorClickHandler() {
	if ($(this).parents("form").find(".administrators .label").length > 1) {
		$(this).remove();
	}
}

function validatorClickHandler() {
	$(this).remove();
}

function deleteGroupClickHandler(event) {
	event.preventDefault();
	$(this).parents(".validatorGroup").remove();
}

$(function() {
	$(".administrators .label").click(administratorClickHandler);

	$(".addUserButton").click(function(event) {
		event.preventDefault();
		var button = $(this);
		var user = button.parent("span").siblings("input").val();

		$.post("do_getUserId.php", {"user": user}, function(data) {
			if (data.id) {
				var userLabel = "<span style=\"margin-right:5px; \" class=\"label label-default\">";
				userLabel += user;
				userLabel += "<input type=\"hidden\" class=\"administratorId\" value=\"" + data.id + "\" />";
				userLabel += "<span style=\"margin-left:5px; \" class=\"glyphicon glyphicon-remove\"></span></span>";
				userLabel = $(userLabel);

				userLabel.click(administratorClickHandler);

				button.parent("span").siblings("input").val("");
				button.parents("form").find(".administrators").append(userLabel);
			}
		}, "json");
	});

	$('.savePartyButton').click(function (e) {
		e.preventDefault();

		var formInputs = $(this).parents("form");

		var administratorIdInputs = formInputs.find(".administratorId");
		var administratorIds = [];

		administratorIdInputs.each(function() {
			administratorIds[administratorIds.length] = $(this).val();
		});

		var myform = 	{
							id: formInputs.find("#partyIdInput").val(),
							name: formInputs.find("#nameInput").val(),
							administratorIds: JSON.stringify(administratorIds)
						};

		$.post("do_myparties.php", myform, responseHandler, "json");
	});

	if (window.location.hash) {
		$("a[aria-controls='"+window.location.hash.replace("#","")+"']").tab('show');
	}
	else {
		$("a[aria-controls='new']").tab('show');
	}

	$("li a[data-toggle=tab]").click(function(event) {
		var anchor = $(this).attr("aria-controls");
		window.location.hash = "#" + anchor;
	});

	$("input[type=checkbox]").click(function(event) {
		if ($(this).attr("checked")) {
			$(this).removeAttr("checked");
		}
		else {
			$(this).attr("checked", "checked");
		}
	});
});
</script>
</body>
</html>