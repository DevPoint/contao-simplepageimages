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
 * Add a palette to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['background_image_spis'] = '{title_legend},name,headline,type;{template_legend},simplepageimages_source,simplepageimages_recursive;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['simplepageimages_source'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['simplepageimages_source'],
	'default'			=> 'page',
	'exclude'			=> true,
	'inputType'			=> 'select',
	'options'			=> array('page', 'news', 'event'),
	'reference'			=> &$GLOBALS['TL_LANG']['tl_module']['simplepageimages_source_options'],
	'eval'				=> array('tl_class'=>'w50'),
	'sql'				=> "varchar(64) NOT NULL default 'page'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['simplepageimages_recursive'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['simplepageimages_recursive'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> array('tl_class'=>'w50 m12'),
	'sql'				=> "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['simplepageimages_layout'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['simplepageimages_layout'],
	'default'			=> 'simplepageimages_default',
	'exclude'			=> true,
	'inputType'			=> 'select',
	'options_callback'	=> array('tl_module_simplepageimages', 'getSimplePageImagesTemplates'),
	'eval'				=> array('tl_class'=>'w50'),
	'sql'				=> "varchar(64) NOT NULL default ''"
);

class tl_module_simplepageimages extends Backend {
	/**
	 * Return SimplePageImages templates as array
	 * @param object
	 * @return array
	 */
	public function getSimplePageImagesTemplates(DataContainer $dc)
	{
		$intPid = $dc->activeRecord->pid;

		if (Input::get('act') == 'overrideAll')
		{
			$intPid = Input::get('id');
		}
			
		return $this->getTemplateGroup('layout_', $intPid);
	}
}