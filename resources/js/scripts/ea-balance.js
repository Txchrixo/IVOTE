/**
 * EA Balance
 * @author  TxChrixo
 */
$(function () {
    var id = $(".input-election").val();
    var txid = [];
    var txaddress = [];
    loadBlockchain(id);

    $(".input-election").on('change', function () {
        loadBlockchain($(this).val());
    });

    $(".btn-load-all-balance").on('click', function () {

        for (var i = 0; i < txid.length; i++) {
            (function (i) {
                setTimeout(function () {
                    loadBalance(txid[i], txaddress[i]);
                }, i * 1800);
            })(i);
        }
    });

    $(".table-balance").on('click', '#btn-load-balance', function () {
        // Delegate the click because balance buttons can be added later.
        let id = $(this).data('id');
        let address = $(this).data('bitcoinAddress');
        console.log(id, address);

        loadBalance(id, address);
    });


    function loadBlockchain(id) {
        var data = { election_id: id };
        txid = [];
        txaddress = [];
        $.getJSON("/api/allbitcoinaddress", data, "json").done(function (result) {
            var div = $('<div>');
            console.log(result.data.length);
            $(result.data).each(function (i, item) {
                if(item.is_paid == false){
                    console.log('oui');
                }
                var btn = $('<button>').addClass(`btn btn-primary rounded-3 btn-balance-${item.id}`)
                    .attr({ 'data-id': item.id, 'data-bitcoin-address': item.bitcoin_address, 'id': 'btn-load-balance' })
                    .text('Load');
                var tr = $('<tr>').append($('<td>').append(item.id))
                tr.append($('<td>').append(item.bitcoin_address));
                tr.append($('<td>').append(btn));
                div.append(tr);

                txid.push(item.id);
                txaddress.push(item.bitcoin_address);
            });
            if (div.html().trim() === '') {
                div.html("<tr><td class='p-3 w-100 text-center' colspan='3'>No data available.</td></tr>");
            }
            $(".table-balance").html(div.html());
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
    function loadBalance(id, address) {

        $.getJSON("/api/blockio/get_address_balance", { address: address }, function (result) {
            if (result.success === true) {
                console.log(result.data.data.available_balance);
                $(".btn-balance-" + id).text(result.data.data.available_balance);
            }
        });
    }
});


