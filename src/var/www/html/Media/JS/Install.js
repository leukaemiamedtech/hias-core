var Install = {
    finalize: function() {
        $.post(window.location.href, $("#Install").serialize(), function(resp) {
            console.log(resp);
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    msg = resp.Message
                    Logging.logMessage("Core", "Forms", "Location Update OK");
                    $('.modal-title').text('Installation Finalize.');
                    $('.modal-body').html(msg);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Installation Finalize');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
};

$('#Install').validator().on('submit', function(e) {
    if (!e.isDefaultPrevented()) {
        e.preventDefault();
        Install.finalize();
    }
});