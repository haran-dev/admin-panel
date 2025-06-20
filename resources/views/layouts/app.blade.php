<!DOCTYPE html>
<html lang="en">

<!-- Include the head partial -->
@include('partials.head')

<body>

    <style>
        #successToast.toast {
            width: auto !important;       
            max-width: 100%;              
            min-width: 0 !important;      
            padding: 0.3rem 1rem;       
        }

        #errorToast.toast
        {
        width: auto !important;
        max-width: 100%;
        min-width: 0 !important;
        padding: 0.3rem 1rem;
        }
    </style>

    <!-- Access Denied Modal -->
    <div class="modal fade" data-bs-backdrop="static" id="accessDeniedModal" tabindex="-1" aria-labelledby="accessDeniedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Center modal vertically -->
            <div class="modal-content border-2 border-danger shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="accessDeniedModalLabel">403 | Access Denied</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3 fs-5 text-muted">You do not have permission to perform this action.</p>
                    <img src="{{ asset('assets/img/Forbidden.gif') }}" alt="Access Denied" class="img-fluid mb-3" style="max-height: 200px;">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>


    <div id="spinner" style="display:none;">
        <div class="loader"></div>
    </div>

    <!-- Toast container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 12000;">

        <!-- Error Toast -->
        <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center">
                <div class="toast-body" id="toastErrorMessage"></div>
                <i class="fa-solid fa-circle-exclamation me-2"></i>
            </div>
        </div>

        <!-- Success Toast -->
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="polite" aria-atomic="true">
            <div class="d-flex align-items-center">
                <div class="toast-body" id="toastSuccessMessage"></div>
                <i class="fa-solid fa-circle-check  me-2"></i>
            </div>
        </div>
    </div>



    <div id="modal-view" data-bs-backdrop="static" class="modal fade">
    </div>


    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
        </div>
    </div>


    <div class="main-content" id="main-content">

        @include('partials.navbar')

        @include('partials.sidebar')

        @yield('content')






        <!-- Footer -->
        @include('partials.footer')
    </div>









    @yield('page_js')









</body>

</html>