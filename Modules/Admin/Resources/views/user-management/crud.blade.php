<!-- Modal -->
<div class="modal-dialog ">
    <form class="text-form needs-validation" autocomplete="off" novalidate>
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
                        <div class="col-12 mb-4">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" id="username" value="{{ @$details->name }}" required>
                            <div class="invalid-feedback username"></div>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="{{ @$details->email }}" required>
                            <div class="invalid-feedback email"></div>
                        </div>


                        <div class="col-12 mb-4">
                            <label for="email" class="form-label">User Roles</label>
                            <select name="role" data-user="{{@$details->user_role}}" class="form-select js-example-basic-single" id="role" required>
                                
                            </select>
                            <div class="invalid-feedback role"></div>
                        </div>


                        <div class="col-12 mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" autocomplete="new-password" value="" required>
                            <div class="invalid-feedback password"></div>
                        </div>




                        


                        @else
                        <p>{{ @$deleteMessage }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                @if ($crudAction == 'add')
                <button type="button" class="btn btn-primary text-form-submit" data-url="{{ url('/user-management/store') }}">
                    Save <i class="fa fa-save"></i>
                </button>
                @elseif ($crudAction == 'edit')
                <button type="button" class="btn btn-primary text-form-submit" data-url="{{ url('/user-management/update') }}">
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