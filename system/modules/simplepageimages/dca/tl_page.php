<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
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
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'simplepageimages_enable';
$GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] = str_replace
(
	'{cache_legend:hide}',
	'{simplepageimages_legend:hide},simplepageimages_enable;{cache_legend:hide}',
	$GLOBALS['TL_DCA']['tl_page']['palettes']['regular']
);
$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = str_replace
(
	'{cache_legend:hide}',
	'{simplepageimages_legend:hide},simplepageimages_enable;{cache_legend:hide}',
	$GLOBALS['TL_DCA']['tl_page']['palettes']['root']
);
$GLOBALS['TL_DCA']['tl_page']['palettes']['error_403'] = str_replace
(
	'{cache_legend:hide}',
	'{simplepageimages_legend:hide},simplepageimages_enable;{cache_legend:hide}',
	$GLOBALS['TL_DCA']['tl_page']['palettes']['error_403']
);
$GLOBALS['TL_DCA']['tl_page']['palettes']['error_404'] = str_replace
(
	'{cache_legend:hide}',
	'{simplepageimages_legend:hide},simplepageimages_enable;{cache_legend:hide}',
	$GLOBALS['TL_DCA']['tl_page']['palettes']['error_404']
);


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['simplepageimages_enable'] = 'simplepageimages_images';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['simplepageimages_enable'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['simplepageimages_enable'],
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

$GLOBALS['TL_DCA']['tl_page']['fields']['simplepageimages_images'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['simplepageimages_images'],
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

$GLOBALS['TL_DCA']['tl_page']['fields']['simplepageimages_order'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['simplepageimages_order'],
	'sql'			=> "text NULL"
);

