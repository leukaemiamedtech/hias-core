var GeniSys = {
    ShowModel: function(nodel, h1, p) {
        $("#" + nodel + "Window").modal("show");
        $("#" + nodel + "WindowH").text(h1);
        $("#" + nodel + "WindowP").text(p);
    },
    ResetForm: function(id) {
        $("#" + id)[0].reset();
    },
    Login: function() {
        submit = true;
        Logging.logMessage("Core", "HIAS", "Begin form submission");

        $(".username-validate").each(function() {
            if (!validation.usernameValidation(this.id)) {
                submit = false;
                Logging.logMessage("Core", "HIAS", "Username is required");
            }
        });

        $(".password-validate").each(function() {
            if (!validation.passwordValidation(this.id)) {
                submit = false;
                Logging.logMessage("Core", "HIAS", "Password is required");
            }
        });

        if (submit) {
            $.post(window.location.href, $("#Login").serialize(), function(arsep) {
                console.log(arsep);
                var arsep = jQuery.parseJSON(arsep);
                switch (arsep.Response) {
                    case "OK":
                        GeniSys.ResetForm("Login");
                        Logging.logMessage("Core", "HIAS", "Login OK");
                        window.location.replace(location.protocol + "//" + location.hostname + "/Dashboard");
                        break;
                    case "BLOCKED":
                        GeniSys.ResetForm("Login");
                        Logging.logMessage("Core", "HIAS", "Login BLOCKED");
                        window.location.replace(location.protocol + "//" + location.hostname + "/Blocked");
                        break;
                    default:
                        GeniSys.ResetForm("Login");
                        msg = "Form submission failed: " + arsep.ResponseMessage
                        Logging.logMessage("Core", "HIAS", msg);
                        $('.modal-title').text('Failed');
                        $('.modal-body').text(msg);
                        $('#responsive-modal').modal('show');
                        break;
                }
            });
        } else {
            GeniSys.ResetForm("Login");
            msg = "Form submission failed";
            Logging.logMessage("Core", "GeniHIASSysAI", msg);
            $('.modal-title').text('Failed');
            $('.modal-body').text(msg);
            $('#responsive-modal').modal('show');
        }
    },
    ResetPass: function() {
        $.post(window.location.href, $("#form").serialize(),
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "HIAS", "Password Reset OK");
                        $('.modal-title').text('New Password');
                        $('.modal-body').text(resp.pw);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "HIAS", msg);
                        break;
                }
            });
    },
    HideInputs: function() {
        $('#domainString').attr('type', 'password');
        $('#phpmyadmin').attr('type', 'password');
        $('#lt').attr('type', 'password');
        $('#lg').attr('type', 'password');
        $('#gmaps').attr('type', 'password');
        $('#recaptcha').attr('type', 'password');
        $('#recaptchas').attr('type', 'password');
    },
    GetLife: function() {
        $.post(window.location.href, { "get_slife": 1, "application": $("#id").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    if (resp.ResponseData["status"] == "ONLINE") {
                        $("#offline1").removeClass("hide");
                        $("#online1").addClass("hide");
                    } else {
                        $("#offline1").addClass("hide");
                        $("#online1").removeClass("hide");
                    }
                    $("#svrecpu").text(resp.ResponseData.cpu)
                    $("#svremem").text(resp.ResponseData.mem)
                    $("#svrehdd").text(resp.ResponseData.hdd)
                    $("#svretempr").text(resp.ResponseData.tempr)
                    Logging.logMessage("Core", "HIAS", "HIAS Stats Updated OK");
                    break;
                default:
                    msg = "HIAS Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "HIAS", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#server_update").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('HIAS Server Update');
                    $('.modal-body').text("HIAS Server Update OK");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "HIAS", "HIAS Server Update OK");
                    break;
                default:
                    msg = "HIAS Server Update Failed: " + resp.Message
                    Logging.logMessage("Core", "HIAS", msg);
                    break;
            }
        });
    },
};

$("#GeniSysAI").on("click", "#loginsub", function(e) {
    e.preventDefault();
    GeniSys.Login();
});
$("#GeniSysAI").on("click", "#resetpass", function(e) {
    e.preventDefault();
    GeniSys.ResetPass();
});

$('#server_update').validator().on('submit', function(e) {
    if (!e.isDefaultPrevented()) {
        e.preventDefault();
        GeniSys.Update();
    }
});

$('.hider').hover(function() {
    $('#' + $(this).attr("id")).attr('type', 'text');
}, function() {
    $('#' + $(this).attr("id")).attr('type', 'password');
});