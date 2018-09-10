<?php
/**
 * Gestion du formulaire de d'édition de news
 *
 * @plugin     News
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\News\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_news
 *     Identifiant du news. 'new' pour un nouveau news.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un news source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du news, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_news_identifier_dist($id_news='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_news)));
}

/**
 * Chargement du formulaire d'édition de news
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_news
 *     Identifiant du news. 'new' pour un nouveau news.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un news source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du news, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_news_charger_dist($id_news='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('news', $id_news, $id_rubrique, $lier_trad, $retour, $config_fonc, $row, $hidden);

	// Charger les catégories
	if ($categories_actives = lire_config('news/categories') == 'actif') {
		$id_groupe = sql_getfetsel('id_groupe', 'spip_groupes_mots', 'titre ='.sql_quote('Actualités'));
		$r = sql_select('id_mot, titre', 'spip_mots', array('id_groupe' => $id_groupe));
		while ($t = sql_fetch($r)){
			$key = $t['id_mot'];
			$categorie[$key] = $t['titre'];
		}
		$valeurs['categorie'] = $categorie;
	}
	
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de news
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_news
 *     Identifiant du news. 'new' pour un nouveau news.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un news source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du news, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_news_verifier_dist($id_news='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('news', $id_news, array('titre'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de news
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_news
 *     Identifiant du news. 'new' pour un nouveau news.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un news source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du news, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_news_traiter_dist($id_news='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	
	$retour = formulaires_editer_objet_traiter('news', $id_news, $id_rubrique, $lier_trad, $retour, $config_fonc, $row, $hidden);
	
	$id_news = $retour['id_news'];

	// gestion des mots clés de la catégorie
	include_spip('action/editer_mot');
	$categories = _request('categorie');

	// gérer le cas où une checkbox est décochée : violent, mais pas trouvé mieux
	sql_delete('spip_mots_liens', "id_objet=".sql_quote($id_news)." AND objet='news'");
	
	if (is_array($categories) AND count($categories) > 0) {
		foreach ($categories as $value) {
			mot_associer($value, array('news'=>$id_news));
		}
	}
	return $retour;
}