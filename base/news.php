<?php
/**
 * Déclarations relatives à la base de données
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
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function news_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['news'] = 'news';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function news_declarer_tables_objets_sql($tables) {

	$tables['spip_news'] = array(
		'type' => 'news',
		'principale' => "oui",
		'field'=> array(
			'id_news'            => 'bigint(21) NOT NULL',
			'id_rubrique'        => 'bigint(21) NOT NULL DEFAULT 0', 
			'id_secteur'         => 'bigint(21) NOT NULL DEFAULT 0', 
			'titre'              => 'text NOT NULL DEFAULT ""',
			'soustitre'          => 'text NOT NULL DEFAULT ""',
			'texte'              => 'text NOT NULL DEFAULT ""',
			'lien_titre'         => 'text NOT NULL DEFAULT ""',
			'lien_url'           => 'text NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"', 
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL', 
			'lang'               => 'VARCHAR(10) NOT NULL DEFAULT ""',
			'langue_choisie'     => 'VARCHAR(3) DEFAULT "non"', 
			'id_trad'            => 'bigint(21) NOT NULL DEFAULT 0', 
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_news',
			'KEY id_rubrique'    => 'id_rubrique', 
			'KEY id_secteur'     => 'id_secteur', 
			'KEY lang'           => 'lang', 
			'KEY id_trad'        => 'id_trad', 
			'KEY statut'         => 'statut', 
		),
		'titre' => 'titre AS titre, lang AS lang',
		'date' => 'date',
		'champs_editables'  => array('titre', 'soustitre', 'texte', 'lien_titre', 'lien_url'),
		'champs_versionnes' => array('texte'),
		'rechercher_champs' => array("titre" => 10),
		'tables_jointures'  => array('id_news' => 'documents_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'news:texte_changer_statut_news', 
		

	);
	$tables[]['tables_jointures'][]= 'documents_liens';
	$tables[]['tables_jointures'][]= 'documents';

	return $tables;
}