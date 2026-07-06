<?php

use App\Http\Controllers\Admin\AutoriteController;
use App\Http\Controllers\Admin\CentreFormationController;
use App\Http\Controllers\Admin\QualificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TypeDocumentController;
use App\Http\Controllers\Admin\EvaluateurController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CompagnyController;
use App\Http\Controllers\Admin\ExaminateurController;
use App\Http\Controllers\Admin\SimulateurController;
use App\Http\Controllers\Admin\AeroportController;

use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\DetenteurLicenceController;

use App\Http\Controllers\Admin\TypeAvionController;
use App\Http\Controllers\Admin\TypeDemandeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Compagnie\AvionController;
use App\Http\Controllers\Compagnie\VolApprobationController;
use App\Http\Controllers\Compagnie\VolController;
use App\Http\Controllers\DemandeAutorisationController;

use App\Http\Controllers\CentreController;

use App\Http\Controllers\CompagnieController;

use App\Http\Controllers\DemandeController;
use App\Http\Controllers\DgDsvController;
use App\Http\Controllers\SmaSlaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|

*/


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::get('/test-verify/{user}', function ($user) {
            $user = App\Models\User::find($user);
            $user->markEmailAsVerified();
            return response()->json(['verified' => $user->hasVerifiedEmail()]);
        });
        Route::get('/refresh-csrf', function() {
            return response()->json(['token' => csrf_token()]);
        })->name('refresh-csrf');

        // Password Reset Routes
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
            ->middleware('guest')
            ->name('password.request');

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware('guest')
            ->name('password.email');

        Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
            ->middleware('guest')
            ->name('password.reset');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->middleware('guest')
            ->name('password.update');
        Route::get('/', function () {
            return view('welcome');
        })->name('welcome');
        Route::get('/errors/500', function () {
            return view('errors.500');
        })->name('errors.500');
        Route::get('public/autorisations/print/{autorisation}', [App\Http\Controllers\AdminController::class, 'print'])->name('public.autorisations.print');
        Auth::routes();
        
        Route::get('/email/verify', function () {
            return view('auth.verify');
        })->middleware('auth')->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

            if (!$request->hasValidSignature()) {
                abort(403);
            }

            $user = \App\Models\User::findOrFail($id);

            if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
                abort(403);
            }
            if ($user->markEmailAsVerified()) {
                event(new \Illuminate\Auth\Events\Verified($user));
            }
            return redirect('login')->with('verified', true);
        })->middleware(['signed'])->name('verification.verify');
        Route::post('/email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])
            ->middleware('auth')
            ->name('verification.resend');


        Route::middleware(['auth:web', 'verified'])->group(function () {
            Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
        });
        Route::put('/users/{user}/password', [App\Http\Controllers\Auth\UserController::class, 'updatePassword'])
            ->name('users.password.update')
            ->middleware('auth');
        Route::middleware(['auth:web', 'verified', 'role:user|dg|dta|admin|dsv|dsad|dsna|daf|compagnie'])
            ->prefix('state')
            ->group(function () {
                Route::post('/{demande}/update-state', [App\Http\Controllers\DemandeAutorisationController::class, 'updateDemandeState'])
                    ->name('update-state');
                Route::post('/{demande}/update-state-approbation', [App\Http\Controllers\CompagnieController::class, 'updateDemandeState'])
                    ->name('update-state-approbation');
                Route::post('/demande/{id}/update-state', [DemandeController::class, 'updateDemandeState'])->name('update-state-licence');

            });

        Route::middleware(['auth:web', 'verified', 'role:daf|dsv|admin|dg|dta'])
            ->prefix('daf')
            ->group(function () {

                Route::get('/', [App\Http\Controllers\DafController::class, 'index'])->name('daf');
                Route::get('/create/{ordre}', [App\Http\Controllers\DafController::class, 'create'])->name('daf.create');
                Route::get('/edit/{facture}', [App\Http\Controllers\DafController::class, 'edit'])->name('daf.edit');
                Route::get('/invoice/{id}', [App\Http\Controllers\DafController::class, 'invoice'])->name('daf.invoice');
                Route::get('/invoiceAutorisation/{id}', [App\Http\Controllers\DafController::class, 'invoiceAutorisation'])->name('daf.invoiceAutorisation');

Route::get('/compagny/details', [App\Http\Controllers\DgDsvController::class, 'getCompagnyDetails'])
    ->name('compagny.details');

Route::post('/compagny/send-reminder', [App\Http\Controllers\DgDsvController::class, 'sendReminder'])
    ->name('compagny.send.reminder');

Route::get('/rapports/compagnie/{id}', [App\Http\Controllers\DgDsvController::class, 'generateCompagnyReport'])
    ->name('rapports.compagnie');
            // Route pour la création de factures en masse
                Route::get('factures/bulk-create', [App\Http\Controllers\DafController::class, 'bulkCreate'])
                    ->name('daf.bulkCreate');
                
                // Route pour le stockage des factures en masse
                Route::post('factures/bulk-store', [App\Http\Controllers\DafController::class, 'bulkStore'])
                    ->name('daf.bulkStore');
                Route::get('/show/{paiement}', [App\Http\Controllers\DafController::class, 'show'])->name('daf.show');
                Route::post('/store', [App\Http\Controllers\DafController::class, 'store'])->name('daf.store');
                Route::post('/update/{facture}', [App\Http\Controllers\DafController::class, 'update'])->name('daf.update');
                Route::delete('/destroy/{facture}', [App\Http\Controllers\DafController::class, 'destroy'])
                    ->name('daf.destroy');
                Route::patch('/valider/{facture}', [App\Http\Controllers\DafController::class, 'valider'])->name('daf.valider');
                Route::patch('/valider_paiement/{paiement}', [App\Http\Controllers\DafController::class, 'validerPaiement'])->name('daf.valider_paiement');
            });
        Route::middleware(['auth:web', 'verified', 'role:dg|dsv|dta|dsad|dsna|daf'])
            ->prefix('dir')
            ->group(function () {

                Route::get('/dashboard/data', [DgDsvController::class, 'getData'])->name('dir.data');
                Route::post('/delete', [App\Http\Controllers\DgDsvController::class, 'delete'])->name('dir.delete');
                Route::get('/demandes/show/{id}', [App\Http\Controllers\DgDsvController::class, 'showDemandeAutorisation'])->name('dg.demandes.show');
                Route::get('/approbations/show/{id}', [App\Http\Controllers\DgDsvController::class, 'showDemandeApprobation'])->name('dg.approbations.show');

                Route::get('/dsna', [App\Http\Controllers\DgDsvController::class, 'index'])->name('dsna');
                Route::get('/dsad', [App\Http\Controllers\DgDsvController::class, 'index'])->name('dsad');
                Route::get('/dta', [App\Http\Controllers\DgDsvController::class, 'index'])->name('dta');

                Route::get('/licences', [App\Http\Controllers\DgDsvController::class, 'indexLicence'])->name('dir.licences');

                Route::get('/demandeLicences', [App\Http\Controllers\DgDsvController::class, 'indexDemandeLicence'])->name('dir.demandeLicences');
                Route::get('/demandeAutorisations', [App\Http\Controllers\DgDsvController::class, 'indexAutorisation'])->name('dir.demandeAutorisations');
                Route::get('/demandeApprobations', [App\Http\Controllers\DgDsvController::class, 'indexApprobation'])->name('dir.demandeApprobations');

                Route::post('/updateComments/{demandeAutorisation}', [App\Http\Controllers\DgDsvController::class, 'updateComments'])->name('dir.updateComments');

                Route::get('/dsv', [App\Http\Controllers\DgDsvController::class, 'index'])->name('dsv');
                Route::get('/dsv/show/{id}', [App\Http\Controllers\DgDsvController::class, 'show'])->name('dsv.show');
                Route::get('/dsv/create/{id}', [App\Http\Controllers\DgDsvController::class, 'create'])->name('dsv.create');
                Route::patch('/dsv/store/{demande}', [App\Http\Controllers\DgDsvController::class, 'store'])->name('dsv.store');

                Route::get('/dsv/ordre/{ordre}', [App\Http\Controllers\DgDsvController::class, 'print'])->name('dsv.ordre');

                Route::patch('/ordre/valider/{ordre}', [App\Http\Controllers\DgDsvController::class, 'valider'])->name('dsv.ordre.valider');
                Route::delete('/ordre/destroy/{ordre}', [App\Http\Controllers\DgDsvController::class, 'destroy'])
                    ->name('dsv.ordre.destroy');

                Route::patch('/dsv/annoter/{id}', [App\Http\Controllers\DgDsvController::class, 'annoterDemandeDSVtoPEL'])->name('dsv.annoter');
                Route::patch('/dsv/valider/{id}', [App\Http\Controllers\DgDsvController::class, 'validerDsv'])->name('dsv.valider');
                Route::post('/rejeter/{id}', [App\Http\Controllers\DgDsvController::class, 'rejeter'])->name('dir.rejeter');
                Route::post('/achiever/{id}', [App\Http\Controllers\DgDsvController::class, 'achiever'])->name('dir.achiever');


                Route::patch('/dsv/signer/{id}', [App\Http\Controllers\DgDsvController::class, 'signerDsv'])->name('dsv.signer');

                Route::patch('/dg/signer/{id}', [App\Http\Controllers\DgDsvController::class, 'signerDg'])->name('dsv.signerDg');
                Route::patch('/dg/valider/{id}', [App\Http\Controllers\DgDsvController::class, 'validerDg'])->name('dsv.validerDg');


                Route::get('/dg', [App\Http\Controllers\DgDsvController::class, 'index'])->name('dg');
                Route::get('/dg/show/{id}', [App\Http\Controllers\DgDsvController::class, 'show'])->name('dg.show');

                Route::patch('/dg/annoter/{id}', [App\Http\Controllers\DgDsvController::class, 'annoterDemandeDGtoDSV'])->name('dg.annoter');
                Route::patch('/dg/valider/{id}', [App\Http\Controllers\DgDsvController::class, 'validerDg'])->name('dg.valider');
                Route::patch('/dg/signer/{id}', [App\Http\Controllers\DgDsvController::class, 'signerDg'])->name('dg.signer');

                Route::post('/store', [App\Http\Controllers\DgDsvController::class, 'store_sc'])->name('dir.store');
                Route::get('/sc', [App\Http\Controllers\DgDsvController::class, 'sc'])->name('dir.sc');
            });
        Route::middleware(['auth:web', 'verified', 'role:centre'])
            ->prefix('centre')
            ->group(function () {

                Route::get('/', [App\Http\Controllers\CentreFormationController::class, 'index'])->name('centre');
                Route::get('/search', [App\Http\Controllers\CentreFormationController::class, 'search'])->name('centre.search');

                Route::get('/create/{demandeur}', [App\Http\Controllers\CentreFormationController::class, 'create'])->name('centre.create');
                Route::post('/store', [App\Http\Controllers\CentreFormationController::class, 'store'])->name('centre.store');
                Route::put('/update/{formation}', [App\Http\Controllers\CentreFormationController::class, 'update'])->name('centre.update');
                Route::delete('/destroy/{formation}', [App\Http\Controllers\CentreFormationController::class, 'destroy'])
                    ->name('centre.destroy');
            });
        Route::middleware(['auth:web', 'verified', 'role:compagnie|user'])
            ->get('/compagnie/finalize-login/{token}', [CompagnieController::class, 'finalizeLogin'])
            ->name('compagnie.finalize.login');
        Route::middleware(['auth:web', 'verified', 'role:compagnie'])
            ->prefix('compagnie')
            ->group(function () {
                Route::get('/login-as-user/{user}', [CompagnieController::class, 'loginAsUser'])
                    ->name('compagnie.login.as.user')
                    ->middleware('signed');
                Route::post('/request-login/{user}', [CompagnieController::class, 'processLoginRequest'])
                    ->name('compagnie.request.login');
                Route::get('/', [App\Http\Controllers\CompagnieController::class, 'index'])->name('compagnie');
                Route::get('/payer/{paiement}', [App\Http\Controllers\CompagnieController::class, 'pay'])->name('compagnie.pay');
                Route::post('/update/{paiement}', [App\Http\Controllers\CompagnieController::class, 'updatePaiement'])->name('compagnie.update');
                Route::patch('/valider/{demandeur}', [App\Http\Controllers\CompagnieController::class, 'valider'])->name('compagnie.valider');
                Route::patch('/rejeter/{demandeur}', [App\Http\Controllers\CompagnieController::class, 'rejeter'])->name('compagnie.rejeter');
                
                Route::delete('/destroy/{demande}', [App\Http\Controllers\CompagnieController::class, 'destroy'])
                    ->name('compagnie.destroy');
                Route::get('/print/{approbation}', [App\Http\Controllers\CompagnieController::class, 'print'])->name('compagnie.print');

                Route::post('/update-program-status', [CompagnieController::class, 'updateProgramStatus'])
                    ->name('compagnie.updateProgramStatus');
                // Approbation
                Route::post('/store', [App\Http\Controllers\CompagnieController::class, 'store'])->name('compagnie.store');
                Route::get('/edit/{id}', [App\Http\Controllers\CompagnieController::class, 'edit'])->name('compagnie.edit');
                // Itinéraires
                Route::post('/itineraires', [App\Http\Controllers\CompagnieController::class, 'storeItineraire']);
                Route::put('/itineraires/{id}', [App\Http\Controllers\CompagnieController::class, 'updateItineraire']);
                Route::delete('/itineraires/{id}', [App\Http\Controllers\CompagnieController::class, 'destroyItineraire']);

                // Documents
                Route::post('/documents', [App\Http\Controllers\CompagnieController::class, 'storeDocuments']);
                Route::delete('/documents/{id}', [App\Http\Controllers\CompagnieController::class, 'destroyDocument']);
                Route::get('/required-documents/{typeDemandeId}', [App\Http\Controllers\CompagnieController::class, 'getRequiredDocuments']);
                // Routes pour les avions
                Route::resource('avions', AvionController::class)->except(['create', 'edit']);
                // Routes pour les vols
                Route::resource('vols', VolApprobationController::class)->except(['create', 'edit']);
            });
        Route::middleware(['auth:web', 'verified', 'role:examinateur'])
            ->prefix('examinateur')
            ->group(function () {

                Route::get('/', [App\Http\Controllers\ExaminateurController::class, 'index'])->name('examinateur');
                Route::get('/search-licence', [App\Http\Controllers\ExaminateurController::class, 'searchLicenceForm'])->name('examinateur.search-licence');
                 Route::post('/search-by-licence', [App\Http\Controllers\ExaminateurController::class, 'searchByLicence'])->name('examinateur.search-by-licence');
                     Route::get('/search', [App\Http\Controllers\ExaminateurController::class, 'searchForm'])->name('examinateur.search');
    Route::get('/search-autocomplete', [App\Http\Controllers\ExaminateurController::class, 'searchAutocomplete'])->name('examinateur.search-autocomplete');
    Route::get('/advanced-search', [App\Http\Controllers\ExaminateurController::class, 'advancedSearch'])->name('examinateur.advanced-search');
                Route::get('/create/{demandeur}', [App\Http\Controllers\ExaminateurController::class, 'create'])->name('examinateur.create');
                
                Route::get('/edit/{examen}', [App\Http\Controllers\ExaminateurController::class, 'edit'])->name('examinateur.edit');
                Route::get('/show/{examen}', [App\Http\Controllers\ExaminateurController::class, 'show'])->name('examinateur.show');
                Route::post('/store', [App\Http\Controllers\ExaminateurController::class, 'store'])->name('examinateur.store');
                Route::post('/update/{examen}', [App\Http\Controllers\ExaminateurController::class, 'update'])->name('examinateur.update');
                Route::delete('/destroy/{examen}', [App\Http\Controllers\ExaminateurController::class, 'destroy'])
                    ->name('examinateur.destroy');
                Route::patch('/valider/{examen}', [App\Http\Controllers\ExaminateurController::class, 'valider'])->name('examinateur.valider');
            });

        Route::middleware(['auth:web', 'verified', 'role:evaluateur'])
            ->prefix('evaluateur')
            ->group(function () {

                Route::get('/', [App\Http\Controllers\EvaluateurController::class, 'index'])->name('evaluateur');
                Route::get('/edit/{examen}', [App\Http\Controllers\EvaluateurController::class, 'edit'])->name('evaluateur.edit');
                Route::get('/show/{examen}', [App\Http\Controllers\EvaluateurController::class, 'show'])->name('evaluateur.show');
                Route::post('/update/{examen}', [App\Http\Controllers\EvaluateurController::class, 'update'])->name('evaluateur.update');
                Route::patch('/valider/{table}/{id}', [App\Http\Controllers\EvaluateurController::class, 'valider'])->name('evaluateur.valider');
            });

        Route::middleware(['auth:web', 'verified', 'role:sma|sla|admin'])
            ->prefix('sec')
            ->group(function () {
                Route::get('/sma', [App\Http\Controllers\SmaSlaController::class, 'index'])->name('sma');
                Route::get('/sma/show/{id}', [App\Http\Controllers\SmaSlaController::class, 'show'])->name('sma.show');
                Route::patch('/sma/valider/{id}', [App\Http\Controllers\SmaSlaController::class, 'validerSma'])->name('sma.valider');
                Route::patch('/sma/annoter', [App\Http\Controllers\SmaSlaController::class, 'annoter'])->name('sma.annoter');
                Route::patch('/sma/relaunch/{examen}', [App\Http\Controllers\SmaSlaController::class, 'relaunch'])->name('sma.relaunch');

                Route::get('/sla', [App\Http\Controllers\SmaSlaController::class, 'index'])->name('sla');
                Route::get('/sla/show/{id}', [App\Http\Controllers\SmaSlaController::class, 'show'])->name('sla.show');
                Route::patch('/sla/valider/{id}', [App\Http\Controllers\SmaSlaController::class, 'validerSla'])->name('sla.valider');
                Route::post('/rejeter', [App\Http\Controllers\SmaSlaController::class, 'handleApproval'])->name('handle_approval');

                //valider
                Route::patch('/sma/valider_examen/{examen}', [App\Http\Controllers\SmaSlaController::class, 'valider'])->name('sma.valider_examen');
                Route::post('/dsv/{demande}/checklist', [SmaSlaController::class, 'checklist'])
                    ->name('dsv.checklist');
            });
        Route::middleware(['auth:web', 'verified', 'role:user'])
            ->prefix('user')
            ->group(function () {

                Route::get('/login-requests', [App\Http\Controllers\DemandeurController::class, 'loginRequests'])
                    ->name('user.login.requests');
                Route::post('/approve-login/{request}', [App\Http\Controllers\DemandeurController::class, 'approveLogin'])
                    ->name('user.approve.login');
                Route::get('/', [App\Http\Controllers\DemandeController::class, 'index'])->name('user');
                Route::get('/profile', [App\Http\Controllers\DemandeurController::class, 'index'])->name('user.profile');
                Route::post('/profile/store', [App\Http\Controllers\DemandeurController::class, 'store'])->name('user.profile.store');
                Route::put('/profile/update', [App\Http\Controllers\DemandeurController::class, 'update'])->name('user.profile.update');
                //** ------------ Autorisations ----------- **//
                Route::get('/autorisations/print/{autorisation}', [App\Http\Controllers\AdminController::class, 'print'])->name('user.print');
                Route::get('/autorisations/payer/{paiement}', [App\Http\Controllers\DemandeAutorisationController::class, 'autorisationPay'])->name('user.autorisations.autorisationPay');
                Route::post('/storeDemandeAutorisation', [App\Http\Controllers\DemandeAutorisationController::class, 'store'])->name('user.autorisations.store');
                Route::post('/autorisations/{id}', [App\Http\Controllers\DemandeAutorisationController::class, 'store'])->name('user.autorisations.update');
                Route::get('/autorisations/edit/{id}', [App\Http\Controllers\DemandeAutorisationController::class, 'edit'])->name('user.autorisations.edit');
                Route::delete('/autorisations/destroy/{id}', [App\Http\Controllers\DemandeAutorisationController::class, 'destroy'])
                    ->name('user.autorisations.destroy');
                
                Route::post('/personnes-deces', [App\Http\Controllers\DemandeAutorisationController::class, 'storeDeceasedPerson'])->name('personnes-deces.store');
                Route::put('/personnes-deces/{id}', [App\Http\Controllers\DemandeAutorisationController::class, 'updateDeceasedPerson'])->name('personnes-deces.update');
                Route::delete('/personnes-deces/{id}', [App\Http\Controllers\DemandeAutorisationController::class, 'destroyDeceasedPerson'])->name('personnes-deces.destroy');
                // Routes pour les avions
                Route::resource('avions', AvionController::class)->except(['create', 'edit']);
                // Routes pour les vols
                Route::resource('vols', VolController::class)->except(['create', 'edit']);
                Route::post('/aeroports', [App\Http\Controllers\DemandeAutorisationController::class, 'storeAeroports'])->name('user.store_aeroports');

                Route::post('/compagnies', [App\Http\Controllers\DemandeAutorisationController::class, 'storeCompagnies'])->name('user.store_compagnies');
                Route::post('/type-avions/store', [App\Http\Controllers\DemandeAutorisationController::class, 'store_type_avions'])
                    ->name('user.store_type_avions');
                Route::post('/mdns', [DemandeAutorisationController::class, 'storeMdn'])->name('mdns.store');
                Route::put('/mdns/{id}', [DemandeAutorisationController::class, 'updateMdn'])->name('mdns.update');
                Route::delete('/mdns/{id}', [DemandeAutorisationController::class, 'destroyMdn'])->name('mdns.destroy');
                // Équipage
                Route::post('/equipes', [DemandeAutorisationController::class, 'storeEquipe']);
                Route::put('/equipes/{id}', [DemandeAutorisationController::class, 'updateEquipe']);
                Route::delete('/equipes/{id}', [DemandeAutorisationController::class, 'destroyEquipe']);

                // Fret
                Route::post('/frets', [DemandeAutorisationController::class, 'storeFret']);
                Route::put('/frets/{id}', [DemandeAutorisationController::class, 'updateFret']);
                Route::delete('/frets/{id}', [DemandeAutorisationController::class, 'destroyFret']);



                // Receiving Parties
                Route::post('/receiving-parties', [DemandeAutorisationController::class, 'storeReceivingParty']);
                Route::post('/receiving-parties/{id}', [DemandeAutorisationController::class, 'updateReceivingParty']); 
                Route::delete('/receiving-parties/{id}', [DemandeAutorisationController::class, 'destroyReceivingParty']);

                // Assistance
                Route::post('/assistance', [DemandeAutorisationController::class, 'storeAssistance']);

                // Documents
                Route::post('/documents', [DemandeAutorisationController::class, 'storeDocuments']);
                Route::put('/documents/{id}', [DemandeAutorisationController::class, 'updateDocument']);
                Route::delete('/documents/{id}', [DemandeAutorisationController::class, 'destroyDocument']);
                Route::get('/required-documents/{typeDemandeId}', [DemandeAutorisationController::class, 'getRequiredDocuments']);

                Route::get('/autorisations/invoice/{id}', [App\Http\Controllers\DemandeAutorisationController::class, 'invoice'])->name('user.autorisations.invoice');
                //** ------------ Licences ----------- **//
                Route::get('/validation/{validation}', [App\Http\Controllers\DemandeController::class, 'getLicenceValidation'])->name('user.validation');

                Route::get('/licences/invoice/{id}', [App\Http\Controllers\DemandeController::class, 'invoice'])->name('user.licences.invoice');

                Route::get('/imprimer/{id}', [App\Http\Controllers\DemandeController::class, 'imprimer'])->name('user.imprimer');
                Route::get('/create', [App\Http\Controllers\DemandeController::class, 'create'])->name('user.create');

                Route::get('/licences/payer/{id}', [App\Http\Controllers\DemandeController::class, 'pay'])->name('user.licences.pay');
                Route::post('/update/{paiement}', [App\Http\Controllers\DemandeController::class, 'update'])->name('user.licences.update');


                Route::get('/licences/edit/{id}', [App\Http\Controllers\DemandeController::class, 'edit'])->name('user.licences.edit');
                Route::post('/storeDemandeLicence', [App\Http\Controllers\DemandeController::class, 'store'])->name('user.store');


                Route::delete('/licences/destroy/{id}', [App\Http\Controllers\DemandeController::class, 'destroy'])
                    ->name('user.licences.destroy');
                Route::patch('/validate/{id}', [App\Http\Controllers\DemandeController::class, 'validateDemande'])->name('user.validate');

                Route::post('/store_mcentres', [App\Http\Controllers\DemandeController::class, 'storeMcentres'])->name('user.store_mcentres');
                Route::post('/store_centres', [App\Http\Controllers\DemandeController::class, 'storeCentres'])->name('user.store_centres');


                Route::post('/store_licences', [App\Http\Controllers\DemandeController::class, 'storeLicences'])->name('user.store_licences');

                Route::put('/update_licences/{licence_demandeur}', [App\Http\Controllers\DemandeController::class, 'updateLicences'])->name('user.update_licences');
                Route::delete('/destroy_licences/{licence_demandeur}', [App\Http\Controllers\DemandeController::class, 'destroyLicences'])
                    ->name('user.destroy_licences');

                Route::post('/store_qualifications', [App\Http\Controllers\DemandeController::class, 'storeQualifications'])->name('user.store_qualifications');
                Route::post('/update_qualifications/{qualification_demandeur}', [App\Http\Controllers\DemandeController::class, 'updateQualifications'])->name('user.update_qualifications');
                Route::delete('/destroy_qualifications/{qualification_demandeur}', [App\Http\Controllers\DemandeController::class, 'destroyQualifications'])
                    ->name('user.destroy_qualifications');
                Route::get('/qualifications/get/{id}', [App\Http\Controllers\DemandeController::class, 'getQualification'])
                    ->name('user.get_qualification');
                Route::post('/store_aptitudes', [App\Http\Controllers\DemandeController::class, 'storeAptitudes'])->name('user.store_aptitudes');

                Route::put('/update_aptitudes/{medical_examination}', [App\Http\Controllers\DemandeController::class, 'updateAptitudes'])->name('user.update_aptitudes');

                Route::delete('/destroy_aptitudes/{medical_examination}', [App\Http\Controllers\DemandeController::class, 'destroyAptitudes'])
                    ->name('user.destroy_aptitudes');
                Route::post('/store_expriences', [App\Http\Controllers\DemandeController::class, 'storeExpriences'])->name('user.store_experiences');

                Route::put('/update_experiences/{experience_demandeur}', [App\Http\Controllers\DemandeController::class, 'updateExpriences'])->name('user.update_experiences');

                Route::delete('/destroy_experiences/{experience_demandeur}', [App\Http\Controllers\DemandeController::class, 'destroyExpriences'])
                    ->name('user.destroy_experiences');
                Route::post('/store_competences', [App\Http\Controllers\DemandeController::class, 'storeCompetences'])->name('user.store_competences');

                Route::put('/update_competences/{competence_demandeur}', [App\Http\Controllers\DemandeController::class, 'updateCompetences'])->name('user.update_competences');

                Route::delete('/destroy_competences/{competence_demandeur}', [App\Http\Controllers\DemandeController::class, 'destroyCompetences'])
                    ->name('user.destroy_competences');
                Route::post('/store_entrainements', [App\Http\Controllers\DemandeController::class, 'storeEntrainements'])->name('user.store_entrainements');

                Route::put('/update_entrainements/{entrainement_demandeur}', [App\Http\Controllers\DemandeController::class, 'updateEntrainements'])->name('user.update_entrainements');

                Route::delete('/destroy_entrainements/{entrainement_demandeur}', [App\Http\Controllers\DemandeController::class, 'destroyEntrainements'])
                    ->name('user.destroy_entrainements');
                Route::post('/store_documents', [App\Http\Controllers\DemandeController::class, 'storeDocuments'])->name('user.store_documents');

                Route::put('/update_documents/{document}', [App\Http\Controllers\DemandeController::class, 'updateDocuments'])->name('user.update_documents');

                Route::delete('/destroy_documents/{document}', [App\Http\Controllers\DemandeController::class, 'destroyDocuments'])
                    ->name('user.destroy_documents');
                Route::post('/store_formations', [App\Http\Controllers\DemandeController::class, 'storeFormations'])->name('user.store_formations');
                Route::put('/update_formations/{formation}', [App\Http\Controllers\DemandeController::class, 'updateFormations'])->name('user.update_formations');
                Route::delete('/destroy_formations/{formation}', [App\Http\Controllers\DemandeController::class, 'destroyFormations'])
                    ->name('user.destroy_formations');

                Route::post('/store_interruptions', [App\Http\Controllers\DemandeController::class, 'storeInterruptions'])->name('user.store_interruptions');

                Route::put('/update_interruptions/{interruption_demandeur}', [App\Http\Controllers\DemandeController::class, 'updateInterruptions'])->name('user.update_interruptions');

                Route::delete('/destroy_interruptions/{interruption_demandeur}', [App\Http\Controllers\DemandeController::class, 'destroyInterruptions'])
                    ->name('user.destroy_interruptions');
                Route::post('/store_maintenances', [App\Http\Controllers\DemandeController::class, 'storeMaintenances'])->name('user.store_maintenances');

                Route::put('/update_maintenances', [App\Http\Controllers\DemandeController::class, 'storeMaintenances'])->name('user.update_maintenances');

                Route::delete('/destroy_maintenances/{experience_maintenance_demandeur}', [App\Http\Controllers\DemandeController::class, 'destroyMaintenances'])
                    ->name('user.destroy_maintenances');

                Route::post('/store_employeurs', [App\Http\Controllers\DemandeController::class, 'storeEmployeurs'])->name('user.store_employeurs');

                Route::put('/update_employeurs/{employeur_demandeur}', [App\Http\Controllers\DemandeController::class, 'updateEmployeurs'])->name('user.update_employeurs');

                Route::delete('/destroy_employeurs/{employeur_demandeur}', [App\Http\Controllers\DemandeController::class, 'destroyEmployeurs'])
                    ->name('user.destroy_employeurs');
                        // Dashboard
    Route::get('/demandeur/dashboard', [DetenteurLicenceController::class, 'dashboard'])->name('demandeur.dashboard');
    
    // Gestion des formations
    Route::get('/demandeur/formations/create', [DetenteurLicenceController::class, 'createFormation'])->name('demandeur.create.formation');
    Route::post('/demandeur/formations/store', [DetenteurLicenceController::class, 'storeFormation'])->name('demandeur.store.formation');
    Route::get('/demandeur/formations', [DetenteurLicenceController::class, 'listFormations'])->name('demandeur.formations.list');
    Route::get('/demandeur/formation/{id}', [DetenteurLicenceController::class, 'showFormation'])->name('demandeur.show.formation');
    Route::post('/demandeur/formation/{id}/status', [DetenteurLicenceController::class, 'updateFormationStatus'])->name('demandeur.update.status');
    
    // API Routes pour AJAX
    Route::get('/demandeur/search-by-licence', [DetenteurLicenceController::class, 'searchByLicence'])->name('demandeur.search.by.licence');
    Route::get('/demandeur/demandeur-details', [DetenteurLicenceController::class, 'getDemandeurDetails'])->name('demandeur.get.details');
            });

        Route::middleware(['auth:web', 'verified', 'role:admin|dta'])
            ->prefix('vr')
            ->group(function () {
                Route::post('/validate/avion', [AdminController::class, 'validateAvion'])->name('validate.avion');
                Route::post('/validate/vol', [AdminController::class, 'validateVol'])->name('validate.vol');
                Route::post('/validate/itineraire', [AdminController::class, 'validateItineraire'])->name('validate.itineraire');
                Route::post('/validate/document', [AdminController::class, 'validateDocument'])->name('validate.document');
                Route::post('/send-notifications/{id}', [App\Http\Controllers\DemandeAutorisationController::class, 'sendNotifications'])->name('send.notifications');
                Route::post('/validate-all-items', [App\Http\Controllers\AdminController::class, 'validateAllItems'])
                    ->name('validate.all.items');
                Route::post('/rejeter', [App\Http\Controllers\AdminController::class, 'handleApproval'])->name('vr.handle_approval');
            });
        Route::middleware(['auth:web', 'verified', 'role:admin|dta'])
            ->prefix('vi')
            ->group(function () {
                Route::post('/validate/avion', [AdminController::class, 'validateAvionVi'])->name('validate.avionvi');
                Route::post('/validate/vol', [AdminController::class, 'validateVolVi'])->name('validate.volvi');
                Route::post('/validate/equipage', [AdminController::class, 'validateEquipageVi'])->name('validate.equipagevi');
                Route::post('/validate/fret', [AdminController::class, 'validateFretVi'])->name('validate.fretvi');
                Route::post('/validate/receiving', [AdminController::class, 'validateReceivingVi'])->name('validate.receiving_partyvi');
                Route::post('/validate/itinerarie', [AdminController::class, 'validateItineraireVi'])->name('validate.itinerairevi');
                Route::post('/validate/document', [AdminController::class, 'validateDocumentVi'])->name('validate.documentvi');
                Route::post('/validate/demande', [AdminController::class, 'validateDemandeVi'])->name('validate.demandevi');
                Route::get('/autorisations/print/{autorisation}', [App\Http\Controllers\AdminController::class, 'print'])->name('autorisations.print');
            });
        Route::middleware(['auth:web', 'verified', 'role:admin|dsv|dg'])
            ->prefix('admin')
            ->group(function () {
                Route::resource('checklists', ChecklistController::class);
                Route::post('/checklists/store-multiple', [ChecklistController::class, 'storeMultiple'])->name('checklists.store.multiple');
                Route::get('checklists-by-type-demande/{typeDemandeId}', [ChecklistController::class, 'getByTypeDemande'])->name('checklists.by-type-demande');
                Route::get('checklists-by-type-licence/{typeLicenceId}', [ChecklistController::class, 'getByTypeLicence'])->name('checklists.by-type-licence');
                Route::get('demandes/{demande}/checklist', [App\Http\Controllers\Admin\ChecklistDemandeController::class, 'edit'])
                    ->name('admin.checklists.edit');
                Route::put('demandes/{demande}/checklist', [App\Http\Controllers\Admin\ChecklistDemandeController::class, 'update'])
                    ->name('admin.checklists.update');
                Route::prefix('pieces')->group(function () {
                    Route::post('/store', [App\Http\Controllers\AdminController::class, 'storeDemandePiece'])->name('pieces.store');
                    Route::put('/update/{id}', [App\Http\Controllers\AdminController::class, 'updateDemandePiece'])->name('pieces.update');
                    Route::delete('/destroy/{id}', [App\Http\Controllers\AdminController::class, 'destroyDemandePiece'])->name('pieces.destroy');
                    
                });
                Route::post('/approve-all/{id}', [AdminController::class, 'approveAllStates'])
                    ->name('approve.all.states');
                Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
                Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');

                Route::get('/sc', [App\Http\Controllers\AdminController::class, 'sc'])->name('admin.sc');
                Route::post('/sc/store', [App\Http\Controllers\AdminController::class, 'store_sc'])->name('admin.sc.store');
                Route::post('/delete', [App\Http\Controllers\AdminController::class, 'delete'])->name('admin.sc.delete');


                Route::get('/demandeurs', [App\Http\Controllers\AdminController::class, 'indexDemandeur'])->name('demandeurs');
                Route::get('/demandeurs/show/{id}', [App\Http\Controllers\AdminController::class, 'showDemandeur'])->name('demandeurs.show');
                Route::post('/demandeurs/update/{demandeur}', [App\Http\Controllers\AdminController::class, 'updateDemandeur'])->name('demandeurs.update');
                Route::patch('/demandeurs/{demandeur}/toggle-status', [App\Http\Controllers\AdminController::class, 'toggleStatus'])->name('demandeurs.toggle-status');


                Route::get('/demandes', [App\Http\Controllers\AdminController::class, 'index'])->name('demandes');
                Route::get('/demandes/show/{id}', [App\Http\Controllers\AdminController::class, 'show'])->name('demandes.show');
                Route::patch('/demandes/annoter/{id}', [App\Http\Controllers\AdminController::class, 'annoterDemande'])->name('admin.annoter');
                Route::patch('/demandes/valider/{id}', [App\Http\Controllers\AdminController::class, 'valider'])->name('admin.valider');
                Route::post('/demandes/update/{demande}', [App\Http\Controllers\AdminController::class, 'update'])->name('demandes.update');
                Route::patch('/demandes/generer/{id}', [App\Http\Controllers\AdminController::class, 'generateLicence'])->name('admin.generer');
                Route::get('/demandes/validation/{validation}', [App\Http\Controllers\AdminController::class, 'generateLicenceValidation'])->name('admin.validation');
                Route::patch('/demandes/signer/{id}', [App\Http\Controllers\AdminController::class, 'signer'])->name('admin.signer');
                Route::delete('/demandes/{demande}', [App\Http\Controllers\AdminController::class, 'destroy'])
                    ->name('admin.destroy');
// Make sure the route is properly defined
Route::post('/demandes/{id}/update-type', [App\Http\Controllers\AdminController::class, 'updateType'])
    ->name('admin.update-type');


                Route::get('/licences', [App\Http\Controllers\AdminController::class, 'licences'])->name('licences');
                Route::post('licences/send-notification/{licence}', [App\Http\Controllers\AdminController::class, 'sendExpiryNotification'])->name('licences.send-notification');
                Route::get('licences/send-all-notifications', [App\Http\Controllers\AdminController::class, 'sendAllExpiryNotifications'])
                    ->name('licences.send-all-notifications');
                
                Route::get('/licences/show/{licence}', [App\Http\Controllers\AdminController::class, 'showLicence'])->name('licences.show');
                Route::put('/licences/update/{licence}', [App\Http\Controllers\AdminController::class, 'updateLicence'])->name('licences.update');
                // Dans web.php
                Route::put('/licences/{id}/update-calculation', [App\Http\Controllers\AdminController::class, 'updateCalculation'])
                    ->name('licences.update.calculation');
                Route::post('licences/update-photo/{id}', [App\Http\Controllers\AdminController::class, 'updatePhoto'])->name('licences.update.photo');
                Route::patch('/licences/valider/{licence}', [App\Http\Controllers\AdminController::class, 'validerLicence'])->name('licences.valider');
                Route::get('/licences/imprimer/{id}', [App\Http\Controllers\AdminController::class, 'imprimer'])->name('licences.imprimer');
                Route::patch('/licences/bloquer/{licence}', [App\Http\Controllers\AdminController::class, 'bloquerLicence'])->name('licences.bloquer');
                Route::patch('/licences/supprimer/{licence}', [App\Http\Controllers\AdminController::class, 'supprimerLicence'])->name('licences.supprimer');
                Route::get('/authentification/imprimer/{id}', [App\Http\Controllers\AdminController::class, 'imprimerAuth'])->name('authentifications.imprimer');


                Route::get('/validations', [App\Http\Controllers\AdminController::class, 'validations'])->name('validations');
                //Route::get('/validations/imprimer/{id}', [App\Http\Controllers\AdminController::class, 'imprimerValidation'])->name('validations.imprimer');
                Route::patch('/validations/supprimer/{validation}', [App\Http\Controllers\AdminController::class, 'supprimerValidation'])->name('validations.supprimer');
                
                Route::get('/cartes', [App\Http\Controllers\AdminController::class, 'carteStagiares'])->name('cartes');
                Route::get('/cartes/imprimer/{id}', [App\Http\Controllers\AdminController::class, 'imprimerCarte'])->name('cartes.imprimer');
                Route::patch('/cartes/supprimer/{carte}', [App\Http\Controllers\AdminController::class, 'supprimerCarte'])->name('cartes.supprimer');
            

                Route::get('/demandeAutorisations', [App\Http\Controllers\AdminController::class, 'index_vi'])->name('demandeAutorisations');
                Route::get('/demandeAutorisations/show/{id}', [App\Http\Controllers\AdminController::class, 'show_vi'])->name('demandeAutorisations.show');
                Route::get('/autorisations', [App\Http\Controllers\AdminController::class, 'autorisations'])->name('autorisations');
                Route::get('/autorisations/show/{autorisation}', [App\Http\Controllers\AdminController::class, 'showAutorisation'])->name('autorisations.show');
                


                Route::post('/autorisations/rejeter', [App\Http\Controllers\AdminController::class, 'rejeter'])->name('autorisations.rejeter');

                Route::get('/demandeApprobations', [App\Http\Controllers\AdminController::class, 'index_vr'])->name('demandeApprobations');
                Route::get('/demandeApprobations/show/{id}', [App\Http\Controllers\AdminController::class, 'show_vr'])->name('demandeApprobations.show');
                Route::get('/approbations', [App\Http\Controllers\AdminController::class, 'approbations'])->name('approbations');
                Route::get('/approbations/show/{approbation}', [App\Http\Controllers\AdminController::class, 'showApprobation'])->name('approbations.show');
                Route::get('/approbations/print/{approbation}', [App\Http\Controllers\AdminController::class, 'printApprobation'])->name('approbations.print');


                Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
                Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

                Route::resource('roles', RoleController::class);
                Route::resource('users', UserController::class);
                Route::resource('qualifications', QualificationController::class);
                Route::resource('autorites', AutoriteController::class);
                Route::resource('settings', SettingController::class);
                Route::resource('type-documents', TypeDocumentController::class);
                Route::resource('compagnies', CompagnyController::class);
                Route::resource('centre-formations', CentreFormationController::class);
                Route::resource('evaluateurs', EvaluateurController::class);
                Route::resource('examinateurs', ExaminateurController::class);
                Route::resource('simulateurs', SimulateurController::class);
                Route::resource('type-avions', TypeAvionController::class);
                Route::resource('type-demandes', TypeDemandeController::class);
                Route::resource('aeroports', AeroportController::class);
                Route::get('aeroports/map', [AeroportController::class, 'map'])->name('aeroports.map');
Route::get('aeroports/api/list', [AeroportController::class, 'apiIndex'])->name('aeroports.api');
Route::post('aeroports/import', [AeroportController::class, 'import'])->name('aeroports.import');
Route::get('aeroports/export', [AeroportController::class, 'export'])->name('aeroports.export');
                Route::post('users/{user}/assign-roles', [UserController::class, 'assignRoles'])->name('users.assign-roles');
            });
        Route::middleware(['auth:web', 'verified', 'role:agent'])
            ->prefix('agent')
            ->group(function () {

                Route::get('/sign/{id}', [App\Http\Controllers\AgentController::class, 'signatureDemandeur'])->name('agent.sign');
                Route::get('/upload/{id}', [App\Http\Controllers\AgentController::class, 'uploadDossier'])->name('agent.upload');
                Route::post('/save/{id}', [App\Http\Controllers\AgentController::class, 'save'])->name('agent.save');
                Route::post('/dossier/{id}', [App\Http\Controllers\AgentController::class, 'saveDossier'])->name('agent.dossier');

                Route::get('/', [App\Http\Controllers\AgentController::class, 'index'])->name('agent');
                Route::get('/imprimer/{id}', [App\Http\Controllers\AgentController::class, 'imprimer'])->name('agent.imprimer');
                Route::patch('/valider/{id}', [App\Http\Controllers\AgentController::class, 'valider'])->name('agent.valider');
            });
        // routes/web.php

Route::middleware(['auth:web', 'verified', 'role:centre'])->prefix('centre')->name('centre.')->group(function () {
    Route::get('/', [CentreController::class, 'index'])->name('index');
    Route::get('/create', [CentreController::class, 'create'])->name('create');
    Route::post('/store', [CentreController::class, 'store'])->name('store');
    Route::get('/formation/{id}', [CentreController::class, 'show'])->name('show');
    // Gestion des instructeurs
    Route::get('/instructeurs', [CentreController::class, 'instructeurs'])->name('instructeurs');
    Route::post('/instructeurs/store', [CentreController::class, 'storeInstructeur'])->name('instructeurs.store');
    // Gestion des licences du centre
    Route::get('/licences', [CentreController::class, 'licences'])->name('licences');
    Route::post('/licences/store', [CentreController::class, 'storeLicence'])->name('licences.store');
    Route::get('/licences/{id}/edit', [CentreController::class, 'editLicence'])->name('licences.edit');
    Route::put('/licences/{id}', [CentreController::class, 'updateLicence'])->name('licences.update');
    Route::delete('/licences/{id}', [CentreController::class, 'destroyLicence'])->name('licences.destroy');
    // Gestion des examinateurs
    Route::get('/examinateurs', [CentreController::class, 'examinateurs'])->name('examinateurs');
    Route::post('/examinateurs/store', [CentreController::class, 'storeExaminateur'])->name('examinateurs.store');
    
    // Gestion des dispositifs
    Route::get('/dispositifs', [CentreController::class, 'dispositifs'])->name('dispositifs');
    Route::post('/dispositifs/store', [CentreController::class, 'storeDispositif'])->name('dispositifs.store');
     // Routes pour la recherche de demandeurs
    Route::get('/search/demandeurs', [CentreController::class, 'searchDemandeurs'])->name('search.demandeurs');
    Route::get('/search/by-licence', [CentreController::class, 'searchByLicence'])->name('search.by.licence');
    Route::get('/demandeur/details', [CentreController::class, 'getDemandeurDetails'])->name('demandeur.details');
});

// Routes pour l'ANAC (validation des examinateurs)
Route::middleware(['auth:web', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/centre/examinateurs/pending', [AdminController::class, 'pendingExaminateurs'])->name('examinateurs.pending');
    Route::post('/centre/examinateurs/{id}/validate', [AdminController::class, 'validateExaminateur'])->name('examinateurs.validate');
    Route::post('/centre/examinateurs/{id}/reject', [AdminController::class, 'rejectExaminateur'])->name('examinateurs.reject');
    Route::get('/centre//examinateurs', [App\Http\Controllers\AdminController::class, 'allExaminateurs'])->name('examinateurs.index');
    Route::get('/centre//examinateurs/{id}', [App\Http\Controllers\AdminController::class, 'showExaminateur'])->name('examinateurs.show');
});
    }
);

// Rida