<div class="modal fade" id="achievementModal" tabindex="-1" aria-labelledby="achievementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="achievementModalLabel">
                    <i class="fas fa-flag-checkered mr-2"></i>Motif d'achievement
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="achievementForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="achievementMotif" class="form-label">
                            Veuillez préciser le motif :
                        </label>
                        <textarea name="motif" id="achievementMotif" class="form-control" rows="4" 
                                  placeholder="Saisissez le motif d'achievement..." required></textarea>
                    </div>
                    <input type="hidden" name="id" id="achievementId">
                    <input type="hidden" name="table" id="achievementTable">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Annuler
                </button>
                <button type="button" class="btn btn-danger" onclick="submitAchievementForm()">
                    <i class="fas fa-flag-checkered mr-1"></i> Demander l'achievement
                </button>
            </div>
        </div>
    </div>
</div>