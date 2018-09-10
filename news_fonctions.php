<?php
/**
 * Fonctions utiles au plugin News
 *
 * @plugin     News
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\News\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/mots');


/**
 * Vérifier que le mot-clé est associé
 *
 * @param int $id_mot
 * 
 * @param int $id_objet
 * 
 * @return bool
 *     true si le mot clé est associé
**/
function news_mot_select($id_mot, $id_objet){
	$id_mot = intval($id_mot);
	$id_objet = intval($id_objet);
	$res = sql_countsel('spip_mots_liens', array("id_mot=$id_mot", "id_objet=$id_objet", "objet='news'"));
	if ($res > 0 ) return true;
	else return false;
}


/**
 * Outil de migration des brèves vers News
 *
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger.
**/
function news_migrer_objets($type) {

	include_spip('base/objets.php');
	$table = table_objet_sql($type);
	$infos = lister_tables_objets_sql($table);
	$cle_primaire = $infos['key']['PRIMARY KEY'];
	$champs = array_keys($infos['field']);

	// champs_extra
	$champs[] = 'type_actu';

	// étape 1 : migration des champs de la table
	$res = sql_select($champs, $table, '', '', "$cle_primaire ASC");

	while ($r = sql_fetch($res)) {
		
		$exp = array('id_rubrique' => $r['id_rubrique'],
					 'id_secteur' => $r['id_rubrique'],
					 'titre' => $r['titre'],
					 'texte' => $r['texte'],
					 'lien_titre' => $r['lien_titre'],
					 'lien_url' => $r['lien_url'],
					 'date' => $r['date_heure'],
					 'statut' => $r['statut'],
					 'lang' => $r['lang'],
					 'langue_choisie' => $r['langue_choisie'],
					 'id_trad' => $r['id_trad']
					 );
		//debug($r);

		$new_id = sql_insertq('spip_news', $exp);

		// étape 2 : migration des documents

		sql_updateq('spip_news', array('id_objet' => $new_id, 'objet' => 'news'), "id_objet = $r[$cle_primaire] AND objet = $type");

		
	}

	return true;
}