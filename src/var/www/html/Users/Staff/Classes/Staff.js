var Staff = {
    Create: function() {
        $.post(window.location.href, $("#staff_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    HIAS.ResetForm("staff_create");
                    $('.modal-title').text('HIAS Staff');
                    $('.modal-body').html("HIAS Staff ID " + resp.UID + " created!<br /><br />The staff member's credentials are provided below. Passwords can be reset however the HIASBCH password cannot.<br /><br /><strong>User ID:</strong> " + resp.UID + "<br /><strong>Username:</strong> " + resp.Uname + "<br /><strong>User Password:</strong> " + resp.Upass + "<br /><br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>HIASBCH User:</strong> " + resp.BCU + "<br /><strong>HIASBCH Pass:</strong> " + resp.BCP + "<br />");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Staff ID #" + resp.UID + " created!");
                    break;
                default:
                    msg = "Staff Creation Failed: " + resp.Message
                    Logging.logMessage("Core", "Staff", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#staff_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    $('.modal-title').text('Staff Update');
                    $('.modal-body').html(resp.Message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Staff", resp.Message);
                    break;
                default:
                    msg = "Staff Update Failed: " + resp.Message
                    $('.modal-title').text('Staff Update');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Staff", msg);
                    break;
            }
        });
    },
    ResetPass: function() {
        $.post(window.location.href, { "reset_u_pass": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Security", "User Password Reset OK");
                    $('.modal-title').text('New Password');
                    $('.modal-body').text("This user's new password is: " + resp.pw);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "User Password Reset Failed: " + resp.Message
                    Logging.logMessage("Core", "Security", msg);
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_staff": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Staff.mqttpa = resp.P;
                    Staff.mqttpae = resp.P.replace(/\S/gi, '*');
                    $("#usrmqttp").text(Staff.mqttpae)
                    Logging.logMessage("Core", "MQTT", "User MQTT Password Reset OK");
                    $('.modal-title').text('New Password');
                    $('.modal-body').text("This user's new MQTT password is: " + resp.P);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "User MQTT Password Reset Failed: " + resp.Message
                    Logging.logMessage("Core", "MQTT", msg);
                    break;
            }
        });
    },
    ResetAppAMQP: function() {
        $.post(window.location.href, { "reset_user_amqp": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Staff.amqpa = resp.P;
                        Staff.amqpae = resp.P.replace(/\S/gi, '*');
                        $("#appamqpp").text(Staff.amqpae)
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        $('.modal-title').text('Reset App AMQP Key');
                        $('.modal-body').text("This user's new application AMQP key is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        $('.modal-title').text('Reset App AMQP Key');
                        $('.modal-body').text(msg);
                        $('#responsive-modal').modal('show');
                        break;
                }
            });
    },
    ResetAppKey: function() {
        $.post(window.location.href, { "reset_appkey_staff": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "API", "User API Key Reset OK");
                        $('.modal-title').text('New API Key');
                        $('.modal-body').text("This user's new API Key is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "User API Key Reset Failed: " + resp.Message
                        Logging.logMessage("Core", "API", msg);
                        break;
                }
            });
    },
    changeStaffHistory: function(changeTo) {
        $.post(window.location.href, { "update_staff_history": 1, "staffHistory": $("#staffHistory").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#staffHistoryContainer").html(resp.Data);
                    break;
                default:
                    $("#staffHistoryContainer").html("");
                    break;
            }
        });
    }
};
$(document).ready(function() {

    $('#staff_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Staff.Create();
        }
    });

    $('#staff_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Staff.Update();
        }
    });

    $("#GeniSysAI").on("click", "#reset_pass", function(e) {
        e.preventDefault();
        Staff.ResetPass();
    });

    $("#GeniSysAI").on("click", "#reset_staff_mqtt", function(e) {
        e.preventDefault();
        Staff.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_staff_apriv", function(e) {
        e.preventDefault();
        Staff.ResetAppKey();
    });

    $("#GeniSysAI").on("click", "#reset_user_amqp", function(e) {
        e.preventDefault();
        Staff.ResetAppAMQP();
    });

    $('#staffHistory').on('change', function(e) {
        e.preventDefault();
        Staff.changeStaffHistory($(this).val());
    });
});