// Click event for form submission
$(document).on("click", ".text-form-submit", function (e) {
    e.preventDefault();

    // Validation logic
    var forms = document.querySelectorAll(".needs-validation");
    forms.forEach(function (form) {
        var inputs = form.elements;
        let isValid = true;

        Array.from(inputs).forEach(function (input) {
            if (input.required && !input.value.trim()) {
                isValid = false;
                input.classList.add("is-invalid");
            } else {
                input.classList.remove("is-invalid");
            }
        });
    });

    // Gather form data
    var elem = $(this);
    var postData = elem.closest("form").serialize();
    var url = elem.data("url");

    console.log("postData =", postData);

    // Optional data attributes
    var spinnerContainer = $(".lds-roller"); // Spinner container
    spinnerContainer.hide(); // Ensure spinner is hidden initially

    // Apply blur to the content (excluding spinner)
    $("#content").css("filter", "blur(5px)");

    // Call the ajax_post function
    var post = ajax_post(url, postData, spinnerContainer);

    post.done(function (response) {
        notification_handler(response);
    })
        .fail(function (xhr) {
            // Handle any request errors
            $(".validationErrorDiv").html("An unexpected error occurred.");
        })
        .always(function () {
            // Hide spinner and remove blur effect
            spinnerContainer.hide();
            $("#content").css("filter", "blur(0px)");
        });
});

$(document).on("click", ".file-form-submit", function (e) {
    e.preventDefault();

    // Get the form element containing the button
    var form = $(this).closest("form")[0];
    var inputs = form.elements;
    let isValid = true;

    // Basic validation for required inputs
    Array.from(inputs).forEach(function (input) {
        if (input.required && !input.value.trim()) {
            isValid = false;
            input.classList.add("is-invalid");
        } else {
            input.classList.remove("is-invalid");
        }
    });

    if (!isValid) {
        return;
    }

    // Get the URL from data attribute
    var url = $(this).data("url");

    // Build FormData from the form (includes files and CSRF token)
    var formData = new FormData(form);

    // Spinner and UI blur
    var spinnerContainer = $(".lds-roller");
    spinnerContainer.show();
    $("#content").css("filter", "blur(5px)");

    // Send AJAX POST with FormData
    $.ajax({
        type: "POST",
        url: url,
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {
            notification_handler(response);
        },
        error: function (xhr) {
            $(".validationErrorDiv").html("File upload failed.");
        },
        complete: function () {
            spinnerContainer.hide();
            $("#content").css("filter", "blur(0px)");
        },
    });
});



$(document).on('change', '.status-switch', function () {
    var id = $(this).data('id');
    var status = $(this).is(':checked') ? 1 : 0;
    var url = $(this).data('url');
    var tokenData = $(this).data('token'); // this will be an object

    var spinnerContainer = $(".lds-roller");
    spinnerContainer.show();
    $("#content").css("filter", "blur(5px)");

    var postData = {
        id: id,
        status: status,
        _token: tokenData._token // include CSRF token here
    };

    var post = ajax_post(url, postData, spinnerContainer);

    post.done(function (response) {
        notification_handler(response);
    })
    .fail(function (xhr) {
        $(".validationErrorDiv").html("An unexpected error occurred.");
    })
    .always(function () {
        spinnerContainer.hide();
        $("#content").css("filter", "blur(0px)");
    });
});




// AJAX POST function
function ajax_post(url, data, spinnerContainer) {
    spinnerContainer.show(); // Show the spinner

    return $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        beforeSend: function () {
            console.log("Request started...");
        },
    }).always(function () {
        console.log("Request completed...");
        spinnerContainer.hide(); // Hide the spinner
    });
}

// add Data Function
$("#add-new-item").on("click", function (e) {
    e.preventDefault();
    var elem = $(this);
    var target = "";
    var spinnerContainer = $(".lds-roller");
    spinnerContainer.hide();
    var postData = elem.data("postdata");
    var url = elem.data("url");
    var post = ajax_post(url, postData, spinnerContainer, elem);
    post.done(function (response) {
        notification_handler(response);
    });

    post.fail(function (jqXHR) {
        if (jqXHR.status === 403) {
            var accessModal = new bootstrap.Modal(
                document.getElementById("accessDeniedModal")
            );
            accessModal.show();
        } else {
            // Handle other errors
        }
    });
});

// Notification Handler
function notification_handler(response) {
    console.log(response);
    var toShow = toShow === undefined ? false : toShow;
    var toHide = toHide === undefined ? false : toHide;
    var target = target === undefined ? false : target;
    var loader = loader === undefined ? false : loader;
    var elem = elem === undefined ? false : elem;
    if (loader == "inline") {
        $(".button-spin").hide();
    }
    if (response.status) {
        if (response.renderType == "modal") {
            render_view(response.data.view, response.data.target);
            $(response.data.target).modal("show");
        }

        if (
            response.renderType == "validation" ||
            response.renderType == "messagewithvalidation"
        ) {
            console.log(response.data);
            show_validation_errors(response.data);
            $(".btn-acc-prev").trigger("click"); // regiter form
        }

        if (response.status === "success") {
            showSuccessToast(response.message);
            if(response.target)
            {
                $(response.target).modal("hide");
                $('#basic-datatable').DataTable().ajax.reload(null, false);
            }
            else
            {
                window.location.href = response.redirect_url;
            } 
        }

        if (response.status === "error") {
            showErrorToast(response.message);
            if (response.target) {
                $(response.target).modal('hide');
                setTimeout(() => {
                    $('#basic-datatable').DataTable().ajax.reload(null, false);
                }, 300);
            }
        }



        if (response.status === "delete") {
            $.confirm({
                title: '<i class="fa fa-trash text-danger"></i> Delete?',
                content:
                    "This dialog will automatically cancel in 8 seconds if you don't respond.",
                type: "red",
                autoClose: "cancelAction|8000",
                buttons: {
                    deleteCategory: {
                        text: "Delete",
                        btnClass: "btn-red",
                        action: function () {
                            $('#basic-datatable').DataTable().ajax.reload(null, false);
                        },
                    },
                    cancelAction: {
                        text: "Cancel",
                        btnClass: "btn-default",
                        action: function () {
                            $('#basic-datatable').DataTable().ajax.reload(null, false);
                        },
                    },
                },
            });
        }

        if (response.status === "sms-success") {
            if (response.added.length === 0) {
                $.notify.addStyle("noIconError", {
                    html: `
                        <div>
                            <span data-notify-text/>
                        </div>
                    `,
                    classes: {
                        base: {
                            "background-color": "#F44336",
                            color: "#fff",
                            padding: "10px",
                            "border-radius": "5px",
                            "box-shadow": "0 2px 10px rgba(0,0,0,0.2)",
                            "max-width": "500px",
                        },
                    },
                });

                let message = "Alredy Exits";

                $.notify(message, {
                    style: "noIconError",
                    position: "top right",
                });
            }
            else
            {
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
            }

            if (
                Array.isArray(response.skipped) &&
                response.skipped.length > 0
            ) {
                let skippedMsg = response.skipped
                    .map(
                        (item) =>
                            `Name: ${item.name}, Number: ${item.number}, Reason: ${item.reason}`
                    )
                    .join("<br>");

                $.confirm({
                    title: '<i class="fa fa-info-circle text-warning"></i> Skipped Entries',
                    content:
                        skippedMsg +
                        "<hr><small>Please review the skipped entries.</small>",
                    type: "orange",
                    autoClose: "okAction|10000",
                    buttons: {
                        okAction: {
                            text: "OK",
                            btnClass: "btn-warning",
                            action: function () {
                                window.location.href = response.redirect_url;
                            },
                        },
                    },
                });
            } else {
                window.location.href = response.redirect_url;
            }
        }


        if (response.status === "email-success") {
            if (response.added.length === 0) {
                $.notify.addStyle("noIconError", {
                    html: `
                        <div>
                            <span data-notify-text/>
                        </div>
                    `,
                    classes: {
                        base: {
                            "background-color": "#F44336",
                            color: "#fff",
                            padding: "10px",
                            "border-radius": "5px",
                            "box-shadow": "0 2px 10px rgba(0,0,0,0.2)",
                            "max-width": "500px",
                        },
                    },
                });

                let message = "Alredy Exits";

                $.notify(message, {
                    style: "noIconError",
                    position: "top right",
                });
            }
            else
            {
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
            }

            if (
                Array.isArray(response.skipped) &&
                response.skipped.length > 0
            ) {
                let skippedMsg = response.skipped
                    .map(
                        (item) =>
                            `Name: ${item.name}, Email: ${item.email}, Reason: ${item.reason}`
                    )
                    .join("<br>");

                $.confirm({
                    title: '<i class="fa fa-info-circle text-warning"></i> Skipped Entries',
                    content:
                        skippedMsg +
                        "<hr><small>Please review the skipped entries.</small>",
                    type: "orange",
                    autoClose: "okAction|10000",
                    buttons: {
                        okAction: {
                            text: "OK",
                            btnClass: "btn-warning",
                            action: function () {
                                window.location.href = response.redirect_url;
                            },
                        },
                    },
                });
            } else {
                window.location.href = response.redirect_url;
            }
        }

        $(toHide).hide();
        $(toShow).show();
    } else {
        if (response.renderType == "messageWithRedirect") {
            error_notification(response.message);
            redirect(response.redirect);
        }
        if (
            response.renderType == "message" ||
            response.renderType == "messagewithvalidation"
        ) {
            error_notification(response.message);
        }
        if (
            response.renderType == "validation" ||
            response.renderType == "messagewithvalidation"
        ) {
            show_validation_errors(response.data);

            $(".btn-acc-prev").trigger("click"); // regiter form
        }
    }
}

// Model render
function render_view(htmlContent, target) {
    $(target).html(htmlContent);
}

// Feilds Validations
function show_validation_errors(validationData) {
    $.each(validationData, function (index, value) {
        // Select the error div corresponding to the field
        var errorDiv = $("." + index);

        console.log("errorDiv =", errorDiv);

        // Find the form control element (input, select, textarea, etc.)
        var formControlParent =
            errorDiv.prev(".form-floating").length > 0
                ? errorDiv.prev(".form-floating")
                : errorDiv;
        var formControlPrev = formControlParent.prev(
            ".form-control, .select2, .form-select"
        );
        var formControlFind = formControlParent.find(
            ".form-control, .select2, .form-select"
        );
        var formControl =
            formControlParent.prev(".form-floating").length > 0
                ? formControlFind
                : formControlPrev;

        // Add error message and apply error styles if the value is not empty
        if (value != "") {
            // Show the error message in the corresponding error div
            errorDiv.html(value); // This is where the validation message is inserted
            errorDiv.removeClass("success-msg");
            errorDiv.addClass("error-msg");

            // Add error styles to the form control (input, select, etc.)
            formControl.addClass("error-msg-border");

            // Apply the 'is-invalid' class to the input and change its border color to red
            $('[name="' + index + '"]').addClass("is-invalid");

            // Change the border color of the wrapper (input container)
            var wrapper = $('[name="' + index + '"]').closest(".input-wrapper");
            if (wrapper) {
                wrapper.css("border", "1px solid red");
            }

            // Ensure the invalid feedback element is shown
            var invalidFeedbackDiv = $('[name="' + index + '"]')
                .closest(".input-group")
                .find(".invalid-feedback");
            if (invalidFeedbackDiv) {
                invalidFeedbackDiv.show(); // Show the invalid feedback message
            }
        } else {
            // If there's no validation error, clear the error message and apply success styles
            errorDiv.html("");
            errorDiv.addClass("success-msg");
            errorDiv.removeClass("error-msg");
            formControl.removeClass("error-msg-border");

            // Remove the 'is-invalid' class and reset the border
            $('[name="' + index + '"]').removeClass("is-invalid");

            // Reset the wrapper border color to default
            var wrapper = $('[name="' + index + '"]').closest(".input-wrapper");
            if (wrapper) {
                wrapper.css("border", "1px solid #ced4da");
            }

            // Hide the invalid feedback element when no error is present
            var invalidFeedbackDiv = $('[name="' + index + '"]')
                .closest(".input-group")
                .find(".invalid-feedback");
            if (invalidFeedbackDiv) {
                invalidFeedbackDiv.hide(); // Hide the invalid feedback message when no error
            }
        }
    });
}

// Edit buttons
$(document).on("click", ".details-edit-button", function (e) {
    e.preventDefault();
    var elem = $(this);
    var target = "";
    var spinnerContainer = $(".lds-roller");
    spinnerContainer.hide();
    var postData = elem.data("postdata");
    var url = elem.data("url");
    var post = ajax_post(url, postData, spinnerContainer, elem);
    post.done(function (response) {
        notification_handler(response, target, spinnerContainer, elem);
    });
});

// Delete Buttons
$(document).on("click", ".row-delete-button", function (e) {
    e.preventDefault();
    var elem = $(this);
    var target = "";
    var spinnerContainer = $(".lds-roller");
    spinnerContainer.hide();
    var postData = elem.data("postdata");
    var url = elem.data("url");
    var post = ajax_post(url, postData, spinnerContainer, elem);
    post.done(function (response) {
        notification_handler(response, target, spinnerContainer, elem);
    });

    post.fail(function (jqXHR) {
        if (jqXHR.status === 403) {
            var accessModal = new bootstrap.Modal(
                document.getElementById("accessDeniedModal")
            );
            accessModal.show();
        } else {
            // Handle other errors
        }
    });
});

// Background Blur when model open
$(document).on("shown.bs.modal", ".modal", function () {
    $("#main-content").addClass("blur-background ");
});

// Remove Blur
$(document).on("hidden.bs.modal", ".modal", function () {
    $("#main-content").removeClass("blur-background");
});



function showErrorToast(message) {
  const toastEl = document.getElementById('errorToast');
  const toastMsg = document.getElementById('toastErrorMessage');
  toastMsg.textContent = message;
  const toast = new bootstrap.Toast(toastEl);
  toast.show();
}

function showSuccessToast(message) {
  const toastEl = document.getElementById('successToast');
  const toastMsg = document.getElementById('toastSuccessMessage');
  toastMsg.textContent = message;
  const toast = new bootstrap.Toast(toastEl);
  toast.show();
}
