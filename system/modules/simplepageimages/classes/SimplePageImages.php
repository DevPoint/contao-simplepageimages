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

abstract class SimplePageImages extends \Module {

	/**
	 * Returns image data
	 *
	 * Adopted from Contao/core/elements/ContentGallery.php
	 *
	 * @param array  $objFile
	 * @param string|id  $objFileId
	 * @param string  $language
	 * @return array
	 */
	private function getImageFromFile($objFile, $objFileId, $language)
	{
		// Only use image type files
		$file = new \File($objFile->path);
		if (!$file->isGdImage || ($file->extension == 'swf'))
		{
			return false;
		}
		
		// retrieve Meta date in current language
		$arrMeta = $this->getMetaData($objFile->meta, $language);

		// Use the file name as title if none is given
		if ($arrMeta['title'] == '')
		{
			$arrMeta['title'] = specialchars($objFile->basename);
		}

		// return the image
		return array(
			'id'        => $objFileId,
			'uuid'      => $objFile->uuid,
			'name'      => $file->basename,
			'singleSRC' => $objFile->path,
			'alt'     	=> $arrMeta['title'],
			'imageUrl'  => $arrMeta['link'],
			'caption'   => $arrMeta['caption']);
	}

	/**
	 * Returns image data array
	 *
	 * Adopted from Contao/core/elements/ContentGallery.php
	 *
	 * @param $multiSrc as retrieved from database
	 * @return array
	 */
	protected function getImages($multiSrc, $orderSrc, $language)
	{
		// deserialize Image IDs
		$multiSrc = deserialize($multiSrc);
		if (!is_array($multiSrc) || empty($multiSrc))
		{
			return null;
		}

		// Get the file entries from the database
		$objFiles = \FilesModel::findMultipleByUuids($multiSrc);
		if ($objFiles === null)
		{
			return null;
		}
	
		// Get all images
		$images = array();
		while ($objFiles->next())
		{
			// Continue if the files has been processed or does not exist
			if (isset($images[$objFiles->path]) || !file_exists(TL_ROOT . '/' . $objFiles->path))
			{
				continue;
			}

			// Single files
			if ($objFiles->type == 'file')
			{
				$arrImage = $this->getImageFromFile($objFiles, $objFiles->id, $language);
				if ($arrImage)
				{
					$images[$objFiles->path] = $arrImage;	
				}
			}
			// Folders
			else
			{
				$objSubFiles = \FilesModel::findByPid($objFiles->id);
				if ($objSubFiles !== null)
				{
					while ($objSubFiles->next())
					{
						// Single files only, skip folders
						if ($objSubFiles->type != 'folder')
						{
							$arrImage = $this->getImageFromFile($objSubFiles, $objFiles->id, $language);
							if ($arrImage)
							{
								$images[$objSubFiles->path] = $arrImage;	
							}
						}
					}
				}
			}
		}

		// Sort Images in order given by <$multiSrc>
		if (!empty($orderSrc))
		{
			$tmp = deserialize($orderSrc);

			if (!empty($tmp) && is_array($tmp))
			{
				// Remove all values
				$arrOrder = array_map(function(){}, array_flip($tmp));

				// Move the matching elements to their position in $arrOrder
				foreach ($images as $k=>$v)
				{
					if (array_key_exists($v['uuid'], $arrOrder))
					{
						$arrOrder[$v['uuid']] = $v;
						unset($images[$k]);
					}
				}

				// Append the left-over images at the end
				if (!empty($images))
				{
					$arrOrder = array_merge($arrOrder, array_values($images));
				}

				// Remove empty (unreplaced) entries
				$images = array_values(array_filter($arrOrder));
				unset($arrOrder);
			}
		}

		// Any images found?
		if (empty($images))
		{
			return null;
		}
		return $images;
	}

	/**
	 * check if a protected archive is visible
	 * @param $archiveGroups (serialized)
	 * @param $user FrontendUser
	 * @return array
	 *
	 * Remark: This function is the essence of the
	 * <Contao\Events::sortOutProtected> and
	 * <Contao\ModuleNews::sortOutProtected> functions
	 */
	static public function checkProtectedArchiveVisible($archiveGroups, $user)
	{
		if (BE_USER_LOGGED_IN)
		{
			return true;
		}

		if (!FE_USER_LOGGED_IN)
		{
			return false;
		}

		$groups = deserialize($archiveGroups);

		if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $user->groups)))
		{
			return false;
		}
		
		return true;
	}


	/**
	 * Sort out protected archives
	 * @param array
	 * @return array
	 *
	 * Remark: This function is a one2one copy of the
	 * <Contao\Events::sortOutProtected> function
	 */
	protected function sortOutProtectedCalendars($arrCalendars)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrCalendars) || empty($arrCalendars))
		{
			return $arrCalendars;
		}

		$this->import('FrontendUser', 'User');
		$objCalendar = \CalendarModel::findMultipleByUuids($arrCalendars);
		$arrCalendars = array();

		if ($objCalendar !== null)
		{
			while ($objCalendar->next())
			{
				if (!$objCalendar->protected || $this->checkProtectedArchiveVisible($objCalendar->groups, $this->User))
				{
					$arrCalendars[] = $objCalendar->id;
				}
			}
		}

		return $arrCalendars;
	}


	/**
	 * Find page images within News item
	 * by finding an active NewsReader module
	 *
	 * @param PageModel
	 * @return array
	 *	 
	 */
	protected function findPageCalendarEventImages($objPage)
	{
		// Find Modules for current Page
		$layoutField = $objPage->isMobile ? 'mobileLayout' : 'layout';
		$objLayout = $objPage->getRelated($layoutField);
		$arrModules = deserialize($objLayout->modules);

		// Create list fo News Reader archives
		$arrCalendars = array();
		$db = Database::getInstance();
		$arrModuleIds = array_map(function($arr) { return $arr['mod']; }, $arrModules);
		$objModules = $db->execute("SELECT * FROM tl_module WHERE id IN(" . implode(',', array_map('intval', $arrModuleIds)) . ") AND type='eventreader'");
		if ($objModules !== null)
		{
			while ($objModules->next())
			{
				$arrCalendars = array_merge($arrCalendars, deserialize($objModules->cal_calendar));
			}
		}
		$arrCalendars = array_unique($arrCalendars);
		$arrCalendars = $this->sortOutProtectedCalendars($arrCalendars);

		// Get Event Id/Alias Parameter from input
		$eventAliasId = \Input::get('events');
		if (!isset($eventAliasId) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			$eventAliasId = \Input::get('auto_item');
		}
		if (!$eventAliasId)
		{
			return null;
		}

		// Create list of images
		$arrImages = null;
		$objEvent = \CalendarEventsModel::findPublishedByParentAndIdOrAlias($eventAliasId, $arrCalendars);
		if (null !== $objEvent && isset($objEvent->simplepageimages_enable))
		{
			if ($objEvent->simplepageimages_enable)
			{
				$arrImages = $this->getImages($objEvent->simplepageimages_images, $objEvent->simplepageimages_order, $objPage->language);
			}
		}
		return $arrImages;
	}

	/**
	 * Sort out protected archives
	 * @param array
	 * @return array
	 *	 
	 * Remark: This function is a one2one copy of the
	 * <Contao\ModuleNews::sortOutProtected> function
	 */
	protected function sortOutProtectedNewsArchives($arrArchives)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrArchives) || empty($arrArchives))
		{
			return $arrArchives;
		}

		$this->import('FrontendUser', 'User');
		$objArchive = \NewsArchiveModel::findMultipleByUuids($arrArchives);
		$arrArchives = array();

		if ($objArchive !== null)
		{
			while ($objArchive->next())
			{
				if (!$objArchive->protected || $this->checkProtectedArchiveVisible($objArchive->groups, $this->User))
				{
					$arrArchives[] = $objArchive->id;
				}
			}
		}

		return $arrArchives;
	}

	/**
	 * Find page images within News item
	 * by finding an active NewsReader module
	 *
	 * @param PageModel
	 * @return array
	 *	 
	 */
	protected function findPageNewsItemImages($objPage)
	{
		// Find Modules for current Page
		$layoutField = $objPage->isMobile ? 'mobileLayout' : 'layout';
		$objLayout = $objPage->getRelated($layoutField);
		$arrModules = deserialize($objLayout->modules);

		// Create list fo News Reader archives
		$arrArchives = array();
		$db = Database::getInstance();
		$arrModuleIds = array_map(function($arr) { return $arr['mod']; }, $arrModules);
		$objModules = $db->execute("SELECT * FROM tl_module WHERE id IN(" . implode(',', array_map('intval', $arrModuleIds)) . ") AND type='newsreader'");
		if ($objModules !== null)
		{
			while ($objModules->next())
			{
				$arrArchives = array_merge($arrArchives, deserialize($objModules->news_archives));
			}
		}
		$arrArchives = array_unique($arrArchives);
		$arrArchives = $this->sortOutProtectedNewsArchives($arrArchives);

		// Get Item Id/Alias Parameter from input
		$newsItemAliasId = \Input::get('items');
		if (!isset($newsItemAliasId) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			$newsItemAliasId = \Input::get('auto_item');
		}
		if (!$newsItemAliasId)
		{
			return null;
		}

		// Create list of images
		$arrImages = null;
		$objNewsItem = \NewsModel::findPublishedByParentAndIdOrAlias($newsItemAliasId, $arrArchives);
		if (null !== $objNewsItem && isset($objNewsItem->simplepageimages_enable))
		{
			if ($objNewsItem->simplepageimages_enable)
			{
				$arrImages = $this->getImages($objNewsItem->simplepageimages_images, $objNewsItem->simplepageimages_order, $objPage->language);
			}
		}
		return $arrImages;
	}

	protected function findPageImages($recursively)
	{
		$arrImages = null;
		global $objPage;

		// retrieve Images from given source
		switch ($this->simplepageimages_source)
		{
			case 'news':
			{
				$arrImages = $this->findPageNewsItemImages($objPage);
				break;
			}
			case 'events':
			{
				$arrImages = $this->findPageCalendarEventImages($objPage);
				break;
			}
		}

		// retrieve Page Images for current page
		if (null === $arrImages || empty($arrImages))
		{
			if ($objPage->simplepageimages_enable)
			{
				$arrImages = $this->getImages($objPage->simplepageimages_images, $objPage->simplepageimages_order, $objPage->language);
			}
		}

		// retrieve Page Images from parent pages
		if ((null === $arrImages || empty($arrImages)) && $recursively)
		{
			$objParentPage = PageModel::findParentsById($objPage->id);
			if ($objParentPage !== null)
			{
				while ($objParentPage->next())
				{
					if ($objParentPage->simplepageimages_enable)
					{
						$arrImages = $this->getImages($objParentPage->simplepageimages_images, $objParentPage->simplepageimages_order, $objPage->language);
						if (null !== $arrImages && !empty($arrImages)) break;
					}
				}
			}
		}

		return $arrImages;
	}
}
