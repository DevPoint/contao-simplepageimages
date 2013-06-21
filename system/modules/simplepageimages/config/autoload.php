<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package    SimplePageImages
 * @copyright  DevPoint | Wilfried Reiter 2013
 * @author     DevPoint | Wilfried Reiter <wilfried.reiter@devpoint.at>
 * @link       http://contao.org
 * @license    MIT
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'SimplePageImages' 		=> 'system/modules/simplepageimages/classes/SimplePageImages.php',
	'ModuleSimplePageImagesSingle' 	=> 'system/modules/simplepageimages/modules/ModuleSimplePageImagesSingle.php',
	'ModuleSimplePageImagesFullCss'	=> 'system/modules/simplepageimages/modules/ModuleSimplePageImagesFullCss.php'
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_simplepageimages_single'	=> 'system/modules/simplepageimages/templates',
	'mod_simplepageimages_fullcss'	=> 'system/modules/simplepageimages/templates',
	'spis_single_default'			=> 'system/modules/simplepageimages/templates'
));
