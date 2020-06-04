var Beds = {
    Create: function() {
        $.post(window.location.href, $("#bed_create").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("bed_create");
                    Logging.logMessage("Core", "Beds", "Bed Created OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/Hospital/Beds/" + resp.BID + '/');
                    break;
                default:
                    msg = "Bed Create Failed: " + resp.Message
                    Logging.logMessage("Core", "Beds", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#bed_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('Bed Update');
                    $('.modal-body').text("Bed Updated OK");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Beds", "Bed Updated OK");
                    break;
                default:
                    msg = "Bed Update Failed: " + resp.Message
                    $('.modal-title').text('Bed Update');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Beds", msg);
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_bed": 1, "id": $("#id").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Beds", "Bed MQTT Reset OK");
                    $("#mqttp").text(resp.P)
                    break;
                default:
                    msg = "Bed MQTT Reset Failed: " + resp.Message
                    Logging.logMessage("Core", "Beds", msg);
                    break;
            }
        });
    },
    HideInputs: function() {

        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');

        Beds.mqttua = $("#bedmqttu").text();
        Beds.mqttuae = $("#bedmqttu").text().replace(/\S/gi, '*');
        Beds.mqttpa = $("#bedmqttp").text();
        Beds.mqttpae = $("#bedmqttp").text().replace(/\S/gi, '*');

        $("#bedmqttu").text(Beds.mqttuae);
        $("#bedmqttp").text(Beds.mqttpae);
    },
};
$(document).ready(function() {

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#bedmqttu').hover(function() {
        $("#bedmqttu").text(Beds.mqttua);
    }, function() {
        $("#bedmqttu").text(Beds.mqttuae);
    });

    $('#bedmqttp').hover(function() {
        $("#bedmqttp").text(Beds.mqttpa);
    }, function() {
        $("#bedmqttp").text(Beds.mqttpae);
    });

    $('#bed_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Beds.Create();
        }
    });

    $('#bed_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Beds.Update();
        }
    });

    $("#GeniSysAI").on("click", "#reset_bed_mqtt", function(e) {
        e.preventDefault();
        Beds.ResetMqtt();
    });

});