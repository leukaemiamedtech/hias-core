var Staff = {
    Create: function() {
        $.post(window.location.href, $("#staff_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("staff_create");
                    Logging.logMessage("Core", "Staff", "Staff Created OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/Hospital/Staff/" + resp.UID + '/');
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
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('Staff Update');
                    $('.modal-body').text("Staff Updated OK");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Staff", "Staff Updated OK");
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
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_staff": 1, "id": $("#aid").val() },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "MQTT", "User MQTT Password Reset OK");
                        $("#usrmqttp").text(resp.P)
                        break;
                    default:
                        msg = "User MQTT Password Reset Failed: " + resp.Message
                        Logging.logMessage("Core", "MQTT", msg);
                        break;
                }
            });
    },
    ResetPass: function() {
        $.post(window.location.href, { "reset_u_pass": 1, "id": $("#id").val(), "user": $("#username").val() },
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Security", "User Password Reset OK");
                        $('.modal-title').text('New Password');
                        $('.modal-body').text(resp.pw);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "User Password Reset Failed: " + resp.Message
                        Logging.logMessage("Core", "Security", msg);
                        break;
                }
            });
    },
    HideInputs: function() {

        Staff.mqttua = $("#usrmqttu").text();
        Staff.mqttuae = $("#usrmqttu").text().replace(/\S/gi, '*');
        Staff.mqttpa = $("#usrmqttp").text();
        Staff.mqttpae = $("#usrmqttp").text().replace(/\S/gi, '*');

        $("#usrmqttu").text(Staff.mqttuae);
        $("#usrmqttp").text(Staff.mqttpae);
    },
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

    $("#GeniSysAI").on("click", "#reset_staff_mqtt", function(e) {
        e.preventDefault();
        Staff.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_pass", function(e) {
        e.preventDefault();
        Staff.ResetPass();
    });

    $('#usrmqttu').hover(function() {
        $("#usrmqttu").text(Staff.mqttua);
    }, function() {
        $("#usrmqttu").text(Staff.mqttuae);
    });

    $('#usrmqttp').hover(function() {
        $("#usrmqttp").text(Staff.mqttpa);
    }, function() {
        $("#usrmqttp").text(Staff.mqttpae);
    });

});