<?php
/**
 * Utilisations de pipelines par News
 *
 * @plugin     News
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\News\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter les objets sur les vues de rubriques
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function news_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
		AND $e['type'] == 'rubrique'
		AND $e['edition'] == false) {

		$id_rubrique = $flux['args']['id_rubrique'];
		$lister_objets = charger_fonction('lister_objets', 'inc');

		$bouton = '';
		if (autoriser('creernewsdans', 'rubrique', $id_rubrique)) {
			$bouton .= icone_verticale(_T("news:icone_creer_news"), generer_url_ecrire("news_edit", "id_rubrique=$id_rubrique"), "news-24.png", "new", "right")
					. "<br class='nettoyeur' />";
		}

		$flux['data'] .= $lister_objets('news', array('titre'=>_T('news:titre_news_rubrique') , 'id_rubrique'=>$id_rubrique, 'par'=>'date'));
		$flux['data'] .= $bouton;

	}
	return $flux;
}


/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function news_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/news', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('news:info_news_auteur')
		), array('ajax' => true));

	}
	return $flux;
}


/**
 * Compter les enfants d'une rubrique
 *
 * @pipeline objets_compte_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function news_objet_compte_enfants($flux) {
	if ($flux['args']['objet']=='rubrique' AND $id_rubrique=intval($flux['args']['id_objet'])) {
		// juste les publiés ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['news'] = sql_countsel('spip_news', "id_rubrique = ".intval($id_rubrique)." AND (statut = 'publie')");
		} else {
			$flux['data']['news'] = sql_countsel('spip_news', "id_rubrique = ".intval($id_rubrique)." AND (statut <> 'poubelle')");
		} 
	}
	return $flux;
}


/**
 * Optimiser la base de données 
 * 
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function news_optimiser_base_disparus($flux){

	sql_delete("spip_news", "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

/**
 * Synchroniser la valeur de id secteur
 *
 * @pipeline trig_propager_les_secteurs
 * @param  string $flux Données du pipeline
 * @return string       Données du pipeline
**/
function news_trig_propager_les_secteurs($flux) {

	// synchroniser spip_news
	$r = sql_select("A.id_news AS id, R.id_secteur AS secteur", "spip_news AS A, spip_rubriques AS R",
		"A.id_rubrique = R.id_rubrique AND A.id_secteur <> R.id_secteur");
	while ($row = sql_fetch($r)) {
		sql_update("spip_news", array("id_secteur" => $row['secteur']), "id_news=" . $row['id']);
	}

	return $flux;
}

/**
 * Ajouter un nouveau contexte pour le plugin Rang
 *
 * @pipeline compositions_declarer_heritage
 * @param  string $heritages Données du pipeline
 * @return string       Données du pipeline
**/
function news_rang_declarer_contexte($contextes) {
	$contextes['news'] = 'configurer_news';
	return $contextes;
}

/**
 * Compatibilité avec le plugin Composition
 *
 * @pipeline compositions_declarer_heritage
 * @param  string $heritages Données du pipeline
 * @return string       Données du pipeline
**/
function news_compositions_declarer_heritage($heritages) {
	$heritages['news'] = 'rubrique';
	return $heritages;
}