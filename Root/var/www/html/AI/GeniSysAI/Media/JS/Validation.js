var validation = {
    emailRegex: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    phoneRegex: /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/,
    usernameRegex: /^[a-zA-Z0-9]+$/,
    urlRegex: /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,
    textValidation: function(id) {
        var retVal = false;
        switch ($("#" + id).val()) {
            case "":
                $("#" + id).addClass("formError");
                retVal = false;
                Logging.logMessage("Core", "Validation", "Text Field Empty");
                break;

            default:
                $("#" + id).removeClass("formError");
                retVal = true;
                Logging.logMessage(
                    "Core",
                    "Validation",
                    "Text Field Validation OK (" + $("#" + id).val() + ")"
                );
        }
        return retVal;
    },
    selectValidation: function(id) {
        var retVal = false;
        switch ($("#" + id).val()) {
            case "":
                $("#" + id).addClass("formError");
                retVal = false;
                Logging.logMessage("Core", "Validation", "Select Empty");
                break;

            case undefined:
                $("#" + id).addClass("formError");
                retVal = false;
                Logging.logMessage("Core", "Validation", "Select Undefined");
                break;

            default:
                $("#" + id).removeClass("formError");
                retVal = true;
                Logging.logMessage(
                    "Core",
                    "Validation",
                    "Select Validation OK (" + $("#" + id).val() + ")"
                );
        }
        return retVal;
    },
    usernameValidation: function(id) {
        var retVal = false;
        switch ($("#" + id).val()) {
            case "":
                $("#" + id).addClass("formError");
                retVal = false;
                Logging.logMessage("Core", "Validation", "Username Empty");
                break;

            default:
                switch (validation.usernameRegex.test($("#" + id).val())) {
                    case false:
                        $("#" + id).addClass("formError");
                        retVal = false;
                        Logging.logMessage(
                            "Core",
                            "Validation",
                            "Username Validation Failed (" + $("#" + id).val() + ")"
                        );
                        break;

                    default:
                        $("#" + id).removeClass("formError");
                        retVal = true;
                        Logging.logMessage(
                            "Core",
                            "Validation",
                            "Username Validation OK (" + $("#" + id).val() + ")"
                        );
                        break;
                }
                break;
        }

        return retVal;
    },
    passwordValidation: function(id) {
        var retVal = false;
        switch ($("#" + id).val()) {
            case "":
                $("#" + id).addClass("formError");
                retVal = false;
                Logging.logMessage("Core", "Validation", "Password Validation Failed");
                break;

            default:
                $("#" + id).removeClass("formError");
                retVal = true;
                Logging.logMessage("Core", "Validation", "Password Validation OK");
                break;
        }

        return retVal;
    }
};

$("#wrapper").on("focusout", ".text-validate", function() {
    validation.textValidation($(this).attr("id"));
});

$("#wrapper").on("focusout", ".select-validate", function() {
    validation.selectValidation($(this).attr("id"));
});

$("#wrapper").on("focusout", ".username-validate", function() {
    validation.usernameValidation($(this).attr("id"));
});

$(".container").on("focusout", ".password-validate", function() {
    validation.passwordValidation($(this).attr("id"));
});