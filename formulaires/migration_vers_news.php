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

/**
 * Chargement du formulaire d'édition de news
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_news
 *     Identifiant du news. 'new' pour un nouveau news.
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_migration_vers_news_charger_dist(){
	$valeurs = array();
	
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
 * @return array
 *     Tableau des erreurs
 */
function formulaires_migration_vers_news_verifier_dist(){
	$erreurs = array();

	$type = _request('type');

	if (!$type)
		$erreurs['type'] = 'veuillez sélectionner un objet';

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de news
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_type_traiter()
 *
 * @param int|string $id_news
 *     Identifiant du news. 'new' pour un nouveau news.
 * @return array
 *     Retours des traitements
 */
function formulaires_migration_vers_news_traiter_dist(){

	include_spip('inc/api_news');

	$type = _request('type');

	$message['message_ok'] = news_migrer_objet_vers_dist($type);

	return $message;
}