# ANAC — Comparaison : parcours idéal vs implémentation réelle

**Demandes d'autorisation de vol — écarts entre `parcours-ideal-demande-autorisation.md` et le code**
Document établi le 01/09/2026 par relecture du code (`app/Http/Controllers`, `app/Models`, `resources/views`).

## 1. Verdict global

L'ossature générale correspond bien au parcours idéal : soumission simultanée DG/DTA/SRTA, options du DG (confirmer / rejeter / annoter DTA / annoter SRTA), validation finale du DG, facturation et confirmation DAF pour l'atterrissage. En revanche, **la SRTA (« service » dans le document idéal) n'a dans le code qu'une fraction des pouvoirs qui lui sont attribués sur le papier**, et le mécanisme de retour après rejet/correction contient plusieurs incohérences (dont deux viennent d'être corrigées ce jour, voir §4). C'est cette différence entre le rôle décrit et le rôle réellement implémenté pour la SRTA qui explique la plupart des anomalies remontées.

## 2. Tableau comparatif par acteur

| Acteur | Boutons prévus (parcours idéal) | Boutons réellement implémentés | Fichier |
|---|---|---|---|
| **DG** | Confirmer directement · Rejeter (motif) · Annoter → DTA · Annoter → SRTA · Validation finale | Identique : bouton « Annoter » ouvre un choix DTA / Admin-SRTA (`dg_annoter` / `dg_annoter_admin`), bouton Valider direct (conditionné à avions+vols renseignés — condition non prévue dans le parcours idéal), bouton Rejeter, validation finale (`dg_valider`/`dta_dg_valider`) | [role-actions.blade.php:8-79](resources/views/dir/demandeAutorisations/partials/role-actions.blade.php#L8-L79), [dg-annotation.blade.php](resources/views/dir/demandeAutorisations/modals/dg-annotation.blade.php) |
| **DTA** | Auto-annoter · Vérifier · Valider · Transférer · Récupérer | **Conforme** : Auto-annoter (`dta_dg_annoter`, bouton « Annoter DG »), annoter vers SRTA (`dta_annoter`), valider service (`service_valider`/`service_tout_valider`), Transférer vers directions (`service_annoter`), Récupérer/retirer une direction (`service_raturer`, bouton « Retrait ») — **+ un bouton « Rejeter » et un bouton « Notifier » (`dta_notifier`) non mentionnés dans le parcours idéal** | [role-actions.blade.php:81-199](resources/views/dir/demandeAutorisations/partials/role-actions.blade.php#L81-L199), [annotation-button.blade.php](resources/views/dir/demandeAutorisations/partials/annotation-button.blade.php) |
| **SRTA (rôle `admin` dans le code)** | Auto-annoter · Valider · Transférer · Récupérer | **Écart majeur** : aucun de ces 4 boutons n'existe pour la SRTA. Elle dispose uniquement d'une validation **pièce par pièce** (avion/vol/document/…) sur une page séparée (`/admin/demandeAutorisations/show`), sans Auto-annoter, sans Transférer vers les directions, sans Récupérer. La partial `role-actions.blade.php`, utilisée par DG/DTA/directions, **n'a même pas de branche pour le rôle `admin`** | [role-actions.blade.php](resources/views/dir/demandeAutorisations/partials/role-actions.blade.php) (pas de `@elseif($role == 'admin')`), [admin/demandeAutorisations/show.blade.php](resources/views/admin/demandeAutorisations/show.blade.php) |
| **Autres services (DSV/DSNA/DSAD/DSF)** | Recevoir par transfert · Donner avis · Rejeter un champ (retour au demandeur puis retour au service) | Conforme dans l'esprit : Valider (`{role}_valider`) et un bouton libellé « Achever » qui, avec un motif, joue le rôle du rejet de champ (`dsv_motif`/`dsna_motif`/`dsad_motif`) — le nom du bouton (« Achever ») prête à confusion, sa fonction correspond au « Rejeter un champ » du parcours idéal | [role-actions.blade.php:201-227](resources/views/dir/demandeAutorisations/partials/role-actions.blade.php#L201-L227), [DgDsvController::achiever](app/Http/Controllers/DgDsvController.php#L1394) |
| **Compagnie (demandeur)** | Déposer · Corriger les champs refusés · Redéposer · Payer | Conforme, avec une nuance : le redépôt (« Envoyer ») ne se débloque que si `documentCount > 0`, et **c'est ce même clic « Envoyer » qui, seul, lève le verrou empêchant la SRTA de re-valider/rejeter** (voir §3.2) | [edit.blade.php:1688-1715](resources/views/user/autorisations/edit.blade.php#L1688-L1715) |
| **DAF (paiement)** | Facture → paiement → confirmation DAF → autorisation | Conforme : `compagnie_payer` puis `daf_confirme_pay` génèrent l'autorisation avec le code `SUR-0001-26` exactement comme décrit | [DemandeAutorisationController.php:1054-1075](app/Http/Controllers/DemandeAutorisationController.php#L1054) |

## 3. Écarts détaillés

### 3.1 La SRTA n'a pas de pouvoir de « service » autonome (écart le plus important)

Le parcours idéal traite la SRTA comme un pair de la DTA : mêmes capacités (Auto-annoter, Valider, Transférer vers les directions, Récupérer). Dans le code, **c'est la DTA seule** qui porte ces quatre capacités ; la SRTA n'intervient qu'en aval, sur une page distincte, pour valider ou rejeter les pièces du dossier une par une, une fois que la DTA (ou le DG directement) l'a déjà « annotée ».

Concrètement :
- Pas de bouton Auto-annoter pour la SRTA si le DG est occupé et n'a pas encore statué — seule la DTA peut se substituer au DG.
- Pas de bouton Transférer/Récupérer pour la SRTA vis-à-vis des directions (DSV/DSNA/DSAD/DSF) — c'est la DTA qui gère `service_annoter`/`service_raturer`.
- Pas de rejet global du dossier avec motif pour la SRTA (`DgDsvController::rejeter`, route `dir.rejeter`, ne gère que `hasRole('dta')` et `hasRole('dg')`) — juste corrigé ce jour pour au moins renvoyer une erreur explicite plutôt que d'échouer silencieusement.

C'est cet écart d'architecture qui a produit les anomalies signalées précédemment (bouton Rejeter de la SRTA qui se bloque après correction, incohérences lors de son intervention).

### 3.2 Le retour « à celui qui a rejeté » est fragile

Le parcours idéal (§7) attend un cycle simple : rejet → retour compagnie → correction → retour automatique à qui a rejeté. Dans le code, ce cycle repose sur un unique point de passage — le clic « Envoyer » de la compagnie (`compagnie_cree_demande`), qui seul relance `resetAllValidations()`/`resetAllMotifs()` et restaure l'étape où le dossier s'était arrêté. Trois défauts corrigés aujourd'hui menaçaient ce cycle :
- `updateDocument`/`updateMdn`/`updateDeceasedPerson` ne levaient pas le verrou de pièce corrigée (motif jamais effacé) → SRTA bloquée en permanence sur un document corrigé.
- `DgDsvController::rejeter` écrasait son propre flag `dta_rejeter`/`dg_rejeter` juste après l'avoir positionné → le dossier ne restait jamais marqué « rejeté » dans l'état du workflow.
- Les jalons d'annotation (`dg_annoter`, `dta_dg_annoter`, …) étaient effacés sans être restaurés → le bouton « Annoter DG » de la DTA pouvait réapparaître après correction alors qu'il ne doit se faire qu'une fois.

**Un point du même type reste ouvert**, non corrigé aujourd'hui : `case 'dta_notifier'` ([DemandeAutorisationController.php:958-968](app/Http/Controllers/DemandeAutorisationController.php#L958-L968)) — le bouton « Notifier » que la DTA utilise pour renvoyer le dossier au demandeur après un rejet de champ par une direction — réinitialise lui aussi tous les états sans restaurer les jalons d'annotation. Je peux l'aligner sur le même correctif si vous le souhaitez.

### 3.3 Un maillon manuel absent du parcours idéal

Le parcours idéal décrit le rejet de champ comme automatique : la compagnie voit directement quoi corriger. Dans le code, pour un rejet de champ par une direction (DSV/DSNA/DSAD), il faut en plus que la **DTA clique explicitement sur « Notifier »** (`dta_notifier`) pour que le dossier reparte réellement vers la compagnie — sans ce clic, `hasRejectionReasons()` est vrai mais rien ne se passe côté compagnie. Ce maillon manuel n'existe pas dans le parcours idéal et peut faire perdre du temps si la DTA oublie de le déclencher.

### 3.4 Précondition non documentée sur la validation directe du DG

Le bouton « Confirmer directement » du DG (parcours idéal, option 1) n'est affiché dans le code que si `$demande->vols->isNotEmpty() && $demande->avions->isNotEmpty()`. C'est une règle métier raisonnable (pas d'autorisation sans avion/vol renseigné) mais elle n'est pas mentionnée dans le document idéal — à valider que c'est bien voulu pour tous les types de demande (ex. transport de dépouille mortelle, qui n'a pas nécessairement d'avion/vol classique).

## 4. Ce qui correspond bien

- Soumission simultanée DG + DTA + SRTA — conforme ([DemandeAutorisationController.php:757-767](app/Http/Controllers/DemandeAutorisationController.php#L757-L767)).
- Les 4 options du DG (Confirmer / Rejeter / Annoter DTA / Annoter SRTA) — conforme.
- Circuit DTA → directions (DSV/DSNA/DSAD/DSF) → retour DTA → DG — conforme, `isValidatedByAll()`/`isFullyValidated()` bloquent bien la validation finale tant que tout n'est pas validé.
- Validation finale du DG et génération du code d'autorisation (`SUR-0001-26`) — conforme.
- Cas paiement atterrissage (facture → paiement → confirmation DAF → autorisation) — conforme, y compris le calcul du montant par avion.
- Rejet de champ par une direction avec retour compagnie puis retour au service — conforme dans son principe (via `achiever` + `dta_notifier`), même si le nommage et le déclenchement manuel diffèrent du parcours idéal.

## 5. Recommandations, par priorité

1. **Décider si la SRTA doit vraiment avoir Auto-annoter/Transférer/Récupérer** comme le décrit le parcours idéal, ou si le document doit être mis à jour pour refléter le fonctionnement réel (SRTA = validation de pièces uniquement, sous la responsabilité de la DTA). C'est la décision produit qui conditionne tout le reste — corriger le code sans trancher ce point reviendrait à deviner une intention non confirmée.
2. Aligner `dta_notifier` sur le correctif de préservation des jalons appliqué aujourd'hui aux autres points de rejet (§3.2).
3. Clarifier/renommer le bouton « Achever » des directions pour refléter qu'il sert aussi à rejeter un champ avec motif.
4. Documenter (ou retirer) la précondition avions+vols sur la validation directe du DG.
