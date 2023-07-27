import { Modal } from 'bootstrap';
import Swal from 'sweetalert2/dist/sweetalert2'
/**
 * EA Vote
 * @author  TxChrixo
 */

$(function () {

   
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery.each(["put", "delete"], function (i, method) {
        jQuery[method] = function (url, data, callback, type) {
            if (typeof data === "function") {
                type = type || callback;
                callback = data;
                data = undefined;
            }

            return jQuery.ajax({
                url: url,
                type: method,
                dataType: type,
                data: data,
                success: callback
            });
        };
    });

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


    $('#electionForm').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('id', $(this).data('id'));

        let jsonData = {};
        formData.forEach((value, key) => jsonData[key] = value);
        console.log(jsonData);

        $.put("/api/election/update", jsonData, "json")
            .done(function (result) {
                $("[data-bs-dismiss=modal]").trigger({ type: "click" });
                Swal.mixin({
                    customClass: {
                      confirmButton: 'btn btn-success rounded-3',
                    },
                    buttonsStyling: false
                }).fire({
                    title: "Good Job",
                    text: result.message,
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonText: "Got it!",
                    closeOnConfirm: false
                });
            })
            .fail(function (error, textStatus, errorThrown) {
                if (error.status === 422) {
                    Object.entries(error.responseJSON.data).forEach(([key, value]) => {
                        $(`.error-${key}`).text(value[0]);
                        $(`.${key}`).addClass('is-invalid');
                        $(`.error-${key}`).attr('hidden', false);
                    });
                    console.error('Erreur 422 :', error.responseJSON.message);
                } else {
                    console.error('Error ' + error.status + ': ' + textStatus + ' - ' + errorThrown);
                }
            });

    });

    $('.btn-getCandidates').on('click', function loadCandidate() {
        let id = $(this).data('id');
        let data = { vote_id: id };
        $.getJSON("/api/candidates", data, "json")
            .done(function (result) {
                console.log(result);
                var str = '';
                $(result.data).each(function (i, item) {
                    str += '<div class="col"><div class="panel panel-headline rounded-4"><div class="panel-heading px-2"><h3>'
                        + item.name +
                        '</h3><p class="panel-subtitle px-2"> #'
                        + item.id +
                        '</p></div><div class="panel-body px-2"><p>'
                        + item.des +
                        '</p></div></div></div></div>';
                });
                $(".box-candidate").html(str);
                if(result.data.length == 0){
                    $(".box-candidate").html("<h3 class='p-3 w-100 text-center' colspan='3'>No candidate registered yet</h3>");
                }
            }).fail(function (error, textStatus, errorThrown) {
                if (error.status === 422) {
                    console.error('Erreur 422 :', error.responseJSON.message);
                } else {
                    Swal.mixin({
                        customClass: {
                          confirmButton: 'btn btn-danger rounded-3',
                        },
                        buttonsStyling: false
                    }).fire({
                        title: textStatus,
                        text: errorThrown,
                        icon: "error",
                        showCancelButton: false,
                        closeOnConfirm: false
                    });
                    console.error('Error ' + error.status + ': ' + textStatus + ' - ' + errorThrown);
                }
            });
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
                return fetch(`/api/election/toggleStage`, {
                    method: 'PUT', // Use PUT method
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(response => {
                        if (!response.success) {
                            if(response.data.password){
                                Swal.showValidationMessage(
                                    `Validation failed: ${response.data.password}`
                                );
                            }else{
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
            console.log('result', result);
            if (result.isConfirmed) {
                Swal.fire({
                    title : result.value.data.message,
                    text: prefixToggle(result.value.data.stage),
                    icon: "success",
                })
                $(this).text(btnToggle(result.value.data.stage));
                if(result.value.data.stage == 1){
                    $(this).removeClass('btn-warning');
                    $(this).addClass('btn-danger');
                }
                if(result.value.data.stage == 2){
                    $(this).removeClass('btn-danger');
                    $(this).addClass('btn-success');
                }
                if(result.value.data.stage == 3){
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-danger');
                }
                if(result.value.data.stage == 4){
                    $(this).removeClass('btn-danger');
                    $(this).addClass('btn-secondary');
                }
                console.log('odddd', $(this));
            }
        })
    });


});
