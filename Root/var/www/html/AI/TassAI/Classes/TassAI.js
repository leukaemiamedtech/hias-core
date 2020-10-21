var GeniSysAI = {
    Create: function() {
        $.post(window.location.href, $("#genisysai_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("genisysai_create");
                    $('.modal-title').text('GeniSyAI Security Devices');
                    $('.modal-body').html("HIAS GeniSyAI Security Device ID #" + resp.GDID + " created! Please save the API keys safely. The device's credentials are provided below. The credentials can be reset in the GeniSyAI Security Devices area.<br /><br /><strong>Device ID:</strong> " + resp.DID + "<br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain User:</strong> " + resp.BU + "<br /><strong>Blockchain Pass:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Device ID #" + resp.DID + " created!");
                    break;
                default:
                    msg = "GeniSysAI Create Failed: " + resp.Message
                    Logging.logMessage("Core", "GeniSysAI", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#genisysai_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "Forms", "Device Update OK");
                    $('.modal-title').text('Device Update');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "GeniSysAI Update Failed: " + resp.Message
                    Logging.logMessage("Core", "GeniSysAI", msg);
                    break;
            }
        });
    }
};
$(document).ready(function() {

    $('#genisysai_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            GeniSysAI.Create();
        }
    });

    $('#genisysai_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            GeniSysAI.Update();
        }
    });

});