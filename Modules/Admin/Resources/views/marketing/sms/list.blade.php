@extends('layouts.app')

@section('page_css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>SMS Marketing</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Admin</a></li>
                <li class="breadcrumb-item active">Marketing / SMS Marketing</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title">SMS Marketing</h5>
                            </div>
                            <div class="col text-end d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                <button type="button" class="btn btn-primary btn-sm d-none position-relative" id="send-msg-btn">
                                    <i class="fas fa-paper-plane me-1"></i> Send Message
                                    <span id="msg-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                                </button>

                                <form id="import-form" enctype="multipart/form-data" class="d-flex align-items-center needs-validation">
                                    @csrf
                                    <div id="import-wrapper" class="input-group" style="max-width: 300px;">
                                        <input type="file" name="csv_file" class="form-control form-control-sm" accept=".csv" required>
                                        <button type="button" class="btn btn-primary btn-sm file-form-submit" title="Import CSV" data-url="{{ url('/sms/store') }}">
                                            <i class="fa fa-plus"></i> Import
                                        </button>
                                    </div>
                                </form>

                                <a id="add-new-item" class="btn btn-primary btn-sm" data-postdata='{"_token": "{{ csrf_token() }}"}' data-url="{{ url('/sms/add') }}">
                                    Add Data
                                </a>
                            </div>
                        </div>

                        <table id="basic-datatable" class="table table-bordered w-100 mt-2">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>Name</th>
                                    <th>Mobile Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@section('page_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>

<script>
    $(document).ready(function () {
        let table = $('#basic-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/sms/fetchList') }}",
                type: 'GET',
                data: {
                    "_token": "{{ csrf_token() }}"
                }
            },
            columns: [
                {
                    data: 'id',
                    render: function (data, type, row) {
                        return `<input type="checkbox" class="row-checkbox" value="${row.DT_RowId}">`;
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'name', name: 'name' },
                { data: 'mobile_number', name: 'mobile_number' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-secondary edit-btn" data-id="${row.DT_RowId}"><i class="fa fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${row.DT_RowId}"><i class="fa fa-trash"></i> Delete</button>
                            <button class="btn btn-sm btn-success save-btn d-none" data-id="${row.DT_RowId}"><i class="fa fa-save"></i> Save</button>
                            <button class="btn btn-sm btn-warning cancel-btn d-none" data-id="${row.DT_RowId}"><i class="fa fa-times"></i> Cancel</button>
                        `;
                    },
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#select-all').on('change', function () {
            $('.row-checkbox').prop('checked', $(this).is(':checked'));
            updateSelectionUI();
        });

        $(document).on('change', '.row-checkbox', function () {
            if (!$(this).is(':checked')) {
                $('#select-all').prop('checked', false);
            }
            updateSelectionUI();
        });

        function updateSelectionUI() {
            let selectedCount = $('.row-checkbox:checked').length;
            $('#msg-badge').text(selectedCount);

            if (selectedCount > 0) {
                $('#send-msg-btn').removeClass('d-none');
                $('#add-new-item').addClass('d-none');
                $('#import-wrapper').hide();
            } else {
                $('#send-msg-btn').addClass('d-none');
                $('#add-new-item').removeClass('d-none');
                $('#import-wrapper').show();
                $('#select-all').prop('checked', false);
            }
        }

        $('#send-msg-btn').on('click', function () {
            let selectedIds = $('.row-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (selectedIds.length > 0) {
                $.ajax({
                    url: '/sms/send',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ids: selectedIds
                    },
                    success: function (response) {
                        if (response.status === "success") {
                            $.notify(response.message, {
                                className: 'success',
                                position: "top right"
                            });
                            window.location.href = response.redirect_url;
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                alert('Please select at least one row.');
            }
        });

        // Edit Row
        $(document).on('click', '.edit-btn', function () {
            let row = $(this).closest('tr');
            let nameCell = row.find('td').eq(1);
            let numberCell = row.find('td').eq(2);

            nameCell.attr('data-original', nameCell.text().trim());
            numberCell.attr('data-original', numberCell.text().trim());

            nameCell.html(`<input type="text" class="form-control form-control-sm edit-name" value="${nameCell.attr('data-original')}">`);
            numberCell.html(`<input type="text" class="form-control form-control-sm edit-number" value="${numberCell.attr('data-original')}">`);

            row.find('.edit-btn, .delete-btn').addClass('d-none');
            row.find('.save-btn, .cancel-btn').removeClass('d-none');
        });

        // Cancel Edit
        $(document).on('click', '.cancel-btn', function () {
            let row = $(this).closest('tr');
            let nameCell = row.find('td').eq(1);
            let numberCell = row.find('td').eq(2);

            nameCell.text(nameCell.attr('data-original'));
            numberCell.text(numberCell.attr('data-original'));

            row.find('.save-btn, .cancel-btn').addClass('d-none');
            row.find('.edit-btn, .delete-btn').removeClass('d-none');
        });

        // Save Button (for example only; you'll need backend support to update DB)
        $(document).on('click', '.save-btn', function () {
            let row = $(this).closest('tr');
            let id = $(this).data('id');
            let name = row.find('.edit-name').val();
            let mobile = row.find('.edit-number').val();

            $.ajax({
                url: '/sms/update/' + id,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name,
                    mobile_number: mobile
                },
                success: function (res) {
                    table.ajax.reload(null, false); // reload table without resetting pagination
                },
                error: function (xhr) {
                    alert("Update failed!");
                }
            });
        });
    });
</script>
@endsection
