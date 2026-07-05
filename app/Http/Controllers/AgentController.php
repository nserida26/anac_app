<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\CompetenceDemandeur;
use App\Models\Demande;
use App\Models\Demandeur;
use App\Models\EtatDemande;
use App\Models\QualificationDemandeur;
use App\Models\TrainingDemandeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $demandes = Demande::get();
        return view('agent.index', compact('demandes'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function imprimer($id)
    {
        $demande  = Demande::find($id);

        $demandeur = $demande->demandeur;
        $licence = $demande->licence;


        if ($licence->licence_valide) {
            # code...
            $medical_certificat = $demande->medicalExaminations()->orderByDesc('id')->where(
                'valider',
                true
            )->first();
            $qualification_ulm = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualification de Class')
                ->where('demandes.id', $id)
                ->whereNotNull('qualification_demandeurs.ulm')
                ->select('qualification_demandeurs.ulm', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->first();
            $qualification_rpas = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualification Type Machine')
                ->where('demandes.id', $id)
                ->whereNotNull('qualification_demandeurs.rpa')
                ->select('qualification_demandeurs.rpa', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_atcs = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualifications ATC')
                ->where('demandes.id', $id)
                ->select('qualification_demandeurs.atc', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_amts = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->leftJoin('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->where('qualifications.libelle', 'Qualifications AMT')
                ->where('demandes.id', $id)
                ->select(
                    'qualification_demandeurs.amt',
                    'qualification_demandeurs.date_examen',
                    'qualification_demandeurs.machine',
                    'type_avions.code'
                )
                ->orderByDesc('qualification_demandeurs.id')
                ->get();

            $qualification_types = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->leftJoin('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('type_avions.code', 'qualification_demandeurs.date_examen')
                ->where('qualifications.libelle', 'Qualification Type Machine')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_ifr = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('qualification_demandeurs.date_examen')
                ->where('qualifications.libelle', 'Qualification IFR')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->first();
            $qualification_classe = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.type_moteur')
                ->where('qualifications.libelle', 'Qualification de Class')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->first();
            $qualification_instructeur = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->join('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.machine', 'type_avions.code', 'qualification_demandeurs.type_privilege')
                ->where('qualifications.libelle', 'Qualification instructeur')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_examinateur = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->join('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.machine', 'type_avions.code', 'qualification_demandeurs.type_privilege')
                ->where('qualifications.libelle', 'Autorisation examinateur')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $competence_demandeur = CompetenceDemandeur::join('demandes', 'demandes.id', 'competence_demandeurs.demande_id')
                ->select('competence_demandeurs.date', 'competence_demandeurs.validite', 'competence_demandeurs.niveau')
                ->where('competence_demandeurs.type', 'Contrôle de compétence linguistique')
                ->where('demandes.id', $id)
                ->orderByDesc('competence_demandeurs.id')
                ->first();



            return view('agent.print', compact('qualification_ulm', 'qualification_atcs', 'qualification_amts', 'competence_demandeur', 'qualification_examinateur', 'qualification_instructeur', 'qualification_classe', 'qualification_ifr', 'qualification_types', 'demande', 'demandeur', 'licence', 'medical_certificat'));
        } else {
            return redirect()->back()->with('error', 'Licence n\' est pas encore valide.');
        }
    }

    //ETAT DEMANDE
    public function valider($id)
    {

        $etat_demande = EtatDemande::where('demande_id', $id)->update(
            [
                'agent_imprimer' => true
            ]
        );
        $activity = Activity::log('agent_imprimer',$id);

        return back()->with('success', 'Licence imprimée avec succès.');
    }
    //END ETAT DEMANDE


    public function signatureDemandeur($id)
    {
        $demandeur = Demandeur::find($id);
        return view('agent.sign', compact('demandeur'));
    }
    public function uploadDossier($id)
    {
        $demandeur = Demandeur::find($id);
        return view('agent.upload', compact('demandeur'));
    }

    public function save(Request $request, $id)
    {
        $request->validate([
            'signature' => 'required'
        ]);

        $image = $request->input('signature');
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = 'sc/' . uniqid() . '.png';

        Storage::disk('public')->put($imageName, base64_decode($image));
        $demandeur = Demandeur::find($id);
        $demandeur->update(['signature' => $imageName]);

        return response()->json(['success' => true, 'path' => $imageName]);
    }
    public function saveDossier(Request $request, $id)
    {
        $request->validate([
            'dossier' => 'required'
        ]);

        if ($request->hasFile('dossier')) {

            $dossierPath = $request->file('dossier')->store('dossiers', 'public');
        } else {
            $dossierPath = null;
        }
        $demandeur = Demandeur::find($id);
        $demandeur->update(['dossier' => $dossierPath]);

        return back()->with('success', 'Le dossier a été téléversé avec succès.');
    }
}
