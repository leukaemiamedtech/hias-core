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
        Logging.logMessage("Core", "Forms", "Begin form submission");

        $(".username-validate").each(function() {
            if (!validation.usernameValidation(this.id)) {
                submit = false;
                Logging.logMessage("Core", "Forms", "Username is required");
            }
        });

        $(".password-validate").each(function() {
            if (!validation.passwordValidation(this.id)) {
                submit = false;
                Logging.logMessage("Core", "Forms", "Password is required");
            }
        });

        if (submit) {
            $.post(window.location.href, $("#Login").serialize(), function(arsep) {
                console.log(arsep);
                var arsep = jQuery.parseJSON(arsep);
                switch (arsep.Response) {
                    case "OK":
                        GeniSys.ResetForm("Login");
                        Logging.logMessage("Core", "Forms", "Login OK");
                        window.location.replace(location.protocol + "//" + location.hostname + "/Dashboard");
                        break;
                    case "BLOCKED":
                        GeniSys.ResetForm("Login");
                        Logging.logMessage("Core", "Forms", "Login BLOCKED");
                        window.location.replace(location.protocol + "//" + location.hostname + "/Blocked");
                        break;
                    default:
                        GeniSys.ResetForm("Login");
                        msg = "Form submission failed: " + arsep.ResponseMessage
                        Logging.logMessage("Core", "Forms", msg);
                        GeniSys.ShowModel("GeniSysAi", "Failed", msg);
                        break;
                }
            });
        } else {
            GeniSys.ResetForm("Login");
            msg = "Form submission failed";
            Logging.logMessage("Core", "Forms", msg);
            GeniSys.ShowModel("GeniSysAi", "Failed", msg);
        }
    },
    ResetPass: function() {
        $.post(window.location.href, $("#form").serialize(),
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        var modal = $(this)
                        modal.find('.modal-title').text('New Password')
                        modal.find('.modal-body input').val(resp.pw)
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        break;
                }
            });
    }
};

$("#GeniSysAI").on("click", "#loginsub", function(e) {
    e.preventDefault();
    GeniSys.Login();
});

$("#GeniSysAI").on("click", "#resetpass", function(e) {
    e.preventDefault();
    GeniSys.ResetPass();
});