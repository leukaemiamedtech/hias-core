var HIASCDI = {
    Monitor: function() {
        setInterval(function() {
            HIASCDI.GetStats();
        }, 15000);
    },
    GetStats: function() {
        $.post(window.location.href, { "get_hiascdi_life": true }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    if (resp.Data["status"] == "ONLINE") {
                        $("#offline1").removeClass("hide");
                        $("#online1").addClass("hide");
                    } else {
                        $("#offline1").addClass("hide");
                        $("#online1").removeClass("hide");
                    }
                    $("#batteryUsage").text(resp.Data.battery)
                    $("#cpuUsage").text(resp.Data.cpu)
                    $("#memoryUsage").text(resp.Data.mem)
                    $("#hddUsage").text(resp.Data.hdd)
                    $("#temperatureUsage").text(resp.Data.tempr)
                    Logging.logMessage("Core", "HIASCDI", "HIASCDI Stats Update OK");
                    break;
                default:
                    msg = "HIASCDI Stats Update KO"
                    Logging.logMessage("Core", "HIASCDI", msg);
                    break;
            }
        });
    },
    HideSecret: function() {
        $.each($('.hiderstr'), function() {
            $(this).data("hidden", $(this).text());
            $(this).text($(this).text().replace(/\S/gi, '*'));
        });
    },
    HideInputSecret: function() {
        $.each($('.hider'), function() {
            $(this).attr('type', 'password');
        });
    },
    SendConsole: function() {
        window.parent.$('#response').html("Loading....");
        $.post(window.location.href, $("#hiascdi_console_form").serialize(), function(resp, textStatus, jQxhr) {
            var resp = jQuery.parseJSON(resp);
            rCode = resp.Code;
            rHeaders = resp.Headers;
            $("#rcode").text(rCode);
            $("#rcode").text(rCode);
            $("#rlen").text(resp.Body ? resp.Body.length : 0);
            $("#rtime").text(new Date($.now()));
            switch (parseInt(rCode)) {
                case 200:
                    var fjson = JSON.stringify(resp.Body, null, '\t');
                    window.parent.$('#response').html(fjson);
                    window.parent.$('#response_headers').html(rHeaders);
                    message = resp.Message + " Request status code: " + rCode;
                    Logging.logMessage("Core", "HIASCDI", message);
                    break;
                default:
                    window.parent.$('#response').html("");
                    message = resp.Message + "<br /><br />Request status code: " + rCode;
                    $('.modal-title').text('HIASCDI');
                    $('.modal-body').html(message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "HIASCDI", message);
                    break;
            }
        });
    },
    UpdateCore: function() {
        $.post(window.location.href, $("#update_hiascdi_broker").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            message = resp.Message;
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "HIASCDI", message);
                    $('.modal-title').text('HIASCDI');
                    $('.modal-body').text(message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    Logging.logMessage("Core", "HIASCDI", msg);
                    $('.modal-title').text('HIASCDI');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    UpdateEntity: function() {
        $.post(window.location.href, $("#update_hiascdi_form").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            message = resp.Message;
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "HIASCDI", message);
                    $('.modal-title').text('HIASCDI');
                    $('.modal-body').text(message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    Logging.logMessage("Core", "HIASCDI", msg);
                    $('.modal-title').text('HIASCDI');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    ResetKey: function() {
        $.post(window.location.href, { "reset_agent_apriv": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "HIASCDI", "Reset OK");
                    $('.modal-title').text('Reset App Key');
                    $('.modal-body').text("This agent's new key is: " + resp.P);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Reset failed: " + resp.Message
                    Logging.logMessage("Core", "HIASCDI", msg);
                    $('.modal-title').text('Reset App Key');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_agent_mqtt": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        HIASCDI.amqttpa = resp.P;
                        HIASCDI.amqttpae = resp.P.replace(/\S/gi, '*');
                        $("#amqttp").text(HIASCDI.amqttpae);
                        $('.modal-title').text('Reset MQTT Password');
                        $('.modal-body').text("This agent's new MQTT password is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        $('.modal-title').text('Reset MQTT Password');
                        $('.modal-body').text(msg);
                        $('#responsive-modal').modal('show');
                        break;
                }
            });
    }
};
$(document).ready(function() {

    $('.hiderstr').hover(function() {
        $(this).text($(this).data("hidden"));
        $(this).removeClass("hiderstr");
    }, function() {
        $(this).text($(this).text().replace(/\S/gi, '*'));
    });

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#hiascdi_console_form').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            HIASCDI.SendConsole();
        }
    });

    $('#update_hiascdi_broker').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            HIASCDI.UpdateCore();
        }
    });

    $('#update_hiascdi_form').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            HIASCDI.UpdateEntity();
        }
    });

    $("#GeniSysAI").on("click", "#reset_agent_apriv", function(e) {
        e.preventDefault();
        HIASCDI.ResetKey();
    });

    $("#GeniSysAI").on("click", "#reset_agent_mqtt", function(e) {
        e.preventDefault();
        HIASCDI.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_agent_amqp", function(e) {
        e.preventDefault();
        HIASCDI.ResetAMQP();
    });
});