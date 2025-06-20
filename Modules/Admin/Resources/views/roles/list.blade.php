@extends('layouts.app')

@section('page_css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Roles</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Admin</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title">Roles</h5>
                            </div>
                            <div class="col text-end">
                                @can('create roles')
                                    <a id="add-new-item" class="btn btn-primary"
                                       data-postdata='{"_token": "{{ csrf_token() }}"}'
                                       data-url="{{ url('/roles/add') }}">
                                        Add Data
                                    </a>
                                @endcan
                            </div>
                        </div>

                        <!-- Table with stripped rows -->
                        <table id="basic-datatable" class="table table-bordered w-100 mt-2">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Roles Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables will load data via Ajax --}}
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>

            </div>
        </div>
    </section>

</main>
@endsection


@section('page_js')
<!-- Bootstrap + DataTables JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>

<script>
$(document).ready(function () {
    // Get user permission from Blade
    const canUpdateRoles = JSON.parse("@json(auth()->user()->can('update roles'))");
    const canDeleteRoles = JSON.parse("@json(auth()->user()->can('delete roles'))");


    let table = $('#basic-datatable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        order: [[0, "desc"]],
        ajax: {
            url: "{{ url('/roles/fetchList') }}",
            type: 'GET',
            data: {
                "_token": "{{ csrf_token() }}"
            }
        },
        columns: [
            {
                data: 'DT_RowId',
                name: 'id',
                visible: false,
                searchable: false,
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: null,
                searchable: false,
                orderable: false,
                render: function (data, type, row) {
                    let isChecked = row.status == '0' ? "" : "checked";
                    let isDisabled = !canUpdateRoles ? "disabled" : "";
                    return `
                        <div class="form-check form-switch">
                            <input class="form-check-input status-switch" data-token='{"_token": "{{ csrf_token() }}"}' data-url="{{ url('/roles/status/update') }}" type="checkbox" role="switch" ${isChecked} ${isDisabled} data-id="${row.DT_RowId}">
                            <label class="form-check-label"></label>
                        </div>
                    `;
                }
            },
            {
                mRender: function (data, type, row, meta) {
                    let actionHtml = '';

                    if (canUpdateRoles) {
                        let editUrl = "{{ url('/roles/edit') }}/" + row.DT_RowId;
                        actionHtml += editLink(
                            editUrl,
                            row.DT_RowId,
                            "Edit roles",
                            'fa-solid fa-pen',
                            'text-secondary'
                        );
                    }

                    if (canDeleteRoles) {
                        let deleteUrl = "{{ url('/roles/delete') }}/" + row.DT_RowId;
                        actionHtml += deleteLink(
                            deleteUrl,
                            row.DT_RowId,
                            'text-danger'
                        );
                    }

                    return actionHtml || '<span class="text-muted">No Permission to Access</span>';
                },
                searchable: false,
                orderable: false
            }

        ]
    });
});

// $(document).on('change', '.status-switch', function() {
//     var id = $(this).data('id');
//     var status = $(this).is(':checked') ? 1 : 0;

//     $.ajax({
//         url: '/roles/status/update',
//         method: 'POST',
//         data: {
//             _token: $('meta[name="csrf-token"]').attr('content'),
//             id: id,
//             status: status
//         },
//         success: function(response) {
//             if (response.status === 'success') {
//                 $.notify.addStyle('noIconSuccess', { 
//                         html: `
//                             <div>
//                                 <span data-notify-text/>
//                             </div>
//                         `,
//                         classes: {
//                             base: {
//                                 "background-color": "#4CAF50", 
//                                 "color": "#fff",
//                                 "padding": "10px",
//                                 "border-radius": "5px",
//                                 "box-shadow": "0 2px 10px rgba(0,0,0,0.2)",
//                                 "max-width": "500px"
//                             }
//                         }
//                     });


//                     $.notify(response.message, {
//                         style: "noIconSuccess",
//                         position: "top right" 
//                     });

//             } else if (response.status === 'error') {
//                 $.notify.addStyle('noIconError', {
//                         html: `
//                             <div>
//                                 <span data-notify-text/>
//                             </div>
//                         `,
//                         classes: {
//                             base: {
//                                 "background-color": "#F44336",
//                                 "color": "#fff",
//                                 "padding": "10px",
//                                 "border-radius": "5px",
//                                 "box-shadow": "0 2px 10px rgba(0,0,0,0.2)",
//                                 "max-width": "500px"
//                             }
//                         }
//                     });


//                     $.notify(response.message, {
//                         style: "noIconError",
//                         position: "top right"
//                     });
//             }
//         },
//         error: function(xhr) {
//             if (xhr.status === 403) {
//                 var accessModal = new bootstrap.Modal(document.getElementById('accessDeniedModal'), {
//                     backdrop: 'static',
//                     keyboard: false
//                 });
//                 accessModal.show();
//             } else {
//                 console.error('Error updating status:', xhr.responseText);
//                 $.notify.addStyle('noIconError', {
//                         html: `
//                             <div>
//                                 <span data-notify-text/>
//                             </div>
//                         `,
//                         classes: {
//                             base: {
//                                 "background-color": "#F44336",
//                                 "color": "#fff",
//                                 "padding": "10px",
//                                 "border-radius": "5px",
//                                 "box-shadow": "0 2px 10px rgba(0,0,0,0.2)",
//                                 "max-width": "500px"
//                             }
//                         }
//                     });


//                     $.notify('Something went wrong. Please try again.', {
//                         style: "noIconError",
//                         position: "top right"
//                     });
//             }
//         }
//     });
// });
</script>
@endsection
