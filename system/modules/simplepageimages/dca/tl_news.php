<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package    SimplePageImages
 * @copyright  DevPoint | Wilfried Reiter 2013
 * @author     DevPoint | Wilfried Reiter <wilfried.reiter@devpoint.at>
 * @link       http://contao.org
 * @license    MIT
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_news']['palettes']['__selector__'][] = 'simplepageimages_enable';
$GLOBALS['TL_DCA']['tl_news']['palettes']['default'] = str_replace
(
	'{enclosure_legend:hide}',
	'{simplepageimages_legend},simplepageimages_enable;{enclosure_legend:hide}',
	$GLOBALS['TL_DCA']['tl_news']['palettes']['default']
);


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_news']['subpalettes']['simplepageimages_enable'] = 'simplepageimages_images';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_news']['fields']['simplepageimages_enable'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_news']['simplepageimages_enable'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'filter'		=> true,
	'eval'			=> array
	(
		'tl_class'		=> 'w50',
		'submitOnChange'	=> true
	),
	'sql'			=> "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_news']['fields']['simplepageimages_images'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_news']['simplepageimages_images'],
	'exclude'		=> true,
	'inputType'		=> 'fileTree',
	'search'		=> true,
	'eval'			=> array
	(
		'tl_class'		=> 'long clr',
		'files'			=> true,
		'filesOnly'		=> false,
		'fieldType'		=> 'checkbox',
		'orderField'	=> 'simplepageimages_order',
		'multiple'		=> true,
		'extensions'	=> $GLOBALS['TL_CONFIG']['validImageTypes']
	),
	'sql'			=> "blob NOT NULL"
);

$GLOBALS['TL_DCA']['tl_news']['fields']['simplepageimages_order'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_news']['simplepageimages_order'],
	'sql'			=> "text NULL"
);
