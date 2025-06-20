<!-- Modal -->
<div class="modal-dialog">
    <form class="text-form needs-validation" novalidate>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateorUpdateLabel">{{ @$title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        @csrf

                        @if ($crudAction != 'delete')
                        <input type="hidden" name="action_id" id="action_id" value="{{ @$details->id }}" />

                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="add-row">
                                <i class="fa fa-plus"></i> Add Row
                            </button>
                        </div>

                        <!-- Dynamic Table -->
                        <table class="table table-bordered" id="dynamic-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Mobile Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="dynamic-table-body">
                                <tr>
                                    <td class="row-number">1</td>
                                    <td><input type="text" name="names[]" class="form-control" required></td>
                                    <td><input type="text" name="mobile_numbers[]" class="form-control" required></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        @else
                        <p>{{ @$deleteMessage }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                @if ($crudAction == 'add')
                <button type="button" class="btn btn-primary file-form-submit" data-url="{{ url('/sms/manual/store') }}">
                    Save <i class="fa fa-save"></i>
                </button>
                @elseif ($crudAction == 'edit')
                <button type="button" class="btn btn-primary text-form-submit" data-url="{{ url('roles/update') }}">
                    Update <i class="fa fa-save"></i>
                </button>
                @else
                <button type="button" class="btn btn-danger text-form-submit" data-url="">
                    Yes Delete <i class="fa fa-trash"></i>
                </button>
                @endif
            </div>
        </div>
    </form>
</div>
