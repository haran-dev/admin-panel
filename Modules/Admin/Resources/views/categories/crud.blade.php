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

                        <div class="card-body pt-2 mt-1">
                            <!-- Service Name -->
                            <div class="col-12 mb-4">
                                <label for="service_name" class="form-label">Category name</label>
                                <input type="text" name="category_name" class="form-control" id="category_name" value="{{ @$details->category_name }}" required>
                                <div class="category_name invalid-feedback"></div>
                            </div>
                        </div>

                        @else
                        {{ @$deleteMessage }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @if ($crudAction == 'add')
                <button type="button" class="btn btn-primary text-form-submit" data-url="{{ url('/categories/store') }}">Save <i class="fa fa-save"></i></button>
                @elseif ($crudAction == 'edit')
                <button type="button" class="btn btn-primary text-form-submit" data-url="{{ url('categories/update') }}">Update <i class="fa fa-save"></i></button>
                @else
                <button type="button" class="btn btn-danger text-form-submit" data-url="">Yes Delete <i class="fa fa-trash"></i></button>
                @endif
            </div>
        </div>
    </form>
</div>