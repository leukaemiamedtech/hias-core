var AI = {
    Create: function() {
        $.post(window.location.href, $("#ai_model").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    HIAS.ResetForm("ai_model");
                    $('.modal-title').text('AI Models');
                    $('.modal-body').html("HIAS AI Model created!");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "HIAS AI Model created!");
                    break;
                default:
                    msg = "AI Model Create Failed: " + resp.Message
                    Logging.logMessage("Core", "AI", msg);
                    $('.modal-title').text('AI Models');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#model_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "AI", resp.Message);
                    $('.modal-title').text('AI Models');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "AI Update Failed: " + resp.Message
                    Logging.logMessage("Core", "AI", msg);
                    $('.modal-title').text('AI Models');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
};
$(document).ready(function() {

    $("#GeniSysAI").on("click", ".removeModelProperty", function(e) {
        e.preventDefault();
        $('#model-property-' + $(this).data('id')).fadeOut(300, function() { $(this).remove(); });
    });

    $("#GeniSysAI").on("click", ".removeModelCommand", function(e) {
        e.preventDefault();
        $('#model-command-' + $(this).data('id')).fadeOut(300, function() { $(this).remove(); });
    });

    $("#GeniSysAI").on("click", ".removeModelState", function(e) {
        e.preventDefault();
        $('#model-state-' + $(this).data('id')).fadeOut(300, function() { $(this).remove(); });
    });

    $("#GeniSysAI").on("click", "#addModelProperty", function(e) {
        e.preventDefault();
        $('.modal-title').text('Add Property');
        $('.modal-footer button').text('OK');
        $('#buttonId').button('option', 'label', 'OK');
        $('.modal-body').html("<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>Property: </div><div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'><input type ='text' id='addPropertyKey' class='form-control' /></div></div>");
        $('#responsive-modal').modal('show');
        $('#responsive-modal').on('hide.bs.modal', function() {
            if ($("#addPropertyKey").val()) {
                var addProperty = '<div class="row" style="margin-bottom: 5px;" id = "model-property-' + $("#addPropertyKey").val() + '"><div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"><input type="text" class="form-control" id="properties[]" name="properties[]" placeholder="' + $("#addPropertyKey").val() + '" value="' + $("#addPropertyKey").val() + '" required></div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><a href="javascript:void(0);" class="removeModelProperty" data-id="' + $("#addPropertyKey").val() + '"><i class="fas fa-trash-alt"></i></a></div></div>';
                $("#propertyContent").append(addProperty);
                $('.modal-body').html("");
            }
        })
    });

    $("#GeniSysAI").on("click", "#addModelCommand", function(e) {
        e.preventDefault();
        $('.modal-title').text('Add Command');
        $('.modal-footer button').text('OK');
        $('#buttonId').button('option', 'label', 'OK');
        $('.modal-body').html("<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>Command Name: </div><div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'><input type ='text' id='addCommandKey' class='form-control' /></div></div><div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>Commands: </div><div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'><input type ='text' id='addCommandValue' class='form-control' /></div></div>");
        $('#responsive-modal').modal('show');
        $('#responsive-modal').on('hide.bs.modal', function() {
            if ($("#addCommandKey").val() && $("#addCommandValue").val()) {
                var addCommand = '<div class= "row" style="margin-bottom: 5px;" id="model-command-' + $("#addCommandKey").val() + '"><div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"><strong>' + $("#addCommandKey").val() + '</strong><input type="text" class="form-control" name="commands[' + $("#addCommandKey").val() + ']" placeholder="Commands as comma separated string" value="' + $("#addCommandValue").val() + '" required></div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><br /><a href="javascript:void(0);" class="removeModelCommand" data-id="' + $("#addCommandKey").val() + '"><i class="fas fa-trash-alt"></i></a></div></div>';
                $("#commandsContent").append(addCommand);
                $('.modal-body').html("");
            }
        })
    });

    $("#GeniSysAI").on("click", "#addModelState", function(e) {
        e.preventDefault();
        $('.modal-title').text('Add State');
        $('.modal-footer button').text('OK');
        $('#buttonId').button('option', 'label', 'OK');
        $('.modal-body').html("<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>State Value: </div><div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'><input type ='text' id='addStateValue' class='form-control' /></div></div>");
        $('#responsive-modal').modal('show');
        $('#responsive-modal').on('hide.bs.modal', function() {
            if ($("#addStateValue").val()) {
                var key = (parseInt($("#lastState").text()) + 1);
                var addState = '<div class="row" style="margin-bottom: 5px;" id="model-state-' + key + '"><div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"><input type="text" class="form-control" name="states[]" placeholder="State" value="' + $("#addStateValue").val() + '" required /></div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><a href="javascript:void(0);" class="removeModelState" data-id="' + key + '"><i class="fas fa-trash-alt"></i></a></div></div >';
                $("#stateContent").append(addState);
                $('.modal-body').html("");
                $("#lastState").text(key);
            }
        })
    });

    $('#ai_model').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            AI.Create();
        }
    });

    $('#model_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            AI.Update();
        }
    });

});