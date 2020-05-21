var COVID19 = {
    updateCOVID: function() {
        $.post(window.location.href, { "updateCOVID": 1 }, function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('Data Updated');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    setTimeout(function() {
                        location.reload(true);
                    }, 5000);
                    break;
                default:
                    msg = "Create failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Data Update Failed');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
};
$(document).ready(function() {

    $("#GeniSysAI").on("click", "#updateCOVID", function(e) {
        e.preventDefault();
        COVID19.updateCOVID();
    });

});