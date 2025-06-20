@extends('layouts.app')

@section('page_css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Email Marketing</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Admin</a></li>
                <li class="breadcrumb-item active">Marketing / Email Marketing</li>
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
                                <h5 class="card-title">Email Marketing</h5>
                            </div>
                            <div class="col text-end d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                <!-- Send Msg Button (Hidden initially) -->
                                <button type="button" class="btn btn-primary btn-sm d-none position-relative" id="send-msg-btn">
                                    <i class="fas fa-paper-plane me-1"></i> Send Email
                                    <span id="msg-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        0
                                    </span>
                                </button>

                                
                                <!-- CSV Import Form -->
                                <form id="import-form" enctype="multipart/form-data" class="d-flex align-items-center needs-validation">
                                    @csrf
                                    <div id="import-wrapper" class="input-group" style="max-width: 300px;">
                                        <input type="file" name="csv_file" class="form-control form-control-sm" accept=".csv" required>
                                        <button type="button" class="btn btn-primary btn-sm file-form-submit" title="Import CSV" data-url="{{ url('/email/store') }}">
                                            <i class="fa fa-plus"></i> Import
                                        </button>
                                    </div>
                                </form>


                                <a id="add-new-item" class="btn btn-primary btn-sm"
                                       data-postdata='{"_token": "{{ csrf_token() }}"}'
                                       data-url="{{ url('/email/add') }}">
                                        Add Data
                                    </a>
                            </div>
                        </div>

                        <!-- DataTable -->
                        <table id="basic-datatable" class="table table-bordered w-100 mt-2">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables loads via Ajax --}}
                            </tbody>
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
                url: "{{ url('/email/fetchList') }}",
                type: 'GET',
                data: {
                    "_token": "{{ csrf_token() }}"
                }
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    render: function (data, type, row) {
                        return `<input type="checkbox" class="row-checkbox" value="${row.DT_RowId}">`;
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                
            ]
        });

        // Select All handler
        $('#select-all').on('change', function () {
            $('.row-checkbox').prop('checked', $(this).is(':checked'));
            updateSelectionUI();
        });

        // Individual checkbox change
        $(document).on('change', '.row-checkbox', function () {
            if (!$(this).is(':checked')) {
                $('#select-all').prop('checked', false);
            }
            updateSelectionUI();
        });

        function updateSelectionUI() {
            let selectedCount = $('.row-checkbox:checked').length;

            if (selectedCount > 0) {
                $('#send-msg-btn').removeClass('d-none');
                $('#add-new-item').addClass('d-none');
                $('#msg-badge').text(selectedCount);
                $('#import-wrapper').hide();
            } else {
                $('#send-msg-btn').addClass('d-none');
                $('#msg-badge').text(0);
                $('#import-wrapper').show();
                $('#select-all').prop('checked', false);
                $('#add-new-item').removeClass('d-none');
            }
        }

        // Send Msg click
        $('#send-msg-btn').on('click', function () {
            let selectedIds = $('.row-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (selectedIds.length > 0) {
                let selectedIds = $('.row-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();
                console.log('Send Email to IDs: ' + selectedIds);
                
                if (selectedIds.length > 0) {
                    // Optional: Show loader or disable button here

                    $.ajax({
                        url: '/email/send', // 🔁 Update with your actual route
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'), // ✅ CSRF token
                            ids: selectedIds
                        },
                        success: function (response) {
                            
                            console.log(response);
                            if (response.status === "success") {
                                $.notify.addStyle("noIconSuccess", {
                                    html: `
                                            <div>
                                                <span data-notify-text/>
                                            </div>
                                        `,
                                    classes: {
                                        base: {
                                            "background-color": "#4CAF50",
                                            color: "#fff",
                                            padding: "10px",
                                            "border-radius": "5px",
                                            "box-shadow": "0 2px 10px rgba(0,0,0,0.2)",
                                            "max-width": "500px",
                                            "z-index": "20000 !important",
                                        },
                                    },
                                });

                                $.notify(response.message, {
                                    style: "noIconSuccess",
                                    position: "top right",
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
            }
        });
    });


    function updateRowNumbers() {
        $('#dynamic-table-body tr').each(function (index) {
            $(this).find('.row-number').text(index + 1);
        });
    }

    // Model Show
    $(document).on('shown.bs.modal', '#modal-view', function () {
        $('#add-row').on('click', function () {
            const newRow = `
                <tr>
                    <td class="row-number"></td>
                    <td><input type="text" name="names[]" class="form-control" required></td>
                    <td><input type="text" name="email[]" class="form-control" required></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>`;
            $('#dynamic-table-body').append(newRow);
            updateRowNumbers();
        });

        // Remove row
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
            updateRowNumbers();
        });

        // Initial row numbering
        updateRowNumbers();
    });
</script>
@endsection
