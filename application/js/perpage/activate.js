/* global $ */

function activateHandler(event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    var form = $("#formPanel");
    form.hide();

    $.post("do_activate.php", form.serialize(), function(data) {
        $("#panel-success").hide();
        if (data.ko) {
            $("#panel-final").hide();
            $("#panel-danger").show();
            form.show();
        }
        else {
            $("#panel-danger").hide();
            $("#panel-final").show();
        }
    }, "json");
}

function checkFormStatus() {
    var form = $("#formPanel");
    
    var errorFields = form.find(".has-error,.has-warning");
    if (errorFields.length) {
        $("#btn-activate").attr("disabled", "disabled");
    }
    else {
        $("#btn-activate").removeAttr("disabled");
    }
}

function loginKeyupHandler() {
    var login = $("#userLoginInput").val();
    var loginWrapper = $("#userLoginInput").parents(".form-group");
    
    loginWrapper.removeClass("has-error");

    if (!login) {
        loginWrapper.addClass("has-error");
        checkFormStatus();
    }
    else if (login.toLowerCase().indexOf("anonymous") == 0) {
        loginWrapper.addClass("has-warning");
        checkFormStatus();
    }
    else {
        // test unicity
        checkFormStatus();
    }
}

function passwordKeyupHandler() {
    var password = $("#userPasswordInput").val();
    var confirm = $("#userConfirmationInput").val();

    var passwordWrapper = $("#userPasswordInput").parents(".form-group");
    var confirmWrapper = $("#userConfirmationInput").parents(".form-group");

    passwordWrapper.removeClass("has-error").removeClass("has-warning");
    confirmWrapper.removeClass("has-error").removeClass("has-warning");

    if (password != confirm) {
        passwordWrapper.addClass("has-warning");
        confirmWrapper.addClass("has-warning");
    }
    
    if (!password) {
        passwordWrapper.addClass("has-error");
    }
    
    if (!confirm) {
        confirmWrapper.addClass("has-error");
    }

    checkFormStatus();
}

$(function() {
    $("#userPasswordInput, #userConfirmationInput").keyup(passwordKeyupHandler);
    $("#userLoginInput").keyup(loginKeyupHandler);
    $("#btn-activate").click(activateHandler);
    
    passwordKeyupHandler();
    loginKeyupHandler();
});