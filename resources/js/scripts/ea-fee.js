import Sha1 from '../../vendor/ring-signature/sha1';
//import * as all from '../../vendor/bitcoinjs/bitcoinjs-min';
import Swal from 'sweetalert2/dist/sweetalert2'
/**
 * EA Fee
 * @author  TxChrixo
 */
$(function () {

    $.getJSON("/api/blockio/get_address_balance", { address: $(".bitcoin-address").val() }, function (result) {
        if (result.success === true) {
            $(".number-btc").html(result.data.data.available_balance);
            $(".number-network").html(result.data.data.network);
        } else {
            $(".number-btc").html("Unconfigured");
            $(".number-network").html("Unconfigured");
        }
    });

    $.getJSON("https://chain.so/api/v2/get_tx_unspent/BTCTEST/" + $(".bitcoin-address").val(), function (result) {
        console.log(result);
        if (result.status === "success") {
            $(".number-unspent").html(result.data.txs.length);

        } else {
            $(".number-unspent").html("Unconfigured");
        }
    });

    var txaddress = [];
    var txid = [];
    var pubkeyhash = [];
    var wif = $('.bitcoin-wif').val();

    var currentid = $(".input-election").val();
    loadBlockchain(currentid);
    getPubKeysHash(currentid);


    $(".input-election").on('change', function () {
        loadBlockchain($(this).val());
        getPubKeysHash($(this).val());
    });

    $(".btn-pay-now").on('click', function () {
        payBitcoin();
    })


    function getPubKeysHash(id) {
        var data = { election_id: id };
        pubkeyhash = [];
        $.getJSON("/api/getAllPubKeys", data, function (result) {
            if (result.success == true) {
                console.log(result.message);
                $(result.data).each(function (i, item) {
                    pubkeyhash.push(Sha1.hash(item.public_key));
                });
            }
        });
    }


    function loadBlockchain(id) {
        var data = { election_id: id };
        $.getJSON("/api/allbitcoinaddress", data, "json").done(function (result) {
            var str = '';
            txaddress = [];
            txid = [];
            $(".number-addresses").html(result.data.length);
            var div = $('<div>');
            console.log(result);
            $(result.data).each(function (i, item) {
                if (item.is_paid == 0) {
                    txaddress.push(item.bitcoin_address);
                    txid.push(item.id);
                }
                var btn = $('<button>').addClass(`btn btn-primary rounded-3 btn-pay-${item.id}`)
                    .attr({ 'data-id': item.id, 'data-bitcoin-address': item.bitcoin_address, 'id': 'btn-single-pay' })
                    .text('Load');
                var tr = $('<tr>').append($('<td>').append(item.id))
                tr.append($('<td>').append(item.bitcoin_address));
                tr.append($('<td>').append(btn));
                div.append(tr);
            });
            if (div.html().trim() === '') {
                div.html("<tr><td class='p-3 w-100 text-center' colspan='3'>No data available.</td></tr>");
            }
            $(".table-fee").html(div.html());
        }).fail(function (error, textStatus, errorThrown) {
            if (error.status === 422) {
                console.error('Erreur 422 :', error.responseJSON.message);
            } else if (error.status === 401) {
                swal({
                    title: errorThrown,
                    text: error.responseJSON.data,
                    icon: "error",
                    showCancelButton: false,
                    closeOnConfirm: false
                });

            } else {
                swal({
                    title: textStatus,
                    text: errorThrown,
                    icon: "error",
                    showCancelButton: false,
                    closeOnConfirm: false
                });
                console.error('Error ' + error.status + ': ' + textStatus + ' - ' + errorThrown);
            }
        });
    }



    function payBitcoin() {
        if (pubkeyhash.length > txaddress) {
            swal("Oops..", "The length of public key hash and address does not match", "error");
        } else if (pubkeyhash.length < txaddress.length) {
            console.log('Padding the hash...');
            for (var j = pubkeyhash.length - 1; j < txaddress.length; j++) {
                pubkeyhash[j] = '';
            }
            payBitcoin();
        } else {
            if (txaddress.length === txid.length) {
                wif = $('.bitcoin-wif').val();
                network = Bitcoin.networks.testnet;
                keyPair = Bitcoin.ECPair.fromWIF(wif, network);
                bitcoinAddress = keyPair.getAddress();
                lock = false;
                for (var i = 0; i < txaddress.length; i++) {
                    (function (i) {
                        setTimeout(function () {
                            makeTransaction(txid[i], txaddress[i], pubkeyhash[i]);
                        }, i * 10000);
                    })(i);
                }
            } else {
                swal("Oops..", "The length of address id and address does not match", "error");
            }
        }
    }
    function payBitcoin() {
        if (pubkeyhash.length > txaddress.length) {
            swal("Oops..", "The length of public key hash and address does not match", "error");
        } else if (pubkeyhash.length < txaddress.length) {
            console.log('Padding the hash...');
            for (var j = pubkeyhash.length - 1; j < txaddress.length; j++) {
                pubkeyhash[j] = '';
            }
            payBitcoin();
        } else {
            if (txaddress.length === txid.length) {
                wif = $('.bitcoin-wif').val();
                network = Bitcoin.networks.testnet;
                keyPair = Bitcoin.ECPair.fromWIF(wif, network);
                bitcoinAddress = keyPair.getAddress();
                lock = false;
                for (var i = 0; i < txaddress.length; i++) {
                    (function (i) {
                        setTimeout(function () {
                            makeTransaction(txid[i], txaddress[i], pubkeyhash[i]);
                        }, i * 10000);
                    })(i);
                }
            } else {
                swal("Oops..", "The length of address id and address does not match", "error");
            }
        }
    }

    function makeTransaction(election_id, address, publicKeyHash) {
        if (!lock) {
            $.getJSON("https://block.io/api/v2/prepare_transaction", {
                api_key: "REDACTED_BLOCKIO_KEY",
                from_addresses: bitcoinAddress,
                to_addresses: address,
                amounts: "0.0001" // Change this amount as needed
            }, function (result) {
                lock = true;
                if (result.status === "success") {
                    var txHex = result.data.tx_hex;
                    let data = { tx_hex: txHex };
                    postTransaction(election_id, data);
                } else {
                    swal("Oops..", "Failed to prepare the transaction.", "error");
                    lock = false;
                }
            });
            return true;
        }
    }


    function postTransaction(election_id, data) {
        $.post("https://block.io/api/v2/submit_transaction", { api_key: "REDACTED_BLOCKIO_KEY", transaction_data: JSON.stringify(data) }, function (result) {
            if (result.status === "success") {
                $(".status-" + election_id).html(result.data.txid);
            } else {
                swal("Oops..", "Failed to post the transaction.", "error");
            }
            lock = false;
        });
    }


    $(".table-fee").on('click', '#btn-single-pay', function () {
        // The click event will be triggered for dynamically added elements with the class btn-load-balance
        let id = $(this).data('id');
        let address = $(this).data('bitcoinAddress');
        console.log(id, address);

        singlepay(id, address);
    });

    function singlepay(election_id, address) {
        Swal.fire({
            title: "OP_RETURN",
            text: "Write the OP_RETURN code, normally the hash of the public key",
            input: "text",
            inputAttributes: {
                placeholder: "Write the OP_RETURN code"
            },
            showCancelButton: true,
            confirmButtonText: "Confirm",
            showLoaderOnConfirm: true,
            preConfirm: (OP_RETURN) => {
                wif = $('.bitcoin-wif').val();
                network = Bitcoin.networks.testnet;
                keyPair = Bitcoin.ECPair.fromWIF(wif, network);
                bitcoinAddress = keyPair.getAddress();
                lock = false;
                Swal("Done!", "Success", "success");
                if (makeTransaction(election_id, address, inputValue)) {
                    Swal("Done!", "Success", "success");
                }


            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            console.log('result', result);
            if (result.isConfirmed) {
                Swal.fire({
                    title: result.value.data.message,
                    text: prefixToggle(result.value.data.stage),
                    icon: "success",
                })
            }
        })
    }

});


