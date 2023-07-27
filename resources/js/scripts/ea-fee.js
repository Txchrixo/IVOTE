import Swal from 'sweetalert2/dist/sweetalert2'
/**
 * EA Fee
 * @author  TxChrixo
 *
 * Le serveur paie les frais avec les identifiants Block.io.
 */
$(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');

    $.getJSON("/api/blockio/get_address_balance", { address: $(".bitcoin-address").val() })
        .done(function (result) {
            if (result.success === true) {
                $(".number-btc").text(result.data.data.available_balance);
                $(".number-network").text(result.data.data.network);
            } else {
                unconfigured();
            }
        })
        .fail(unconfigured);

    function unconfigured() {
        $(".number-btc").text("Unconfigured");
        $(".number-network").text("Unconfigured");
    }

    // Le nombre d’UTXO reste indisponible depuis l’arrêt de chain.so v2.
    $(".number-unspent").text("N/A");

    let unpaid = [];

    loadBlockchain($(".input-election").val());

    $(".input-election").on('change', function () {
        loadBlockchain($(this).val());
    });

    $(".btn-pay-now").on('click', function () {
        payAll();
    });

    function loadBlockchain(id) {
        $.getJSON("/api/allbitcoinaddress", { election_id: id }).done(function (result) {
            unpaid = [];
            const tbody = $(".table-fee").empty();
            $(".number-addresses").text(result.data.length);

            $(result.data).each(function (i, item) {
                if (Number(item.is_paid) === 0) {
                    unpaid.push(item.id);
                }
                // Afficher les valeurs de l’API en texte brut.
                const btn = $('<button>')
                    .addClass('btn btn-primary rounded-3 btn-single-pay')
                    .attr('type', 'button')
                    .data('id', item.id)
                    .prop('disabled', Number(item.is_paid) === 1)
                    .text(Number(item.is_paid) === 1 ? 'Paid' : 'Pay');
                // ID | Bitcoin address | Transaction ID | Operation
                const tr = $('<tr>')
                    .append($('<td>').text(item.id))
                    .append($('<td>').text(item.bitcoin_address))
                    .append($('<td>').append($('<span>').addClass('text-break status-' + Number(item.id))))
                    .append($('<td>').append(btn));
                tbody.append(tr);
            });

            if (result.data.length === 0) {
                tbody.append($('<tr>').append($('<td>').addClass('p-3 w-100 text-center').attr('colspan', 4).text('No data available.')));
            }
        }).fail(function (error) {
            Swal.fire({ title: 'Error', text: 'Unable to load addresses (' + error.status + ')', icon: 'error' });
        });
    }

    function pay(keyId) {
        return $.ajax({
            url: '/ea/api/blockio/pay',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            data: { key_id: keyId },
        }).done(function (result) {
            $(".status-" + Number(keyId)).text(result.data.txid || 'OK');
        }).fail(function (error) {
            const msg = error.responseJSON && error.responseJSON.message ? error.responseJSON.message : 'Error';
            $(".status-" + Number(keyId)).text(msg);
        });
    }

    async function payAll() {
        if (unpaid.length === 0) {
            Swal.fire({ title: 'Nothing to pay', icon: 'info' });
            return;
        }
        const confirm = await Swal.fire({
            title: 'Pay ' + unpaid.length + ' addresses?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Pay now',
        });
        if (!confirm.isConfirmed) return;

        // Paiements séquentiels pour ne pas dépenser deux fois les mêmes UTXO
        for (const id of unpaid.slice()) {
            try { await pay(id); } catch (e) { /* statut déjà affiché */ }
        }
        loadBlockchain($(".input-election").val());
    }

    $(".table-fee").on('click', '.btn-single-pay', async function () {
        const id = $(this).data('id');
        const confirm = await Swal.fire({
            title: 'Pay address #' + Number(id) + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Pay',
        });
        if (!confirm.isConfirmed) return;
        try { await pay(id); } catch (e) { /* statut déjà affiché */ }
        loadBlockchain($(".input-election").val());
    });
});
