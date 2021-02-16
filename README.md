# Plugin News pour SPIP
Objet éditorial pour la gestion d'actualités

## Intérêt de ce plugin
Il reprend plusieurs bonnes idées ici là :

*C'est d'abord un objet éditorial dédié*
- Il vient donc avec des boutons "Créer une actus", etc.
- Grâce au plugin Stat objets, les actus ont leur propres stats;

*mais aussi*
- Par opposition aux brèves, il n'est pas limités aux rubriques secteurs;
- Comme pour les articles, il est possible de configurer les champs dont vous avez besoins pour votre projet.

## ConfigurationS
Il est possible de configurer deux choses :

### Actus à la Une
Dans l'edition d'une actu, pouvoir la taguer 'À la Une'.
On peut ensuite utiliser le critère 'ala_une' dans les boucles NEWS.

Par exemple
```
<BOUCLE_actus_a_la_une(NEWS){!par date}{ala_une=oui}>
```
…retournera tous les actus à la Une.

### Contenus des Actus
Comme pour les articles, le formuliare d'édition d'une actu propose deux champs : Titre et Texte.
Il est possible d'activer également les champs suivants :
- soustitre
- chapeau
- lien hypertexte

## Compatibilité avec les plugins SPIP suivants :
- LIM et sa gestion des objets par rubrique;
- Rang : il est possible d'activer le drag&drop sur les actus;
- Zcore : les squelettes publics proposés sont fait pour être utilisés avec zcore;
- Compositions

## TODO's
Gestion de la migration  "Brèves -> News" en reprennant le code de https://contrib.spip.net/Conversion-des-breves-en-articles
