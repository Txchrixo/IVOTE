import Swal from 'sweetalert2/dist/sweetalert2'

/**
 * EA Dashboard
 * @author TxChrixo
 */
$(function () {
    $('.blockchain').each((index, input) => {
        let blockchain = input.value;
        let network = input.getAttribute('data-network');
        let address = ['tb1qzw30qyf5kankatqr9ju8xamvfd5p396d73jxwq'];
        console.log(`/cryptoapis-proxy/blockchain-data/${blockchain}/${network}/addresses/${address}/balance`);

        $.getJSON(`/cryptoapis-proxy/blockchain-data/${blockchain}/${network}/addresses/${address}/balance`, function (result) {
            console.log(result);
            if (result.data) {
                $(".number-btc").html(result.data.item.confirmedBalance.amount + result.data.item.confirmedBalance.unit);
                $(".number-network").html(input.getAttribute('data-network').toUpperCase());
            } else {
                $(".number-btc").html("Unconfigured");
                $(".number-network").html("Unconfigured");
            }
        })
    });
    $('.btn-toggleStage').on('click', function loadCandidate() {
        let id = $(this).data('id');
        let data = { id: id };
        Swal.fire({
            title: 'Enter your password for process',
            input: 'password',
            text: "You will not be able to return to the previous step!",
            icon: "question",
            inputAttributes: {
                placeholder: "Type your password for confirmation"
            },
            showCancelButton: true,
            confirmButtonText: "Yes, " + $(this).text(),
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                data.password = password;
                console.log(data);
                return fetch(`/api/election/toggleStage`, {
                    method: 'PUT', // Use PUT method
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(response => {
                        if (!response.success) {
                            if (response.data.password) {
                                Swal.showValidationMessage(
                                    `Validation failed: ${response.data.password}`
                                );
                            } else {
                                throw new Error(response.data);
                            }
                        }

                        return response;
                    })
                    .catch(error => {
                        console.log(error);
                        Swal.showValidationMessage(
                            `Request failed: ${error.message}`
                        );
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: result.value.data.message,
                    text: prefixToggle(result.value.data.stage),
                    icon: "success",
                })
                $(this).text(btnToggle(result.value.data.stage))
                $(this).parent().parent().parent().get(0).querySelector('.status-available').innerText = statusToggle(result.value.data.stage);
                if(result.value.data.stage == 0 || result.value.data.stage == 2 ){
                    $(this).parent().parent().parent().get(0).querySelector('.status-available').className = "offline-status status-available";
                }
                if(result.value.data.stage == 1 || result.value.data.stage == 3 || result.value.data.stage == 4){
                    $(this).parent().parent().parent().get(0).querySelector('.status-available').className = "online-status status-available";
                }
                console.log('result', $(this).parent().parent().parent().get(0).querySelector('.status-available').innerText);
            }
        })
    });

    function prefixToggle(str) {
        if (str == 1)
            return "The verification has started";
        if (str == 2)
            return "The verification has ended";
        if (str == 3)
            return "The vote has started";
        if (str == 4)
            return "The vote has ended";
    }

    function btnToggle(str) {
        if (str == 0)
            return "Sart Veriff";
        if (str == 1)
            return "Stop Veriff";
        if (str == 2)
            return "Start Voting";
        if (str == 3)
            return "Stop Voting";
        if (str == 4)
            return "See Results";
    }

    function statusToggle(str) {
        if (str == 0)
            return "Unbegun";
        if (str == 1)
            return "Veriff";
        if (str == 2)
            return "Waiting";
        if (str == 3)
            return "Voting Ongoing";
        if (str == 4)
            return "Done";
    }

    $('.btn-getElection').on('click', function loadElection() {
        let id = $(this).data('id');
        let data = { id: id };
        $.getJSON("/api/election/details", data, "json").done(function (result) {
            console.log(result.data);
            $(".title").val(result.data.title);
            $(".number").val(result.data.number);
            $(".description").val(result.data.description);
            $(`input[value=${result.data.stage}]`).attr('checked', true);
            $('input[type=radio]').each((value, radio) => {
                if (value < $(`input[value=${result.data.stage}]`).val()) {
                    radio.setAttribute('disabled', true);
                }
            });

            Object.entries(result.data).forEach(([key, value]) => {
                if ($(`input[name="${key}"]`)) {
                    $(`input[name="${key}"]`).val(value)
                }
            });

            if (result.data.stage > 1) {
                $(".title").attr('readonly', true);
                $(".number").attr('readonly', true);
                $('input[name="start_veriff"]').attr('readonly', true);
                $('input[name="end_veriff"]').attr('readonly', true);
            }
            if (result.data.stage > 3) {
                $(".number").attr('readonly', true);
                $('input[name="start_vote"]').attr('readonly', true);
                $('input[name="end_vote"]').attr('readonly', true);
            }
        });
        $("#electionForm").attr('data-id', id);
    });





});
