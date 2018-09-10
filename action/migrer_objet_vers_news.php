<?php
/**
 * Action utiles au plugin News
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




/**
 * Outil de migration d'un objet (brèves, articles, etc) vers l'objet News
 *
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger.
**/
function action_migrer_objet_vers_news_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg= explode("-", $arg);
	$type = $arg[0];

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

		// étape 3 : migration des logos

		
	}

	return true;
}