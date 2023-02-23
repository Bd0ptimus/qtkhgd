$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function deleteItems(gridOptions, nodeRows, url) {
    if (nodeRows.length > 0) {
        var ids = [];
        nodeRows.forEach(function (selectedRow) {
            var data = selectedRow.data;
            ids.push(data.id);
        });

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: true,
        })
        swalWithBootstrapButtons.fire({
            title: 'Bạn có chắc chắn muốn xoá?',
            text: "",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Đúng, Chắc chắn xoá!',
            confirmButtonColor: "#DD6B55",
            cancelButtonText: 'Không, tôi không muốn!',
            reverseButtons: true,

            preConfirm: function () {
                return new Promise(function (resolve) {
                    $.ajax({
                        method: 'post',
                        url,
                        data: {
                            ids:ids,
                        },
                        success: function (data) {
                            if (data.error == 1) {
                                swalWithBootstrapButtons.fire(
                                    'Đã huỷ',
                                    data.msg,
                                    'error'
                                )
                            } else {
                                resolve(data);
                            }

                        }
                    });
                });
            }

        }).then((result) => {
            if (result.value) {
                gridOptions.api.applyTransaction({remove: nodeRows});
                swalWithBootstrapButtons.fire(
                    'Đã xoá!',
                    'Xoá thành công',
                    'success'
                )
            }
        })
    }
}