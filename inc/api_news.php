<?php
/**
 * Migration des brèves vers les actualités
 *
 * @plugin     News
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\News\inc
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// les étapes
// 1- migrer les donnes des tables spip_breves -> spip_news
// 2- gestions des documents
// 3- gestions des mots-cles
// 4- gestion des logos
// 5- gestion des urls
// 6- gestion des fils RSS

/**
 * Outil de migration d'un objet (brèves, articles, etc) vers l'objet News
 *
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger.
**/
function news_migrer_objet_vers_dist($type) {

	include_spip('base/objets.php');

	$table = table_objet_sql($type);
	$infos = lister_tables_objets_sql($table);
	$cle_primaire = $infos['key']['PRIMARY KEY'];
	$champs = array_keys($infos['field']);

	// Bug brèves : id_trad n'est pas dans la base

	if (!isset($champs['id_trad']))
		$champs[] = 'id_trad';
	

	// champs_extra
	$champs[] = 'type_actu';


	// étape 1 : migration des champs de la table
	$res = sql_select($champs, $table, '', '', "$cle_primaire ASC");

	while ($r = sql_fetch($res)) {
		$id_objet = $r[$cle_primaire];

		$exp = array( 'id_news' => $id_objet,
					 'id_rubrique' => 164,
					 'id_secteur' => 6,
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

		$id_news = sql_insertq('spip_news', $exp);

		// éviter d'écraser
		if ($id_news === false) {
			$message = 'migration avortée !';
			return $message;
		}
		

		// étape 2 : correspondance mot-clé
		// $extra_type_actu = $r['type_actu'];

		// if ($extra_type_actu == '1')
		// 	$id_mot = 2;

		// if ($extra_type_actu == '2')
		// 	$id_mot = 3;

		// 	sql_insertq('spip_mots_liens', array('id_mot' => $id_mot,'id_objet' => $id_news, 'objet' => 'news'));
		
	}
	
	// étape 4 : migration des logos
	// $liste_logos = glob(_DIR_LOGOS."breve*.*");
	// foreach ($liste_logos as $logo) {
	// 	$new_logo = preg_replace('/breve/i', 'news', $logo);
	// 	copy($logo, $new_logo);
	// }

	// étape X : migration des documents : une seule passe suffit
	//sql_updateq('spip_documents_liens', array('objet' => 'news'), 'objet='.sql_quote($type));

	$message = 'migration réussie !';
	return $message;
}

