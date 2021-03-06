<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin News
 *
 * @plugin     News
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\News\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin News.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function news_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array( array('maj_tables', array('spip_news')), 
							array('news_init_metas'));

	$maj['1.0.1'] = array(
		array('sql_alter',"TABLE `spip_news` ADD `chapo` text NOT NULL DEFAULT '' AFTER `soustitre`")
	);

	$maj['1.0.2'] = array(
		array('sql_alter',"TABLE `spip_news` ADD `ala_une` VARCHAR(3) DEFAULT 'non' AFTER `soustitre`")
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Déclare la configuration de News pas défaut
**/
function news_init_metas() {
	ecrire_config("news/texte", 'oui');
}

/**
 * Fonction de désinstallation du plugin News.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function news_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_news");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('news')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('news')));
	sql_delete("spip_forum",                 sql_in("objet", array('news')));

	effacer_meta('news');
	effacer_meta($nom_meta_base_version);
}