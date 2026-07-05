<div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectionModalLabel">
                    <i class="fas fa-times-circle mr-2"></i>Motif de rejet
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectionForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="rejectionMotif" class="form-label">
                            Veuillez préciser le motif de rejet :
                        </label>
                        <textarea name="motif" id="rejectionMotif" class="form-control" rows="4" 
                                  placeholder="Saisissez le motif de rejet..." required></textarea>
                    </div>
                    <input type="hidden" name="id" id="rejectionId">
                    <input type="hidden" name="table" id="rejectionTable">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Annuler
                </button>
                <button type="button" class="btn btn-danger" onclick="submitRejectionForm()">
                    <i class="fas fa-times-circle mr-1"></i> Rejeter
                </button>
            </div>
        </div>
    </div>
</div>