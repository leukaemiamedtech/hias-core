var Patients = {
    Create: function() {
        $.post(window.location.href, $("#patient_create").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("patient_create");
                    $('.modal-title').text('HIAS Patient');
                    $('.modal-body').html("HIAS Patient ID #" + resp.UID + " created! The patient's credentials are provided below, please provide them to the new patient. The credentials can be reset in the patient area.<br /><br /><strong>User ID:</strong> " + resp.UID + "<br /><strong>Username:</strong> " + resp.Uname + "<br /><strong>User Password:</strong> " + resp.Upass + "<br /><br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain User:</strong> " + resp.BCU + "<br /><strong>Blockchain Pass:</strong> " + resp.BCP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br />");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Patient ID #" + resp.UID + " created!");
                    break;
                default:
                    msg = "Patient Create Failed: " + resp.Message
                    Logging.logMessage("Core", "Patients", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#patient_update").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('Patient Update');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Patients", "Patient Updated OK");
                    break;
                default:
                    msg = "Patient Update Failed: " + resp.Message
                    $('.modal-title').text('Patient Update');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Patients", msg);
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_patient": 1, "identifier": $("#identifier").val(), "pid": $("#id").val(), "id": $("#aid").val() },
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Patients.mqttpa = resp.P;
                        Patients.mqttpae = resp.P.replace(/\S/gi, '*');
                        $("#pntmqttp").text(Patients.mqttpae)
                        Logging.logMessage("Core", "MQTT", "Patient MQTT Password Reset OK");
                        $('.modal-title').text('New Password');
                        $('.modal-body').text("This patient's new MQTT password is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Patient MQTT Password Reset Failed: " + resp.Message
                        Logging.logMessage("Core", "MQTT", msg);
                        break;
                }
            });
    },
    ResetAppKey: function() {
        $.post(window.location.href, { "reset_pt_apriv": 1, "identifier": $("#identifier").val(), "pid": $("#id").val(), "id": $("#aid").val() },
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

        $('#email').attr('type', 'password');
        $('#username').attr('type', 'password');

        Patients.mqttua = $("#pntmqttu").text();
        Patients.mqttuae = $("#pntmqttu").text().replace(/\S/gi, '*');
        Patients.mqttpa = $("#pntmqttp").text();
        Patients.mqttpae = $("#pntmqttp").text().replace(/\S/gi, '*');
        Patients.idappida = $("#idappid").text();
        Patients.idappidae = $("#idappid").text().replace(/\S/gi, '*');
        Patients.bcida = $("#bcid").text();
        Patients.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#pntmqttu").text(Patients.mqttuae);
        $("#pntmqttp").text(Patients.mqttpae);
        $("#idappid").text(Patients.idappidae);
        $("#bcid").text(Patients.bcidae);
    },
};
$(document).ready(function() {

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#pntmqttu').hover(function() {
        $("#pntmqttu").text(Patients.mqttua);
    }, function() {
        $("#pntmqttu").text(Patients.mqttuae);
    });

    $('#pntmqttp').hover(function() {
        $("#pntmqttp").text(Patients.mqttpa);
    }, function() {
        $("#pntmqttp").text(Patients.mqttpae);
    });

    $('#idappid').hover(function() {
        $("#idappid").text(Patients.idappida);
    }, function() {
        $("#idappid").text(Patients.idappidae);
    });

    $('#bcid').hover(function() {
        $("#bcid").text(Patients.bcida);
    }, function() {
        $("#bcid").text(Patients.bcidae);
    });

    $('#patient_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Patients.Create();
        }
    });

    $('#patient_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Patients.Update();
        }
    });

    $("#GeniSysAI").on("click", "#reset_patient_mqtt", function(e) {
        e.preventDefault();
        Patients.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_pt_apriv", function(e) {
        e.preventDefault();
        Patients.ResetAppKey();
    });

});