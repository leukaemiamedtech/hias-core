var Patients = {
    Create: function() {
        $.post(window.location.href, $("#patient_create").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("patient_create");
                    Logging.logMessage("Core", "Patients", "Patient Created OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/Hospital/Patients/" + resp.UID + '/');
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
                    $('.modal-body').text("Patient Updated OK");
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
        $.post(window.location.href, { "reset_mqtt_patient": 1, "id": $("#id").val() },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Patients", "Patient MQTT Reset OK");
                        $("#pntmqttp").text(resp.P)
                        break;
                    default:
                        msg = "Patient MQTT Reset Failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
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

        $("#pntmqttu").text(Patients.mqttuae);
        $("#pntmqttp").text(Patients.mqttpae);
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

    $("#GeniSysAI").on("click", "#reset_patients_mqtt", function(e) {
        e.preventDefault();
        Patients.ResetMqtt();
    });

});