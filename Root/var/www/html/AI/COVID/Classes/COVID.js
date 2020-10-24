var COVID = {
    Create: function() {
        $.post(window.location.href, $("#aml_classifier").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("aml_classifier");
                    $('.modal-title').text('COVID Classifier Devices');
                    $('.modal-body').html("HIAS COVID Classifier Device ID #" + resp.GDID + " created! Please save the API keys safely. The device's credentials are provided below. The credentials can be reset in the GeniSyAI Security Devices area.<br /><br /><strong>Device ID:</strong> " + resp.DID + "<br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain User:</strong> " + resp.BU + "<br /><strong>Blockchain Pass:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Device ID #" + resp.DID + " created!");
                    break;
                default:
                    msg = "COVID Create Failed: " + resp.Message
                    Logging.logMessage("Core", "COVID", msg);
                    $('.modal-title').text('COVID Classifier Devices');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#aml_classifier_update").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "Forms", "Device Update OK");
                    $('.modal-title').text('COVID Classifier Devices');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "COVID Update Failed: " + resp.Message
                    Logging.logMessage("Core", "COVID", msg);
                    $('.modal-title').text('COVID Classifier Devices');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    deleteData: function() {
        $.post(window.location.href, { "deleteData": 1 }, function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('#dataBlock').empty();
                    $('#dataBlock').html("<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><p>Please upload your test dataset.</p></div>");
                    break;
                default:
                    break;
            }
        });
    },
    prepareUploadForm: function() {

        var upper = document.querySelector('#dataup'),
            form = new FormData(),
            xhr = new XMLHttpRequest();

        form.append('uploadAllData', 1);

        upper.addEventListener('change', function(event) {
            event.preventDefault();

            var files = this.files;
            for (var i = 0, n = files.length; i < n; i++) {
                var file = files[i];

                form.append('alldata[]', file, file.name);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log(xhr.response)
                        var resp = jQuery.parseJSON(xhr.response);
                        if (resp.Response === "OK") {
                            $('#dataBlock').empty();
                            $('#dataBlock').html(resp.Data);
                            $('.modal-title').text('Data Upload OK');
                            $('.modal-body').text(resp.Message);
                            $('#responsive-modal').modal('show');
                            COVID.setOpacity();
                            Logging.logMessage("Core", "Forms", resp.Message);
                        } else {
                            Logging.logMessage("Core", "Forms", resp.Message);
                            $('.modal-title').text('Data Upload Failed');
                            $('.modal-body').text(resp.Message);
                            $('#responsive-modal').modal('show');
                        }
                    }
                }

                xhr.open('POST', '');
                xhr.send(form);
            }
        });
    },
    setOpacity: function() {
        $('.classify').css("opacity", "1.0");
        $('.classify').hover(function() {
                $(this).stop().animate({ opacity: 0.2 }, "fast");
            },
            function() {
                $(this).stop().animate({ opacity: 1.0 }, "fast");
            });
    },
    classify: function(im) {

        $('#imageView').html("<img src='../" + im + "' style='width: 100%;' />");
        $("#imName").text(im);
        var classification = '';
        $("#imClass").html("<strong>Diagnosis:</strong> WAITING FOR RESPONSE");
        $("#imResult").html("<strong>Result:</strong> WAITING FOR RESPONSE");
        $.post(window.location.href, { "classifyData": 1, "im": im }, function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    if (im.indexOf("Non-Covid") >= 0 && resp.Diagnosis == "Negative") {
                        classification = "True Negative";
                    } else if (im.indexOf("Non-Covid") >= 0 && resp.Diagnosis == "Positive") {
                        classification = "False Positive";
                    } else if (im.indexOf("Non-Covid") < 0 && resp.Diagnosis == "Positive") {
                        classification = "True Positive";
                    } else if (im.indexOf("Non-Covid") < 0 && resp.Diagnosis == "Negative") {
                        classification = "False Negative";
                    }
                    $("#imClass").html("<strong>Diagnosis:</strong> " + resp.Diagnosis);
                    if (resp.Confidence) {
                        $("#imConf").html("<strong>Confidence:</strong> " + resp.Confidence);
                    } else {
                        $("#imConf").hide();
                    }
                    $("#imResult").html("<strong>Result:</strong> " + classification);
                    break;
                default:
                    break;
            }
        });

    }
};
$(document).ready(function() {

    $('#aml_classifier').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            COVID.Create();
        }
    });

    $('#aml_classifier_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            COVID.Update();
        }
    });

    $("#GeniSysAI").on("click", "#uploadData", function(e) {
        e.preventDefault();
        $('#dataup').trigger('click');
    });

    $("#GeniSysAI").on("click", "#deleteData", function(e) {
        e.preventDefault();
        COVID.deleteData();
    });

    $("#GeniSysAI").on("click", ".classify", function(e) {
        e.preventDefault();
        COVID.classify($(this).attr("id"));
    });

});