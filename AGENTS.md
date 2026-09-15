# AGENTS.md

## Règles Générales

- **Langue** : Réponds TOUJOURS en français, quelle que soit la langue de la demande.
- **Ne jamais toucher au code sans confirmation explicite** de l'utilisateur. Toujours attendre la validation du plan avant toute modification.
- Si tu as besoin d'informations supplémentaires, demande-les et continue tant que l'utilisateur n'a pas confirmé.

---

## Lors d'un bug (demande de fix)

1. **Analyser le bug** : identifier la cause racine, le fichier concerné, la ligne exacte.
2. **Expliquer le bug** de façon simple et claire.
3. **Expliquer comment éviter** ce type de bug à l'avenir.
4. **Proposer un plan d'implémentation** détaillé avec la liste de tous les fichiers à ajouter, modifier ou supprimer.
5. **Attendre la confirmation** avant de toucher au code.

---

## Lors d'une nouvelle fonctionnalité

1. **Analyser la demande** : résumer toutes les informations pertinentes.
2. **Proposer un plan d'implémentation** très détaillé avec la liste de tous les fichiers à ajouter, modifier ou supprimer.
3. **Attendre la confirmation** avant de toucher au code.

---

## Pour toute autre demande

1. **Analyser** en profondeur et donner un résultat détaillé.
2. **Proposer un plan d'implémentation** si des modifications sont nécessaires.
3. **Attendre la confirmation** avant de toucher au code.

---

## Génération de code

- Appliquer les principes **DRY** et **SOLID**.
- Suivre les **meilleures pratiques Laravel**.
- Lire la documentation Laravel pertinente.
- Analyser et appliquer le **design pattern** le plus adapté au cas.
- Si du code est répété (apparaît 2 fois ou plus), proposer un **plan de refactoring**.
- Agir comme un **développeur Senior Laravel expert**.

---

## Code Review

- Après la génération du code, effectuer un **code review** complet.
- Laisser l'utilisateur confirmer le review.
- Appliquer les changements demandés si nécessaire.

---

## Convention de commit

- Format : **Conventional Commits** → https://www.conventionalcommits.org/en/v1.0.0/
- Icônes : **Gitmoji** → https://gitmoji.dev/
- Auteur : toujours **l'utilisateur**.
- Ne **jamais** ajouter `Co-Authored-By` avec un agent ou une IA.
