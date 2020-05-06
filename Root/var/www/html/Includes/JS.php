        <div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h5 class="modal-title"></h5>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
        
        <script type="text/javascript" src="<?=$_GeniSys->_helpers->odecrypt($_GeniSys->_confs["domainString"]); ?>/vendors/bower_components/jquery/dist/jquery.min.js"></script>
        <script type="text/javascript" src="<?=$_GeniSys->_helpers->odecrypt($_GeniSys->_confs["domainString"]); ?>/vendors/bower_components/bootstrap/dist/js/bootstrap.js"></script>
        <script type="text/javascript" src="<?=$_GeniSys->_helpers->odecrypt($_GeniSys->_confs["domainString"]); ?>/vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
        <script type="text/javascript" src="<?=$_GeniSys->_helpers->odecrypt($_GeniSys->_confs["domainString"]); ?>/dist/js/jquery.slimscroll.js"></script>
        <script type="text/javascript" src="<?=$_GeniSys->_helpers->odecrypt($_GeniSys->_confs["domainString"]); ?>/dist/js/init.js"></script>

        <script type="text/javascript" src="<?=$_GeniSys->_helpers->odecrypt($_GeniSys->_confs["domainString"]); ?>/GeniSysAI/Media/JS/GeniSysAi.js"></script>
        <script type="text/javascript" src="<?=$_GeniSys->_helpers->odecrypt($_GeniSys->_confs["domainString"]); ?>/GeniSysAI/Media/JS/Logging.js"></script>
        <script type="text/javascript" src="<?=$_GeniSys->_helpers->odecrypt($_GeniSys->_confs["domainString"]); ?>/GeniSysAI/Media/JS/Validation.js"></script>

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>