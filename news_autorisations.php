<?php
/**
 * Définit les autorisations du plugin News
 *
 * @plugin     News
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\News\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function news_autoriser(){}


// -----------------
// Objet news


/**
 * Autorisation de voir un élément de menu (news)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_news_configurer_dist($faire, $type, $id, $qui, $opt){
	if ($qui['webmestre'] == 'oui') {
		return true;
	}
	return false;
} 


/**
 * Autorisation de voir un élément de menu (news)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_news_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
} 


/**
 * Autorisation de voir le bouton d'accès rapide de création (news)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_newscreer_menu_dist($faire, $type, $id, $qui, $opt){
	return autoriser('creer', 'news', '', $qui, $opt);
} 

/**
 * Autorisation de créer (news)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_news_creer_dist($faire, $type, $id, $qui, $opt) {
	return (in_array($qui['statut'], array('0minirezo', '1comite')) AND sql_countsel('spip_rubriques')>0); 
}

/**
 * Autorisation de voir (news)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_news_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (news)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_news_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de supprimer (news)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_news_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de créer l'élément (news) dans une rubrique
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_rubrique_creernewsdans_dist($faire, $type, $id, $qui, $opt) {
	return ($id AND autoriser('voir','rubrique', $id) AND autoriser('creer','news', $id));
}

/**
 * Autorisation de voir les catégories
**/
function autoriser_categorie_voir_dist($faire, $type, $id, $qui, $opt) {
	
	// le Webmestre à toujours le droit de voir les catégories
	if ($qui['webmestre'] == 'oui') 
		return true;

	$quels_status = lire_config('news/autorisation_categorie');

	if ($quels_status == '0minirezo') {
		return ($qui['statut'] == '0minirezo' AND !$qui['restreint']);
	}
	if ($quels_status == 'restreint') {
		return ( $qui['statut'] == '0minirezo' OR ( $qui['statut'] == '0minirezo' AND is_array($qui['restreint'])) );
	}

	return false;
}

/* Compatibilité avec le plugin LIM */
if (!function_exists('autoriser_rubrique_creernewsdans')) {
	function autoriser_rubrique_creernewsdans($faire, $type, $id, $qui, $opt) {
		$quelles_rubriques = lire_config('lim_rubriques/news');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		
		return
			$lim_rub
			AND autoriser_rubrique_creernewsdans_dist($faire, $type, $id, $qui, $opt);
	}
}
