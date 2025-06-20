<!-- Modal -->
<div class="modal-dialog modal-xl">
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

                        <!-- Role Name -->
                        <div class="col-3 mb-4">
                            <label for="roles_name" class="form-label">Role Name</label>
                            <input type="text" name="roles_name" class="form-control" id="roles_name" value="{{ @$details->name }}" required>
                            <div class="invalid-feedback roles_name"></div>
                        </div>

                        <!-- Permissions Grouped by Label -->
                        <div class="col-12 mb-4">
                            <label class="form-label">Permissions</label>
                            <div class="invalid-feedback permissions"></div>
                            <div class="row">
                                @foreach($groupedPermissions as $label => $actions)
                                    <div class="col-md-4 mb-4"> {{-- Added mb-4 for vertical spacing --}}
                                        <div class="border p-3 rounded h-100">
                                            <strong class="text-capitalize">{{ $label }}</strong>
                                            <div class="mt-2">
                                                @foreach($actions as $item)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                        id="perm_{{ $item['id'] }}" value="{{ $item['id'] }}"
                                                        {{ isset($checkedPermissions) && in_array($item['id'], $checkedPermissions) ? 'checked' : '' }}>

                                                        <label class="form-check-label" for="perm_{{ $item['id'] }}">
                                                            {{ ucfirst($item['action']) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

           



                        @else
                        <p>{{ @$deleteMessage }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                @if ($crudAction == 'add')
                <button type="button" class="btn btn-primary text-form-submit" data-url="{{ url('/roles/store') }}">
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