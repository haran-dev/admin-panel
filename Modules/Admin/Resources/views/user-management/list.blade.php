@extends('layouts.app')


@section('content')

@section('page_css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endsection


<main id="main" class="main">

    <div class="pagetitle">
        <h1>User Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Admin</a></li>
                <li class="breadcrumb-item active">User Management</li>
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
                                <h5 class="card-title">User Management</h5>
                            </div>
                            <div class="col">
                                @can('create user')
                                    <h5 class="card-title text-end"><a id='add-new-item' class="btn btn-primary" data-postdata='{"_token": "{{ csrf_token() }}"}' data-url="{{ url('/user-mangement/add') }}">Add Data</a>
                                    </h5>
                                @endcan
                            </div>
                        </div>


                        <!-- Table with stripped rows -->
                        <table id="basic-datatable" class="table table-bordered w-100 mt-2">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>User Role</th>
                                    <th>User Role</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

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
<!-- jQuery (required for DataTables) -->
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->

<!-- Bootstrap + DataTables JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>

<!-- Initialize DataTable -->
<script>
    $(document).ready(function() {

        const canUpdateRoles = JSON.parse("@json(auth()->user()->can('update user'))");
        const canDeleteRoles = JSON.parse("@json(auth()->user()->can('delete user'))");

        let table = $('#basic-datatable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [
                [0, "desc"]
            ],
            ajax: {
                url: "{{ url('/user-mangement/fetchList') }}",
                type: 'GET',
                data: {
                    "_token": "{{ csrf_token() }}"
                }
            },
            columns: [{
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
                    data: 'role_name',
                    name: 'role_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row) {
                        if (!data) return '';
                        return new Date(data).toISOString().slice(0, 10); // outputs YYYY-MM-DD
                    }
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
                                <input class="form-check-input status-switch" data-token='{"_token": "{{ csrf_token() }}"}' data-url="{{ url('/user-mangement/status/update') }}" type="checkbox" role="switch" ${isChecked} ${isDisabled} data-id="${row.DT_RowId}">
                                <label class="form-check-label"></label>
                            </div>
                        `;
                    }
                },
                {
                    mRender: function (data, type, row, meta) {
                        let actionHtml = '';

                        if (canUpdateRoles) {
                            let editUrl = "{{ url('/user-mangement/edit') }}/" + row.DT_RowId;
                            actionHtml += editLink(
                                editUrl,
                                row.DT_RowId,
                                "Edit roles",
                                'fa-solid fa-pen',
                                'text-secondary'
                            );
                        }

                        if (canDeleteRoles) {
                            let deleteUrl = "{{ url('/user-mangement/delete') }}/" + row.DT_RowId;
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

    $(document).on('shown.bs.modal', '#modal-view', function () {
        initSelect2(
            '#modal-view .js-example-basic-single',      // selector
            '{{ url("roles/select2") }}',                // AJAX URL
            'user',
            'Select a role',                             // Placeholder
            '#modal-view'                                // dropdownParent (for modals)
        );
    });


    function initSelect2(selector, url, value, placeholder, dropdownParentSelector = 'body') {

        const $element = $(selector);

        $element.select2({
            placeholder: placeholder,
            dropdownParent: $(dropdownParentSelector),
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term // Search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.id, text: item.name };
                        })
                    };
                },
                cache: true
            }
        });


        const selectedId = $element.data(value);

        if(selectedId)
        {
            const selectedUrl = `${url}/${selectedId}`;
            $.ajax({
                url: selectedUrl,
                dataType: 'json',
                success: function (item) {
                    const option = new Option(item.name, item.id, true, true);
                    $element.append(option).trigger('change');
                }
            });
        }



    }



 


</script>




@endsection