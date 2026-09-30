{{-- Modals partagés du layout utilisateur : mot de passe, aperçu PDF, déconnexion --}}

{{-- Password Update Modal --}}
<div class="modal fade" id="passwordUpdateModal" tabindex="-1" role="dialog"
    aria-labelledby="passwordUpdateModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="passwordUpdateModalLabel">{{ trans('trans.update_password') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="passwordUpdateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="current_password">{{ trans('trans.current_password') }}</label>
                        <input type="password" class="form-control" id="current_password" name="current_password"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">{{ trans('trans.new_password') }}</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation">{{ trans('trans.confirm_password') }}</label>
                        <input type="password" class="form-control" id="new_password_confirmation"
                            name="new_password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ trans('trans.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('trans.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- PDF Preview Modal --}}
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">{{ trans('trans.pdf_preview') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="pdfViewer" src="" width="100%" height="500px"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-dismiss="modal">{{ trans('trans.close') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Logout Modal --}}
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">@lang('trans.ready_to_leave')</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">{{ trans('trans.logout_word') }}</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button"
                    data-dismiss="modal">{{ __('trans.cancel') }}</button>
                <a href="{{ route('logout') }}" class="btn btn-danger">{{ trans('trans.logout') }}</a>
            </div>
        </div>
    </div>
</div>
