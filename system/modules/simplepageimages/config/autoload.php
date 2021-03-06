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
	'SimplePageImages' 		=> 'system/modules/simplepageimages/classes/SimplePageImages.php',
	'ModuleImageSPIS' 	=> 'system/modules/simplepageimages/modules/ModuleImageSPIS.php',
	'ModuleBGImageSPIS' 	=> 'system/modules/simplepageimages/modules/ModuleBGImageSPIS.php'
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_bgimage_spis'			=> 'system/modules/simplepageimages/templates',
	'mod_image_spis'			=> 'system/modules/simplepageimages/templates'
));
