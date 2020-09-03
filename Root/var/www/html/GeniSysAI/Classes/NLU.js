var NLU = {
    Create: function() {
        $.post(window.location.href, $("#genisysai_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("genisysai_create");
                    Logging.logMessage("Core", "NLU", "NLU Create OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/NLU/" + resp.DID + '/');
                    break;
                default:
                    msg = "NLU Create Failed: " + resp.Message
                    Logging.logMessage("Core", "NLU", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#genisysai_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('NLU Update');
                    $('.modal-body').text("NLU Update OK");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "NLU", "NLU Update OK");
                    break;
                default:
                    msg = "NLU Update Failed: " + resp.Message
                    Logging.logMessage("Core", "NLU", msg);
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
                        NLU.mqttp3a = resp.P;
                        NLU.mqttp3ae = resp.P.replace(/\S/gi, '*');
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

        NLU.mqttu3a = $("#mqttut").text();
        NLU.mqttu3ae = $("#mqttut").text().replace(/\S/gi, '*');
        NLU.mqttp3a = $("#mqttpt").text();
        NLU.mqttp3ae = $("#mqttpt").text().replace(/\S/gi, '*');

        $("#mqttut").text(NLU.mqttu3ae);
        $("#mqttpt").text(NLU.mqttp3ae);
    },
    GetLife: function() {
        $.post(window.location.href, { "get_tlife": 1, "device": $("#did").val() }, function(resp) {
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
                    Logging.logMessage("Core", "NLU", "NLU Stats Updated OK");
                    break;
                default:
                    msg = "NLU Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "NLU", msg);
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

    $('#genisysai_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            NLU.Create();
        }
    });

    $('#genisysai_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            NLU.Update();
        }
    });

    $("#NLU").on("click", "#reset_mqtt", function(e) {
        e.preventDefault();
        NLU.ResetMqtt();
    });

    $('#mqttut').hover(function() {
        $("#mqttut").text(NLU.mqttu3a);
    }, function() {
        $("#mqttut").text(NLU.mqttu3ae);
    });

    $('#mqttpt').hover(function() {
        $("#mqttpt").text(NLU.mqttp3a);
    }, function() {
        $("#mqttpt").text(NLU.mqttp3ae);
    });

    setInterval(function() {
        NLU.GetLife();
    }, 5000);

});