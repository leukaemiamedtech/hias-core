var TASS = {
    Create: function() {
        $.post(window.location.href, $("#tass_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("tass_create");
                    Logging.logMessage("Core", "TASS", "TASS Create OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/TASS/" + resp.DID + '/');
                    break;
                default:
                    msg = "TASS Create Failed: " + resp.Message
                    Logging.logMessage("Core", "TASS", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#tass_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('TASS Update');
                    $('.modal-body').text("TASS Update OK");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "TASS", "TASS Update OK");
                    break;
                default:
                    msg = "TASS Update Failed: " + resp.Message
                    Logging.logMessage("Core", "TASS", msg);
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_mqtt": 1, "id": $("#did").val() },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Credentials", "Reset OK");
                        TASS.mqttp3a = resp.P;
                        TASS.mqttp3ae = resp.P.replace(/\S/gi, '*');
                        $("#mqttp3").text(resp.P)
                        break;
                    default:
                        msg = "Credentials reset failed: " + resp.Message
                        Logging.logMessage("Core", "Credentials", msg);
                        break;
                }
            });
    },
    HideInputs: function() {
        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');
        $('#sport').attr('type', 'password');
        $('#sportf').attr('type', 'password');
        $('#sckport').attr('type', 'password');

        TASS.mqttu3a = $("#mqttut").text();
        TASS.mqttu3ae = $("#mqttut").text().replace(/\S/gi, '*');
        TASS.mqttp3a = $("#mqttpt").text();
        TASS.mqttp3ae = $("#mqttpt").text().replace(/\S/gi, '*');

        $("#mqttut").text(TASS.mqttu3ae);
        $("#mqttpt").text(TASS.mqttp3ae);
    },
    GetLife: function() {
        $.post(window.location.href, { "get_tlife": 1, "device": $("#id").val() }, function(resp) {
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
                    $("#idecpuU").text(resp.ResponseData.cpu)
                    $("#idememU").text(resp.ResponseData.mem)
                    $("#idehddU").text(resp.ResponseData.hdd)
                    $("#idetempU").text(resp.ResponseData.tempr)
                    Logging.logMessage("Core", "TASS", "TASS Stats Updated OK");
                    break;
                default:
                    msg = "TASS Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "TASS", msg);
                    break;
            }
        });
    },
};
$(document).ready(function() {

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#tass_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            TASS.Create();
        }
    });

    $('#tass_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            TASS.Update();
        }
    });

    $("#GeniSysAI").on("click", "#reset_mqtt", function(e) {
        e.preventDefault();
        TASS.ResetMqtt();
    });

    $('#mqttut').hover(function() {
        $("#mqttut").text(TASS.mqttu3a);
    }, function() {
        $("#mqttut").text(TASS.mqttu3ae);
    });

    $('#mqttpt').hover(function() {
        $("#mqttpt").text(TASS.mqttp3a);
    }, function() {
        $("#mqttpt").text(TASS.mqttp3ae);
    });

    setInterval(function() {
        TASS.GetLife();
    }, 5000);

});