import Swal from 'sweetalert2/dist/sweetalert2'
/**
 * RA: show candidate
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
    var id = $(".input-election").val();
    loadCandidate(id);

    $(".input-election").on('click', function () {
        loadCandidate($(this).val());
    });

    $(".btn-refresh").on('click', function () {
        loadCandidate($(".input-election").val());
    });

    $(".btn-edit-candidate").on('click', function () {
        var candidateid = $(".candidate-id").val();
        var data = { id: candidateid, name: $(".input-name").val(), des: $(".input-des").val() };
        $.getJSON("/api/updatecandidate", data, function (result) {
            if (result.success === '1') {
                $(".modal-edit-candidate").modal("hide");
                swal('Good Job!', result.content, 'success');
                loadCandidate($(".input-id").val());
            } else {
                swal('Opps', 'Please refresh this page and try again!', 'error');
                loadCandidate($(".input-id").val());

            }
        });
    });
    function loadCandidate(id) {
        let data = { vote_id: id };
        $.getJSON("/api/candidates", data, "json")
            .done(function (result) {
                console.log(result);
                var str = '';
                var count = 0;
                $(result.data).each(function (i, item) {
                    count ++;
                    var editbutton = '<button type="button" class="btn btn-outline-primary btn-toggle p-2 px-4 my-1 rounded-3" data-bs-toggle="modal" data-bs-target="#candidateModal" data-id="'+ item.id +'">Edit</button>';
                    var delbutton = '<button type="button" class="btn btn-outline-danger btn-candidate-delete p-2 px-4 mx-1 rounded-3" data-id="'+ item.id +'">Delete</button>';
                    str += '<div class="col-md-4"><div class="panel panel-headline rounded-4"><div class="panel-heading"><h3 class="panel-title candidate-' + item.id + '-name" style="font-family: Helvetica;">'
                        + item.name +
                        '</h3><p class="panel-subtitle"> #'
                        + count +
                        '</p></div><div class="panel-body candidate-' + item.id + '-des"><p class="text-break">'
                        + item.des +
                        '</p></div><div class="panel-footer p-3"><h5>'
                        + editbutton + ' ' + delbutton +
                        '</h5></div></div></div>';
                });
                $(".box-candidate").html(str);
                if (result.data.length == 0) {
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
    }
    $(".btn-candidate-delete").on('click', function () {
        console.log('ok');
        let id = $(this).data('id');
        let data = { id: id };
        Swal.mixin({
            customClass: {
              confirmButton: 'btn btn-danger rounded-3',
            },
            buttonsStyling: false
        }).fire({
            title: "Are you sure?",
            text: "This candidate will be deleted",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Delete",
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                var data = { id: id };
                $.delete("/api/delcandidate", data, function (result) {
                    if (result.success == true) {
                        Swal.fire("Deleted!", "This candidate has been deleted.", "success");
                        loadCandidate($(".input-election").val());
                    }
                });
                console.log('result', $(this)); 
            }
        });
    });
    
    
    
});


function editcandidate(id) {
    $(".candidate-id").val(id);
    $(".input-name").val($(".candidate-" + id + "-name").html());
    $(".input-des").val($(".candidate-" + id + "-des").text());
}

