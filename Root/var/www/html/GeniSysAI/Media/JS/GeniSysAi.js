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
    }
};

$("#GeniSysAI").on("click", "#loginsub", function(e) {
    e.preventDefault();
    GeniSys.Login();
});