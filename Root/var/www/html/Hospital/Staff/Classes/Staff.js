var Staff = {
    Create: function() {
        $.post(window.location.href, $("#staff_create").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("staff_create");
                    $('.modal-title').text('HIAS Staff');
                    $('.modal-body').html("HIAS Staff ID #" + resp.UID + " created! Please save your API keys safely. The staff member's credentials are provided below, please provide them to the new staff member. The credentials can be reset in the staff area.<br /><br /><strong>User ID:</strong> " + resp.UID + "<br /><strong>Username:</strong> " + resp.Uname + "<br /><strong>User Password:</strong> " + resp.Upass + "<br /><br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain User:</strong> " + resp.BCU + "<br /><strong>Blockchain Pass:</strong> " + resp.BCP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br />");
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
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
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
        $.post(window.location.href, { "reset_u_pass": 1, "id": $("#id").val(), "user": $("#username").val() },
            function(resp) {
                console.log(resp)
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
        $.post(window.location.href, { "reset_mqtt_staff": 1, "id": $("#aid").val(), "uid": $("#id").val() },
            function(resp) {
                console.log
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
    ResetAppKey: function() {
        $.post(window.location.href, { "reset_appkey_staff": 1, "id": $("#aid").val(), "uid": $("#id").val() },
            function(resp) {
                console.log
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
    HideInputs: function() {
        Staff.mqttua = $("#usrmqttu").text();
        Staff.mqttuae = $("#usrmqttu").text().replace(/\S/gi, '*');
        Staff.mqttpa = $("#usrmqttp").text();
        Staff.mqttpae = $("#usrmqttp").text().replace(/\S/gi, '*');
        Staff.usrappid = $("#usrappid").text();
        Staff.usrappide = $("#usrappid").text().replace(/\S/gi, '*');
        Staff.usrappid = $("#usrappid").text();
        Staff.usrappide = $("#usrappid").text().replace(/\S/gi, '*');
        Staff.usrbcid = $("#usrbcid").text();
        Staff.usrbcide = $("#usrbcid").text().replace(/\S/gi, '*');

        $("#usrmqttu").text(Staff.mqttuae);
        $("#usrmqttp").text(Staff.mqttpae);
        $("#usrappid").text(Staff.usrappide);
        $("#usrbcid").text(Staff.usrbcide);
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

    $("#GeniSysAI").on("click", "#reset_staff_apriv", function(e) {
        e.preventDefault();
        Staff.ResetAppKey();
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

    $('#usrappid').hover(function() {
        $("#usrappid").text(Staff.usrappid);
    }, function() {
        $("#usrappid").text(Staff.usrappide);
    });

    $('#usrbcid').hover(function() {
        $("#usrbcid").text(Staff.usrbcid);
    }, function() {
        $("#usrbcid").text(Staff.usrbcide);
    });

});