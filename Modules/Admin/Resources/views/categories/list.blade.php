@extends('layouts.app')


@section('content')

@section('page_css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endsection


<main id="main" class="main">

    <div class="pagetitle">
        <h1>Categories</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Admin</a></li>
                <li class="breadcrumb-item active">Categories</li>
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
                                <h5 class="card-title">Categories</h5>
                            </div>
                            <div class="col">
                                <h5 class="card-title text-end"><a id='add-new-item' class="btn btn-primary" data-postdata='{"_token": "{{ csrf_token() }}"}' data-url="{{ url('/categories/add') }}">Add Data</a>
                                </h5>
                            </div>
                        </div>


                        <!-- Table with stripped rows -->
                        <table id="basic-datatable" data-datatable-init="true" class="table table-bordered w-100 mt-2">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Category Name</th>
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
        let table = $('#basic-datatable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [
                [0, "desc"]
            ],
            ajax: {
                url: "{{ url('/Categories/fetchList') }}",
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
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row) {
                        let isChecked = row.status == '0' ? "" : "checked";
                        return `
                            <div class="form-check form-switch">
                                <input class="form-check-input status-switch" data-token='{"_token": "{{ csrf_token() }}"}' data-url="{{ url('/Categories/status/update') }}" type="checkbox" role="switch" ${isChecked} data-id="${row.DT_RowId}">
                                <label class="form-check-label"></label>
                            </div>
                        `;
                    }
                },
                {
                    mRender: function(data, type, row, meta) {
                        let editUrl = "{{ url('/Categories/edit') }}/" + row.DT_RowId;
                        let deleteUrl = "{{ url('/Categories/delete') }}/" + row.DT_RowId;

                        let actionEdit = editLink(
                            editUrl,
                            row.DT_RowId,
                            "Edit category",
                            'fa-solid fa-pen',
                            'text-secondary'
                        );

                        let actionDelete = deleteLink(
                            deleteUrl,
                            row.DT_RowId,
                            'text-danger'
                        );

                        return actionEdit + actionDelete;
                    },
                    searchable: false,
                    orderable: false
                }

            ]
        });



    });






</script>




@endsection