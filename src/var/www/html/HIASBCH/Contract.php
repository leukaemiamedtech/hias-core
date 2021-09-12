<?php session_start();

$pageDetails = [
    "PageID" => "HIASBCH",
    "SubPageID" => "HIASBCH",
    "LowPageID" => "Contracts"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$cb = $HIAS->hiasbch->get_hiasbch();
$contracts = $cb["contracts"]["value"];

$contract = $contracts[filter_input(INPUT_GET, "index", FILTER_SANITIZE_NUMBER_INT)];

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="robots" content="noindex, nofollow" />

        <title><?=$HIAS->confs["meta_title"]; ?></title>
        <meta name="description" content="<?=$HIAS->confs["meta_description"]; ?>" />
        <meta name="keywords" content="" />
        <meta name="author" content="hencework"/>

        <script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

        <link type="image/x-icon" rel="icon" href="/img/favicon.png" />
        <link type="image/x-icon" rel="shortcut icon" href="/img/favicon.png" />
        <link type="image/x-icon" rel="apple-touch-icon" href="/img/favicon.png" />

        <link href="/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
        <link href="/dist/css/style.css" rel="stylesheet" type="text/css">
        <link href="/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
    </head>
    <body id="GeniSysAI">

        <div class="preloader-it">
            <div class="la-anim-1"></div>
        </div>

        <div class="wrapper theme-6-active pimary-color-pink">

            <?php include dirname(__FILE__) . '/../Includes/Nav.php'; ?>
            <?php include dirname(__FILE__) . '/../Includes/LeftNav.php'; ?>
            <?php include dirname(__FILE__) . '/../Includes/RightNav.php'; ?>

            <div class="page-wrapper">
                <div class="container-fluid pt-25">

                    <?php include dirname(__FILE__) . '/../Includes/Stats.php'; ?>

                    <div class="row">
                        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel panel-default card-view panel-refresh">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <?php include dirname(__FILE__) . '/../Includes/Weather.php'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel panel-default card-view">
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <?php include dirname(__FILE__) . '/../iotJumpWay/Includes/iotJumpWay.php'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel panel-default card-view panel-refresh">
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark">HIASBCH Smart Contract <?=filter_input(INPUT_GET,"contract",FILTER_SANITIZE_STRING); ?></h6>
                                        <p><strong>Name: </strong> <?=$contract["name"]; ?></p>
                                        <p><strong>Txn ID: </strong> <?=$contract["transaction"]; ?></p>
                                    </div>
                                    <div class="pull-right"><button type="submit" class="btn btn-success btn-anim" id="replenish"><i class="icon-rocket"></i><span class="btn-text">Replenish</span></button></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <p>Before you can deploy a contract, you need to write the contract and compile it using solc. Once you have your compiled smart contract use the contents of the YourSmartContract.abi and YourSmartContract.bin files in the form below. Below is an example of compiling a smart contract written in Solidity.</p>
                                            <p>&nbsp;</p>

                                            <p>solc --abi /hias/ethereum/contracts/HIAS-3.sol -o /hias/ethereum/contracts/build --overwrite</p>
                                            <p>solc --bin /fserver/ethereum/contracts/HIAS-3.sol -o /fserver/ethereum/contracts/build --overwrite</p>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default card-view panel-refresh">
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <div class="form-wrap">

                                            <form data-toggle="validator" role="form" id="send">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Interaction Function</label>
                                                            <input type="text" class="form-control" id="func" name="func" placeholder="Interaction Endpoint" required value="">
                                                            <span class="help-block">Contact function to send the below data to</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Call Type</label>
                                                            Send <input type="radio" id="type" name="type" value="Send" checked> Call <input type="radio" id="type" name="type" value="Call">
                                                            <span class="help-block">Whether the call is to "Send" or "Call" to a Contract Function</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Data To Send</label>
                                                            <textarea class="form-control" id="data" name="data" placeholder="Contract data as JSON Array"  rows="12"></textarea>
                                                            <span class="help-block">Contract data as JSON Array, only required if the function you have provided above has parameters.</span>
                                                        </div>
                                                        <div class="form-group mb-0">
                                                            <input type="hidden" id="contract" name="contract" value="<?=$contract["contract"]; ?>">
                                                            <input type="hidden" id="name" name="name" value="<?=$contract["name"]; ?>">
                                                            <input type="hidden" id="hacc" name="hacc"  value="<?=$HIAS->hiasbch->un; ?>">
                                                            <input type="hidden" id="hp" name="hp" value="<?=$HIAS->hiasbch->up; ?>">
                                                            <input type="hidden" id="acc" name="acc"  value="<?=$_SESSION["HIAS"]["BC"]["BCUser"]; ?>">
                                                            <input type="hidden" id="p" name="p" value="<?=$HIAS->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]); ?>">
                                                            <input type="hidden" id="contract" name="contract" value="<?=$contract["contract"]; ?>">
                                                            <button type="submit" class="btn btn-success btn-anim" id="interact"><i class="icon-rocket"></i><span class="btn-text">Interact</span></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Application Binary Interface</label>
                                                            <textarea class="form-control" id="abi" name="abi" placeholder="Application Binary Interface" required rows="12"><?php print_r(json_encode($contract["abi"], JSON_PRETTY_PRINT)); ?></textarea>
                                                            <span class="help-block">Application Binary Interface</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div><br />
                            <div class="panel panel-default card-view panel-refresh">
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark">Contract History</h6>
                                    </div>
                                    <div class="pull-right"></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <div class="scroll_450px">
                                            <div class="table-wrap mt-40">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <tbody id="contractHistoryContainer">

                                                        <?php
                                                            $userDetails = "";
                                                            $history = $HIAS->hiasbch->get_transactions(100, "", filter_input(INPUT_GET,"contract",FILTER_SANITIZE_STRING));
                                                            if(isSet($history["ResponseData"])):
                                                                foreach($history["ResponseData"] as $key => $value):
                                                                    $block = $HIAS->hiasbch->get_block($value["BlockHash"]);
                                                        ?>

                                                        <tr>
                                                            <td><a href="/HIASBCH/Explorer/Block/<?=$value["BlockHash"]; ?>" title="<?=$value["BlockHash"]; ?>"><?=$value["BlockNumber"]; ?></a><br /></td>
                                                            <td>

                                                                <div class="row">
                                                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Hash:</div>
                                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                        <a href="/HIASBCH/Explorer/Transaction/<?=$value["Hash"]; ?>" title="<?=$value["Hash"]; ?>"><?=$value["Hash"]; ?></a>

                                                                    </div>
                                                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Fee:</div>
                                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                        <?=$value["Gas"] * $value["GasPrice"]; ?>

                                                                    </div>
                                                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">From:</div>
                                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                        <a href="/HIASBCH/Explorer/Address/<?=$value["From"]; ?>" title="<?=$value["From"]; ?>"><?=$value["From"]; ?></a>

                                                                    </div>
                                                                    <div class="col-lg-1  col-md-12 col-sm-12 col-xs-12">To:</div>
                                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                    <?php if($value["To"]): ?>
                                                                        <a href="/HIASBCH/Explorer/Address/<?=$value["To"]; ?>" title="<?=$value["To"]; ?>"><?=$value["To"]; ?></a>
                                                                    <?php else: ?>
                                                                        Contract Creation
                                                                    <?php endif; ?>

                                                                    </div>
                                                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">At:</div>
                                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                        <?=date("Y-m-d H:i:s", hexdec($block["Data"]->timestamp)); ?> (<span style="font-size: 8;"><?=($HIAS->helpers->time_ago(time()-hexdec($block["Data"]->timestamp))); ?> ago</span>)

                                                                    </div>
                                                                </div>

                                                            </td>
                                                        </tr>

                                                        <?php
                                                                endforeach;
                                                            endif;
                                                        ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel panel-default card-view">
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <h6>Contract Details</h6>

                                        <div class="row">
                                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">Address:</div>
                                            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12"><span class="hiderstr"><?=$contract["contract"]; ?></span></div>
                                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">HIAS ETHER:</div>
                                            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12"><span style="font-size: 8px;"><?=$HIAS->hiasbch->check_balance($contract["contract"]); ?></span></div>
                                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">Authorized User:</div>
                                            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12"><span class="hiderstr"><?=$HIAS->hiasbch->un; ?></span></div>
                                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">HIAS ETHER:</div>
                                            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12"><span style="font-size: 8px;"><?=$HIAS->hiasbch->check_balance($HIAS->hiasbch->un); ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default card-view">
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <h6>Your Details</h6>

                                        <div class="row">
                                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">Address:</div>
                                            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12"><span class="hiderstr"><?=$_SESSION["HIAS"]["BC"]["BCUser"]; ?></span></div>
                                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">HIAS ETHER:</div>
                                            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12"><span style="font-size: 8px;"><?=$HIAS->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"]); ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default card-view">
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <div id="dataLog" style="border: 0px solid; height: 385px; overflow: scroll; padding: 5px; color: #fff; font-size: 10px; overflow-x: hidden;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

        </div>

        <?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

        <script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
        <script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
        <script type="text/javascript" src="/HIASBCH/Classes/HIASBCH.js"></script>
        <script type="text/javascript" src="/HIASBCH/Classes/web3.js"></script>
        <script type="text/javascript">
            HIASBCH.hideSecret();
            window.addEventListener('load', function () {
                HIASBCH.connect("/hiasbch/api/");
                if(HIASBCH.isConnected()){
                    msg = "Connected to HIASBCH!";
                    Logging.logMessage("Core", "HIASBCH", msg);
                    HIASBCH.logData(msg);
                } else {
                    msg = "Connection to HIASBCH failed!";
                    Logging.logMessage("Core", "HIASBCH", msg);
                    HIASBCH.logData(msg);
                }
            });
        </script>
    </body>
</html>
