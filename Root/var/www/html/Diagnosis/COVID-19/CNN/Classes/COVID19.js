var COVID19 = {
    deleteData: function() {
        $.post(window.location.href, { "deleteData": 1 }, function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('#dataBlock').empty();
                    $('#dataBlock').html("<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><p>Please upload your SARS-COV-2 Ct-Scan Dataset data.</p></div>");
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

        form.append('uploadCovData', 1);

        upper.addEventListener('change', function(event) {
            event.preventDefault();

            var files = this.files;
            for (var i = 0, n = files.length; i < n; i++) {
                var file = files[i];

                form.append('covdata[]', file, file.name);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var resp = jQuery.parseJSON(xhr.response);
                        if (resp.Response === "OK") {
                            $('#dataBlock').empty();
                            $('#dataBlock').html(resp.Data);
                            COVID19.setOpacity();
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

        $('#imageView').html("<img src='" + im + "' style='width: 100%;' />");
        $("#imName").text(im);
        var classification = '';
        $.post(window.location.href, { "classifyData": 1, "im": im }, function(resp) {
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
                    $("#imConf").html("<strong>Confidence:</strong> " + resp.Confidence);
                    $("#imResult").html("<strong>Result:</strong> " + classification);
                    break;
                default:
                    break;
            }
        });

    }
};
$(document).ready(function() {

    $("#GeniSysAI").on("click", "#uploadData", function(e) {
        e.preventDefault();
        $('#dataup').trigger('click');
    });

    $("#GeniSysAI").on("click", "#deleteData", function(e) {
        e.preventDefault();
        COVID19.deleteData();
    });

    $("#GeniSysAI").on("click", ".classify", function(e) {
        e.preventDefault();
        COVID19.classify($(this).attr("id"));
    });

    COVID19.setOpacity();
    COVID19.prepareUploadForm();

});