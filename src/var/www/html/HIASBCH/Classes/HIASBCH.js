var HIASBCH = {
    logData: function(data) {
        $("#dataLog").prepend("- " + data + "<br />");
    },
    hideSecret: function() {
        $.each($('.hiderstr'), function() {
            $(this).data("hidden", $(this).text());
            $(this).text($(this).text().replace(/\S/gi, '*'));
        });
    },
    hideInputs: function() {
        $.each($('.hider'), function() {
            $(this).attr('type', 'password');
        });
    },
    accounts: [],
    connect: function(server) {
        if (typeof web3 !== 'undefined') {
            msg = 'Web3 Detected! ' + web3.currentProvider.constructor.name;
            Logging.logMessage("Core", "HIASBCH", msg);
            HIASBCH.logData(msg);
            window.web3 = new Web3(web3.currentProvider);
        } else {
            msg = "Connecting to HIASBCH using HTTP Provider...";
            Logging.logMessage("Core", "HIASBCH", msg);
            HIASBCH.logData(msg);
            window.web3 = new Web3(new Web3.providers.HttpProvider(server));
        }
    },
    isConnected: function() {
        return web3.eth.net.isListening();
    },
    getAccounts: async function() {
        await web3.eth.getAccounts(function(error, accounts) {
            HIASBCH.accounts = accounts;
        });
    },
    unlockAccount: async function(u, p, t) {
        addr = u.toString().toLowerCase().replace('0x', '');
        await web3.eth.personal.unlockAccount(web3.utils.toHex(addr), p, t)
            .then((response) => {
                msg = "Account unlocked!";
                Logging.logMessage("Core", "HIASBCH", msg);
                HIASBCH.logData(msg);
            }).catch((error) => {
                msg = "Account unlocking failed!";
                Logging.logMessage("Core", "HIASBCH", msg);
                HIASBCH.logData(msg);
            });
    },
    getBalance: function(address) {
        try {
            web3.eth.getBalance(address, function(error, wei) {
                if (!error) {
                    var balance = web3.utils.fromWei(wei, 'ether');
                    msg = "Your Balance: " + balance;
                    Logging.logMessage("Core", "HIASBCH", msg);
                    HIASBCH.logData(msg);
                }
            });
        } catch (err) {
            Logging.logMessage("Core", "HIASBCH", "Get balance failed! ERR: " + err);
        }
    },
    deployContract: async function(acc, p, name, abi, bin) {
        var acc = web3.utils.toHex(acc);
        var abi = JSON.parse(abi);
        var bin = web3.utils.toHex('0x' + bin);
        var rbin = bin;
        var txid = "";
        await web3.eth.personal.unlockAccount(acc, p, 1000)
            .then((response) => {
                HIASBCH.getBalance(acc);
                newContract = new web3.eth.Contract(abi);
                newContract.deploy({ data: bin })
                    .send({
                        from: acc,
                        gas: 8000000
                    })
                    .on('transactionHash', function(transactionHash) {
                        txid = transactionHash;
                    })
                    .then((newContractInstance) => {
                        newContract.options.address = newContractInstance.options.address
                        HIASBCH.storeContract(newContract.options.address, abi, rbin, acc, name, txid);
                    });
            }).catch((error) => {});
    },
    storeContract: function(address, abi, bin, acc, name, hash) {
        $.post(window.location.href, { "store_contract": 1, "id": address, "acc": acc, "abi": JSON.stringify(abi), "bin": bin, "name": name, "txid": hash }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    msg = resp.Message;
                    Logging.logMessage("Core", "HIASBCH", msg);
                    HIASBCH.logData(msg);
                    $("#name").val("");
                    $("#abi").val("");
                    $("#bin").val("");
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = resp.Message;
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "HIASBCH", msg);
                    HIASBCH.logData(msg);
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    replenish: async function(acc, p, address, abi, amount) {
        var address = web3.utils.toHex(address);
        var acc = web3.utils.toHex(acc);
        var abi = JSON.parse(abi);
        await web3.eth.personal.unlockAccount(acc, p, 1000)
            .then((response) => {
                msg = "HIASBCH account unlocked!";
                Logging.logMessage("Core", "HIASBCH", msg);
                HIASBCH.logData(msg);
                HIASBCH.getBalance(acc);
                web3.eth.handleRevert = true;
                msg = "Replenishing HIASBCH Contract Ether with 1000 Ether...";
                Logging.logMessage("Core", "HIASBCH", msg);
                HIASBCH.logData(msg);
                existingContract = new web3.eth.Contract(abi, address);
                existingContract.methods.deposit(web3.utils.toWei(amount, "ether")).send({
                        "to": address,
                        "from": acc,
                        "value": web3.utils.toWei(amount, "ether")
                    })
                    .then(receipt => {
                        msg = "HIASBCH Contract Ether replenished successfully!";
                        Logging.logMessage("Core", "HIASBCH", msg);
                        HIASBCH.logData(msg);
                        msg2 = "Receipt: " + JSON.stringify(receipt);
                        Logging.logMessage("Core", "HIASBCH", msg2);
                        HIASBCH.logData(msg2);
                        HIASBCH.getBalance(acc);
                        hash = receipt.transactionHash;
                        msg3 = "Txn: " + hash;
                    }).catch((error) => {
                        msg = "HIASBCH Contract Ether replenish failed!";
                        Logging.logMessage("Core", "HIASBCH", msg);
                        HIASBCH.logData(msg);
                        msg2 = "ERROR: " + JSON.stringify(error);
                        Logging.logMessage("Core", "HIASBCH", msg2);
                        HIASBCH.logData(msg2);
                        $('.modal-title').text('HIASBCH');
                        $('.modal-body').html(msg + "<br /><br />" + msg2);
                        $('#responsive-modal').modal('show');
                    });
            }).catch((error) => {
                console.log(error);
            });

    },
    interact: async function(acc, p, func, address, abi, type, data) {
        var address = web3.utils.toHex(address);
        var acc = web3.utils.toHex(acc);
        var abi = JSON.parse(abi);
        await web3.eth.personal.unlockAccount(acc, p, 1000)
            .then((response) => {
                msg = "HIASBCH account unlocked!";
                Logging.logMessage("Core", "HIASBCH", msg);
                HIASBCH.logData(msg);
                HIASBCH.getBalance(acc);
                for (var i = 0; i < data.length; i++) {
                    if (typeof data[i] == "object" || typeof data[i] == "array")
                        data[i] = JSON.stringify(data[i]);
                }
                if (!data.length) {
                    data = "";
                }
                web3.eth.handleRevert = true;
                existingContract = new web3.eth.Contract(abi, address, 2);
                if (type == "Send") {
                    msg = "Sending data to HIASBCH Contract function: " + func;
                    Logging.logMessage("Core", "HIASBCH", msg);
                    HIASBCH.logData(msg);
                    existingContract.methods[func](...data).send({ from: acc })
                        .then(receipt => {
                            msg = "Send to HIASBCH Contract function successful!";
                            Logging.logMessage("Core", "HIASBCH", msg);
                            HIASBCH.logData(msg);
                            msg2 = "Receipt: " + JSON.stringify(receipt);
                            Logging.logMessage("Core", "HIASBCH", msg2);
                            HIASBCH.logData(msg2);
                            HIASBCH.getBalance(acc);
                            hash = receipt.transactionHash;
                            msg3 = "Txn: " + hash;
                            $('.modal-title').text('HIASBCH');
                            $('.modal-body').html(msg + "<br /><br />" + msg2);
                            $('#responsive-modal').modal('show');
                        }).catch((error) => {
                            msg = "Contract function call failed!";
                            Logging.logMessage("Core", "HIASBCH", msg);
                            HIASBCH.logData(msg);
                            msg2 = "ERROR: " + JSON.stringify(error);
                            Logging.logMessage("Core", "HIASBCH", msg2);
                            HIASBCH.logData(msg2);
                            $('.modal-title').text('HIASBCH');
                            $('.modal-body').html(msg + "<br /><br />" + msg2);
                            $('#responsive-modal').modal('show');
                        });
                } else {
                    msg = "Calling HIASBCH Contract function: " + func;
                    Logging.logMessage("Core", "HIASBCH", msg);
                    HIASBCH.logData(msg);
                    existingContract.methods[func](...data).call({ from: acc })
                        .then(response => {
                            msg = "Call to HIASBCH Contract function successful!";
                            Logging.logMessage("Core", "HIASBCH", msg);
                            HIASBCH.logData(msg);
                            msg2 = "Receipt: " + response;
                            Logging.logMessage("Core", "HIASBCH", msg2);
                            HIASBCH.logData(msg2);
                            HIASBCH.getBalance(acc);
                            $('.modal-title').text('HIASBCH');
                            $('.modal-body').html(msg + "<br /><br />" + msg2);
                            $('#responsive-modal').modal('show');
                        }).catch((error) => {
                            msg = "Contract function call failed!";
                            Logging.logMessage("Core", "HIASBCH", msg);
                            HIASBCH.logData(msg);
                            msg2 = "ERROR: " + JSON.stringify(error);
                            Logging.logMessage("Core", "HIASBCH", msg2);
                            HIASBCH.logData(msg2);
                            $('.modal-title').text('HIASBCH');
                            $('.modal-body').html(msg + "<br /><br />" + msg2);
                            $('#responsive-modal').modal('show');
                        });
                }
            }).catch((error) => {
                console.log(error);
            });
    },
    transfer: function() {
        $.post(window.location.href, $("#send").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').html(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Contract deployment failed: "
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "HIASBCH", msg);
                    HIASBCH.logData(msg);
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    checkDataIntegrity: function(elem) {
        var abi = JSON.parse($("#abi").html());
        var acc = web3.utils.toHex(elem.data("user"));
        var address = $("#address").html();
        web3.eth.handleRevert = true
        existingContract = new web3.eth.Contract(abi, address, 2);
        existingContract.methods.getHash(elem.data("key")).call({ from: acc })
            .then(response => {
                msg = "Call to HIASBCH Contract function successful!";
                Logging.logMessage("Core", "HIASBCH", msg);
                msg2 = "Receipt: " + response;
                Logging.logMessage("Core", "HIASBCH", msg2);
                hashed = web3.utils.hexToAscii(response.dataHash)
                HIASBCH.compareHash(elem.data("hash"), hashed);
            }).catch((error) => {
                msg = "Contract function call failed!";
                Logging.logMessage("Core", "HIASBCH", msg);
                msg2 = "ERROR: " + error.reason;
                Logging.logMessage("Core", "HIASBCH", msg2);
                $('.modal-title').text('HIASBCH');
                $('.modal-body').html(msg + "<br /><br />" + msg2);
                $('#responsive-modal').modal('show');
            });
    },
    compareHash: function(current, hash) {
        $.post(window.location.href, { "check_hash": 1, "current": current, "hash": hash }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            checked = resp.Check === true ? "PASSED!" : "FAILED!"
            $('.modal-title').text('HIASBCH');
            $('.modal-body').html("Data Integrtity Check: " + checked + "<br /><br />Data String: " + resp.String + "<br />Data Hash: " + resp.Hash);
            $('#responsive-modal').modal('show');
        });
    },
    updateConfig: function() {
        $.post(window.location.href, $("#blockchain_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    msg = "HIASBCH settings updated!"
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "HIASBCH", msg);
                    break;
                default:
                    msg = "HIASBCH settings update failed: "
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "HIASBCH", msg);
                    break;
            }
        });
    },
    updateEntity: function() {
        $.post(window.location.href, $("#update_hiasbch_form").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    msg = "HIASBCH entity updated!"
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "HIASBCH", msg);
                    break;
                default:
                    msg = "HIASBCH entity update failed: "
                    $('.modal-title').text('HIASBCH');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "HIASBCH", msg);
                    break;
            }
        });
    },
    resetKey: function() {
        $.post(window.location.href, { "reset_hiasbch_key": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "HIASBCH", "Reset OK");
                        $('.modal-title').text('Reset App Key');
                        $('.modal-body').text("This agent's new key is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "HIASBCH", msg);
                        $('.modal-title').text('Reset App Key');
                        $('.modal-body').text(msg);
                        $('#responsive-modal').modal('show');
                        break;
                }
            });
    },
    resetMqtt: function() {
        $.post(window.location.href, { "reset_hiasbch_mqtt": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Reset OK");
                    $("#amqttp").text(resp.P.replace(/\S/gi, '*'));
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
    },
    resetAmqp: function() {
        $.post(window.location.href, { "reset_hiasbch_amqp": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Reset OK");
                    $('.modal-title').text('Reset AMQP Password');
                    $('.modal-body').text("The new AMQP password is: " + resp.P);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Reset failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Reset AMQP Password');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    updateLifeGraph: function() {
        $.post(window.location.href, { "update_hiasbch_life_graph": 1, "deviceGraphs": $("#deviceGraphs").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            if (resp[0].length > 0) {
                hiasbch_stats.setOption({
                    xAxis: {
                        type: 'category',
                        axisLabel: {
                            textStyle: {
                                color: '#ffffff'
                            },
                            interval: 1,
                            rotate: 45
                        },
                        data: resp[0]
                    },
                    series: resp[1]
                })
            } else {
                hiasbch_stats.clear()
            }
        });
    },
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

    $('#update_hiasbch_form').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            HIASBCH.updateEntity();
        }
    });

    $("#GeniSysAI").on("click", "#contract_deploy", function(e) {
        e.preventDefault();
        HIASBCH.deployContract($("#acc").val(), $("#p").val(), $("#name").val(), $("#abi").val(), $("#bin").val());
    });
    $("#GeniSysAI").on("click", "#interact", function(e) {
        e.preventDefault();
        if ($("#func").val() === "initiate") {
            acc = $("#hacc").val();
            p = $("#hp").val();
        } else {
            acc = $("#hacc").val();
            p = $("#hp").val();
        }
        HIASBCH.interact(acc, p, $("#func").val(), $("#contract").val(), $("#abi").val(), $("input[name='type']:checked").val(), $("#data").val() ? jQuery.parseJSON($("#data").val()) : {});
    });
    $("#GeniSysAI").on("click", "#console_interact", function(e) {
        e.preventDefault();
        HIASBCH.interact($("#acc").val(), $("#p").val(), $("#func").val(), $("#contract").val(), $("#abi").val(), $("input[name='type']:checked").val(), $("#data").val() ? jQuery.parseJSON($("#data").val()) : {});
    });
    $("#GeniSysAI").on("click", "#update_blockchain", function(e) {
        e.preventDefault();
        HIASBCH.updateConfig();
    });
    $("#GeniSysAI").on("click", "#replenish", function(e) {
        e.preventDefault();
        HIASBCH.replenish($("#hacc").val(), $("#hp").val(), $("#contract").val(), $("#abi").val(), "1000");
    });
    $("#GeniSysAI").on("click", "#transfer_ether", function(e) {
        e.preventDefault();
        HIASBCH.transfer();
    });
    $("#GeniSysAI").on("click", ".verify", function(e) {
        e.preventDefault();
        HIASBCH.checkDataIntegrity($(this));
    });
    $("#GeniSysAI").on("click", "#reset_hiasbch_key", function(e) {
        e.preventDefault();
        HIASBCH.resetKey();
    });
    $("#GeniSysAI").on("click", "#reset_hiasbch_mqtt", function(e) {
        e.preventDefault();
        HIASBCH.resetMqtt();
    });
    $("#GeniSysAI").on("click", "#reset_hiasbch_amqp", function(e) {
        e.preventDefault();
        HIASBCH.resetAmqp();
    });
});