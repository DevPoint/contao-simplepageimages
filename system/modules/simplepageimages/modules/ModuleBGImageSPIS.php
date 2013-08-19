<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @package    SimplePageImages
 * @copyright  DevPoint | Wilfried Reiter 2013
 * @author     DevPoint | Wilfried Reiter <wilfried.reiter@devpoint.at>
 * @license    MIT
 */

class ModuleBGImageSPIS extends SimplePageImages {

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_bgimage_spis';

    /**
     * Generate module
     */
    protected function compile() 
    {
        $arrImages = $this->findPageImages(false);
        if (null !== $arrImages && !empty($arrImages))
        {
			$arrImages[0]['size'] = $this->imgSize;
			$imgSize = getimagesize(TL_ROOT .'/'. $arrImages[0]['singleSRC']);
			$this->addImageToTemplate($this->Template, $arrImages[0], $imgSize[0]);
        }
        else
        {
            $this->Template->src = false;
        }
    }
}
