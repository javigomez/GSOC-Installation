<?php
/**
 * @package    Joomla.Installation
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Language Installer model for the Joomla Core Installer.
 *
 * @package  Joomla.Installation
 * @since    3.0
 */
class InstallationModelLanguages extends JModelLegacy
{
	public function __construct()
	{
		// Deletes the default installation config file and recreates it with the good config file
		JFactory::$config = null;
		JFactory::getConfig(JPATH_SITE . '/configuration.php');
		parent::__construct();
	}

	/**
	 * Generate a list of language choices to install in the Joomla CMS
	 *
	 * @return	boolean True if successful
	 *
	 * @since	3.0
	 */
	public function getItems()
	{
		// Initialise variables.
//		$app = JFactory::getApplication();

		/*
		  // Detect the native language.
		  $native = JLanguageHelper::detectLanguage();

		  if (empty($native))
		  {
			  $native = 'en-GB';
		  }
		  // Get a forced language if it exists.
		  $forced = $app->getLocalise();

		  if (!empty($forced['language']))
		  {
			  $native = $forced['language'];
		  }

		// Get the setup options
		$options = (object) $this->getOptions();
		// Get a database object.
		try
		{
			$db = InstallationHelperDatabase::getDBO(
				$options->db_type,
				$options->db_host,
				$options->db_user,
				$options->db_pass,
				$options->db_name,
				$options->db_prefix);
		}
		catch (RuntimeException $e)
		{
			$this->setError(JText::sprintf('INSTL_ERROR_CONNECT_DB', $e->getMessage()));
		}

		// Set's the database to JFactory that is used by the Updater and all the Adapters
		JFactory::$database = $db;
  */

		$updater = JUpdater::getInstance();

		/*
		 * The following function uses extension_id 600, that is the english language extension id.
		 * In #__update_sites_extensions you should have 600 linked to the Accredited Translations Repo
		 */
		$updater->findUpdates(array(600), 0);

		$db		= JFactory::getDbo();
		$query  = $db->getQuery(true);

		// Select the required fields from the updates table
		$query->select('update_id, name')
			->from('#__updates')
			->order('name');

		$db->setQuery($query);
		$list = $db->loadObjectList();

		if (!$list || $list instanceof Exception)
		{
			$list = array();
		}

		return $list;
	}

	/**
	 * Method that install selected languages in the Languages View ov the installer
	 *
	 * @param $lids array list of the update_id value of the languages to install
	 *
	 * @return  void
	 *
	 * @since	3.0
	 */
	public function install($lids)
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		//	JSession::checkToken('request') or $this->sendResponse(new Exception(JText::_('JINVALID_TOKEN'), 403));

		// Get the posted config options.

		$app			= JFactory::getApplication();
		$installer		= JInstaller::getInstance();

		// Loop through every selected language
		foreach ($lids as $id)
		{

		// Get the url to the XML manifest file of the selected language
		$remote_manifest 	= $this->_getLanguageManifest($id);
		if (!$remote_manifest)
		{
		// Could not find the url, the information in the update server may be corrupt
		$app->enqueueMessage(JText::_('COM_INSTALLER_MSG_LANGUAGES_CANT_FIND_REMOTE_MANIFEST') . ': ' . $id);
		continue;
		}

		// Based on the language XML manifest get the url of the package to download
		$package_url 		= $this->_getPackageUrl($remote_manifest);
		if (!$package_url)
		{
		// Could not find the url , maybe the url is wrong in the update server, or there is not internet access
		$app->enqueueMessage(JText::_('COM_INSTALLER_MSG_LANGUAGES_CANT_FIND_REMOTE_PACKAGE') . ': ' . $id);
		continue;
		}

		// Download the package to the tmp folder
		$package 			= $this->_downloadPackage($package_url);

		// Install the package
		if (!$installer->install($package['dir']))
		{
		// There was an error installing the package
		$app->enqueueMessage(JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($package['type']))));
		continue;
		}

		// Package installed successfully
		$app->enqueueMessage(JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($package['type']))));

		// Cleanup the install files in tmp folder
		if (!is_file($package['packagefile']))
		{
		$config = JFactory::getConfig();
		$package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
		}
		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		// Delete the installed language from the list
		$instance = JTable::getInstance('update');
		$instance->delete($id);
		}

	}

	/**
	 * Gets the manifest file of a selected language from a the language list in a update server.
	 *
	 * @param   int  $uid  the id of the language in the #__updates table
	 *
	 * @return string
	 */
	protected function _getLanguageManifest($uid)
	{
		$instance = JTable::getInstance('update');
		$instance->load($uid);

		return $instance->detailsurl;
	}

	/**
	 * Finds the url of the package to download.
	 *
	 * @param   string  $remote_manifest  url to the manifest XML file of the remote package
	 *
	 * @return string|bool
	 */
	protected function _getPackageUrl( $remote_manifest )
	{
		jimport('joomla.updater.update');

		$update = new JUpdate;
		$update->loadFromXML($remote_manifest);
		$package_url = $update->get('downloadurl', false)->_data;

		return $package_url;
	}

	/**
	 * Download a language package from a URL and unpack it in the tmp folder.
	 *
	 * @param   string  $url  hola
	 *
	 * @return array|bool Package details or false on failure
	 */
	protected function _downloadPackage($url)
	{

		// Download the package from the given URL
		$p_file = JInstallerHelper::downloadPackage($url);

		// Was the package downloaded?
		if (!$p_file)
		{
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
			return false;
		}

		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path');

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

		return $package;
	}
}
