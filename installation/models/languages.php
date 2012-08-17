<?php
/**
 * @package    Joomla.Installation
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @since	2.5.x
 */

defined('_JEXEC') or die;


/**
 * Language Installer model for the Joomla Core Installer.
 *
 * @package  Joomla.Installation
 */
class JInstallationModelLanguages extends JModelLegacy
{

	/**
	 * @var object client object
	 */
	protected $client = null;

	/**
	 * @var array languages description
	 */
	protected $data = null;

	/**
	 * @var string language path
	 */
	protected $path = null;

	/**
	 * @var int total number pf languages installed
	 */
	protected $langlist = null;

	/**
 	 * Constructor, deletes the default installation config file and recreates it with the good config file.
	 */
	public function __construct()
	{
		JFactory::$config = null;
		JFactory::getConfig(JPATH_SITE . '/configuration.php');
		parent::__construct();
	}

	/**
	 * Generate a list of language choices to install in the Joomla CMS
	 *
	 * @return	boolean True if successful
	 */
	public function getItems()
	{
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
	 */
	public function install($lids)
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		//	JSession::checkToken('request') or $this->sendResponse(new Exception(JText::_('JINVALID_TOKEN'), 403));

		$app			= JFactory::getApplication();
		$installer		= JInstaller::getInstance();

		// Loop through every selected language
		foreach ($lids as $id)
		{
			// Loads the update database object that represents the language
			$language = JTable::getInstance('update');
			$language->load($id);

			// Get the url to the XML manifest file of the selected language
			$remote_manifest 	= $this->_getLanguageManifest($id);
			if (!$remote_manifest)
			{
				// Could not find the url, the information in the update server may be corrupt
				$message 	= JText::sprintf('INSTL_DEFAULTLANGUAGE_COULD_NOT_INSTALL_LANGUAGE', $language->name);
				$message 	.= ' ' . JText::_('INSTL_DEFAULTLANGUAGE_TRY_LATER');
				$app->enqueueMessage($message);
				continue;
			}

			// Based on the language XML manifest get the url of the package to download
			$package_url 		= $this->_getPackageUrl($remote_manifest);
			if (!$package_url)
			{
				// Could not find the url , maybe the url is wrong in the update server, or there is not internet access
				$message 	= JText::sprintf('INSTL_DEFAULTLANGUAGE_COULD_NOT_INSTALL_LANGUAGE', $language->name);
				$message 	.= ' ' . JText::_('INSTL_DEFAULTLANGUAGE_TRY_LATER');
				$app->enqueueMessage($message);
				continue;
			}

			// Download the package to the tmp folder
			$package 			= $this->_downloadPackage($package_url);

			// Install the package
			if (!$installer->install($package['dir']))
			{
				// There was an error installing the package
				$message 	= JText::sprintf('INSTL_DEFAULTLANGUAGE_COULD_NOT_INSTALL_LANGUAGE', $language->name);
				$message 	.= ' ' . JText::_('INSTL_DEFAULTLANGUAGE_TRY_LATER');
				$app->enqueueMessage($message);
				continue;
			}

			// Package installed successfully
			//$app->enqueueMessage(JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', $language->name));

			// Cleanup the install files in tmp folder
			if (!is_file($package['packagefile']))
			{
				$config = JFactory::getConfig();
				$package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
			}
			JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

			// Delete the installed language from the list
			$language->delete($id);
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
	protected function _getPackageUrl($remote_manifest)
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
	 * @param   string  $url  url of the package
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

	/**
	 * Method to get Languages item data
	 *
	 * @return	array
	 */
	public function getInstalledlangs()
	{
		$langlist   = $this->_getLanguageList();
		$path		= $this->_getPath();

		// Compute all the languages
		$data	= array ();

		foreach($langlist as $lang)
		{
			$file = $path . '/' . $lang . '/' . $lang.'.xml';
			$info = JApplicationHelper::parseXMLLangMetaFile($file);
			$row = new stdClass;
			$row->language = $lang;

			if (!is_array($info)) {
				continue;
			}

			foreach($info as $key => $value)
			{
				$row->$key = $value;
			}

			$row->checked_out = 0;
			$data[] = $row;
		}

		usort($data, array($this, '_compareLanguages'));

		return $data;
	}

	/**
	 * Method to get installed languages data.
	 *
	 * @return	string	An SQL query
	 */
	protected function _getLanguageList()
	{
		// Create a new db object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		// Select field element from the extensions table.
		$query->select('a.element, a.name');
		$query->from('#__extensions AS a');

		$query->where('a.type = ' . $db->quote('language'));
		$query->where('state = 0');
		$query->where('enabled = 1');
		$query->where('client_id = 1');

		$db->setQuery($query);

		$this->langlist = $db->loadColumn();

		return $this->langlist;
	}

	/**
	 * Method to compare two languages in order to sort them
	 *
	 * @param	object	$lang1 the first language
	 * @param	object	$lang2 the second language
	 *
	 * @return	integer
	 */
	protected function _compareLanguages($lang1, $lang2)
	{
		return strcmp($lang1->name, $lang2->name);
	}


	/**
	 * Method to get the path
	 *
	 * @return	string	The path to the languages folders
	 */
	protected function _getPath()
	{
		if (is_null($this->path)) {
			$client = $this->_getClient();
			$this->path = JLanguage::getLanguagePath($client->path);
		}

		return $this->path;
	}

	/**
	 * Method to get the client object of Administrator
	 *
	 * @return	object
	 */
	protected function _getClient()
	{
		if (is_null($this->client)) {
			$this->client = JApplicationHelper::getClientInfo(1);
		}

		return $this->client;
	}

	/**
	 * Method to set the default language.
	 *
	 * @param int $language_id
	 *
	 * @return	bool
	 */
	public function setDefault($language_id = null)
	{
		if ($language_id)
		{
			return false;
		}

		$client	= $this->_getClient();

		$params = JComponentHelper::getParams('com_languages');
		$params->set($client->name, $language_id);

		$table = JTable::getInstance('extension');
		$id = $table->find(array('element' => 'com_languages'));

		// Load
		if (!$table->load($id)) {
			$this->setError($table->getError());
			return false;
		}

		$table->params = (string)$params;
		// pre-save checks
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// save the changes
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		return true;

	}

	/**
	 * Get the current setup options from the session.
	 *
	 * @return	array
	 */
	public function getOptions()
	{
		$session = JFactory::getSession();
		$options = $session->get('setup.options', array());

		return $options;
	}
}
