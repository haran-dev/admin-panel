@extends('layouts.app')

@section('page_css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Settings</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </nav>
    </div>

    <section class="section settings-section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Manage Settings</h5>

                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="settingsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="api-tab" data-bs-toggle="tab" data-bs-target="#api" type="button" role="tab">
                                    <i class="bi bi-key"></i> API Settings
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                                    <i class="bi bi-bell"></i> Notifications
                                </button>
                            </li>
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content pt-4" id="settingsTabContent">
                            <!-- API Settings Tab -->
                            <div class="tab-pane fade show active" id="api" role="tabpanel">
                                <form>
                                    @csrf
                                    <input type="hidden" name="action_id" id="action_id" value="{{ @$smsApiSetting->id }}" />
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">API Key</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="api_key" id="api_key" value="{{ old('api_key', $smsApiSetting?->api_key ?? '') }}">
                                                <button class="btn btn-outline-secondary toggle-api-key" type="button" tabindex="-1">
                                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback api_key"></div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Sender ID</label>
                                            <input type="text" class="form-control" name="sender_id" value="{{ old('sender_id', $smsApiSetting?->sender_id ?? '') }}">
                                            <div class="invalid-feedback sender_id"></div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">User ID</label>
                                            <input type="text" class="form-control" name="user_code" value="{{ old('user_code', $smsApiSetting?->user_code ?? '') }}">
                                            <div class="invalid-feedback user_code"></div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label d-block">Status</label>
                                            @if($activeStatus)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Active
                                            </span>
                                            @elseif($activeStatus === false || $activeStatus == 0)
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i> Inactive
                                            </span>
                                            @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-question-circle me-1"></i> N/A
                                            </span>
                                            @endif
                                        </div>


                                    </div>

                                    <div class="mb-3">
                                        <button type="button" class="btn btn-primary text-form-submit" data-url="{{ url('/api/notify/store') }}">
                                            <i class="bi bi-save"></i> Save Settings
                                        </button>
                                    </div>
                                </form>

                                <hr>

                                <h6>Remaining SMS</h6>
                                <div class="alert alert-info">
                                    You have <strong>{{ $balance ?? 'N/A' }}</strong> SMS messages left.
                                </div>
                            </div>

                            <!-- Notifications Tab -->
                            <div class="tab-pane fade" id="notifications" role="tabpanel">
                                <p class="text-muted">You can configure your email/SMS/push notifications here in future versions.</p>
                            </div>
                        </div>

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
    $(document).on('click', '.toggle-api-key', function() {
        const input = $('#api_key');
        const icon = $('#toggleIcon');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
</script>
@endsection