AGENCE NATIONALE DE L'AVIATION CIVILE (ANAC)
Direction du Transport Aérien

Objet : Autorisation de vol {{ $autorisation->code_autorisation }}

Monsieur le Directeur,

Nous avons l'honneur de vous adresser, pour exécution, l'autorisation de vol
approuvée relative à la demande susvisée. Nous vous demandons de bien vouloir
prendre toutes les dispositions nécessaires à son application, conformément aux
textes en vigueur.

Détails de l'autorisation :
- Numéro d'autorisation : {{ $autorisation->code_autorisation }}
- Type d'autorisation : {{ $autorisation->demande->type->libelle }}
- Date d'émission : {{ now()->format('d/m/Y') }}
- Période de validité : du {{ \Carbon\Carbon::parse($autorisation->demande->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($autorisation->demande->date_fin)->format('d/m/Y') }}
@if($autorisation->demande->sous_validite)
- Sous-validité : {{ $autorisation->demande->sous_validite }} heures
@endif

Consulter l'autorisation complète :
{{ route('public.autorisations.download', $autorisation) }}

Toute anomalie, incident ou non-conformité constaté(e) devra être signalé(e)
sans délai à nos services.

Pour toute question relative à l'exécution de cette autorisation, veuillez
prendre attache avec nos services : survol.dta@anac.mr - Tél : 00 222 45 24 40 05

Nous vous prions d'agréer l'expression de notre considération distinguée.

--
AGENCE NATIONALE DE L'AVIATION CIVILE (ANAC)
Direction du Transport Aérien
Nouakchott, Mauritanie - www.anac.mr
Ce message est généré automatiquement, merci de ne pas y répondre directement.
