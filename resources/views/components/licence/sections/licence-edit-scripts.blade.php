<script>
    
// ===== Shared CRUD helpers — defined immediately (no jQuery dependency) =====
// These function definitions are safe to run before jQuery/SweetAlert are loaded
// because the BODIES only execute when the user clicks a button.

window.restoreBtn = function($btn) {
    if ($btn.length && $btn.data('original-html')) {
        $btn.prop('disabled', false).html($btn.data('original-html'));
        $btn.removeData('original-html');
    }
};

window.editSection = function(opts) {
    var $form = $('#' + opts.formId);
    $form.removeData('submitting');
    clearTimeout($form.data('submit-timer'));
    $('#' + opts.editField).val(opts.id);
    $('#' + opts.submitBtn).html('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> @lang("trans.update")');
    $('#' + opts.cancelBtn).show();
    $('html, body').animate({ scrollTop: $form.offset().top - 100 });
};

window.cancelSection = function(opts) {
    $('#' + opts.formId)[0].reset();
    $('#' + opts.editField).val('');
    $('#' + opts.formId).removeData('submitting');
    clearTimeout($('#' + opts.formId).data('submit-timer'));
    $('#' + opts.submitBtn).prop('disabled', false).html('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> @lang("trans.send")');
    $('#' + opts.cancelBtn).hide();
};

window.deleteSection = function(route, row, message) {
    Swal.fire({
        title: "Êtes-vous sûr ?",
        text: "Cette action est irréversible !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui, supprimer !",
        cancelButtonText: "Annuler"
    }).then(function(result) {
        $.ajax({
            url: route,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                _method: "DELETE"
            },
            success: function() {
                row.remove();
                Swal.fire({ title: 'Succès', icon: 'success', text: message, timer: 2000, showConfirmButton: false });
            },
            error: function(xhr) {
                var msg = 'Une erreur est survenue.';
                if (xhr.status === 419) {
                    msg = 'Session expirée. Veuillez rafraîchir la page.';
                } else if (xhr.status === 404) {
                    msg = 'Élément non trouvé.';
                } else if (xhr.status === 403) {
                    msg = 'Action non autorisée.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({ title: 'Erreur', icon: 'error', text: msg });
            }
        });
    });
};
</script>

@push('custom')
<script>
$(function() {
    $('.select2').select2()
});

$(document).ready(function() {
    // ===== Disable hidden fields before form submission =====
    // This prevents fields inside hidden sections (e.g. conditional qualification fields)
    // from being submitted with values when they are not visible.
    $(document).on('submit', 'form', function() {
        $(this).find('select, input, textarea').each(function() {
            var $field = $(this);
            if ($field.closest('[style*="display: none"]').length > 0) {
                $field.prop('disabled', true);
            }
        });
    });

    // ===== Double-submit prevention & loading states =====
    $(document).on('submit', 'form:not([data-no-protect])', function() {
        var $form = $(this);
        if ($form.data('submitting')) {
            return false;
        }
        $form.data('submitting', true);

        var $btn = $form.find('button[type="submit"]').first();
        if ($btn.length) {
            $btn.data('original-html', $btn.html());
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> En cours...');
        }

        var timer = setTimeout(function() {
            $form.removeData('submitting');
            var restoreBtn = $form.find('button[type="submit"]').first();
            if (restoreBtn.length && restoreBtn.data('original-html')) {
                restoreBtn.prop('disabled', false).html(restoreBtn.data('original-html'));
                restoreBtn.removeData('original-html');
            }
        }, 30000);
        $form.data('submit-timer', timer);
    });

    // Restore stuck buttons and re-enable hidden fields on AJAX errors
    $(document).on('ajaxError', function() {
        restoreBtn($('#saveMedicalCenterBtn'));
        $('button[type="submit"][disabled], button.btn-navy[disabled]').each(function() {
            var $btn = $(this);
            if ($btn.data('original-html')) {
                $btn.prop('disabled', false).html($btn.data('original-html'));
                $btn.removeData('original-html');
                var $form = $btn.closest('form');
                $form.removeData('submitting');
                clearTimeout($form.data('submit-timer'));
            }
        });
        // Re-enable all form fields that were disabled by the hidden-fields handler
        $('form').find('select, input, textarea').prop('disabled', false);
    });

    // ===== Centre modals =====
    $(document).on('click', '#addCenterBtn1, #addCenterBtn2', function() {
        $('#centerModal').modal('show');
    });
    $('#addMedicalCenterBtn').click(function() {
        $('#centerMedicalModal').modal('show');
    });
    $('#centerMedicalModal').on('hidden.bs.modal', function() {
        restoreBtn($('#saveMedicalCenterBtn'));
    });
    $(document).on('click', '#saveMedicalCenterBtn', function() {
        var $btn = $(this);
        $btn.data('original-html', $btn.html());
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> En cours...');
    });
});
</script>
@endpush
