<?php
/**
 * @package    Joomla.Installation
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Get version of Joomla! to compare it with the version of the language package
$ver = new JVersion;
?>
<?php echo JHtml::_('installation.stepbarlanguages'); ?>
<form action="index.php" method="post" id="adminForm" class="form-validate form-horizontal">
	<div class="btn-toolbar">
		<div class="btn-group pull-right">
			<a class="btn" href="#" onclick="return Install.goToPage('complete');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><i class="icon-arrow-left"></i> <?php echo JText::_('JPrevious'); ?></a>
			<?php
			// Check if ther is any languages to list, if not you cannot move forward
			if($this->items) :
			?>
				<a  class="btn btn-primary" href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><i class="icon-arrow-right icon-white"></i> <?php echo JText::_('JNext'); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<h3><?php echo JText::_('INSTL_LANGUAGES'); ?></h3>
	<hr class="hr-condensed" />
	<?php if(!$this->items) : ?>
		<p><?php echo JText::_('INSTL_LANGUAGES_WARNING_NO_INTERNET') ?></p>
		<p><a href="#" class="btn btn-primary" onclick="return Install.goToPage('complete');"><i class="icon-arrow-left icon-white"></i> <?php echo JText::_('INSTL_LANGUAGES_WARNING_BACK_BUTTON'); ?></a></p>
		<p><?php echo JText::_('INSTL_LANGUAGES_WARNING_NO_INTERNET2') ?></p>
	<?php else : ?>
		<p><?php echo JText::_('INSTL_LANGUAGES_DESC'); ?></p>
		<table class="table table-striped table-condensed">
			<THEAD>
					<tr>
						<th>
							<?php echo JText::_('INSTL_LANGUAGES_COLUMN_HEADER_LANGUAGE'); ?>
						</th>
						<th>
							<?php echo JText::_('INSTL_LANGUAGES_COLUMN_HEADER_VERSION'); ?>
						</th>
					</tr>
			</THEAD>
			<TBODY>
				<?php foreach($this->items as $i => $language) : ?>
					<?php
					// Checks that the language package is valid for current Joomla version
					if(substr($language->version, 0, 3) == $ver->RELEASE) :
					?>
					<tr>
						<td>
							<label class="checkbox">
								<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $language->update_id; ?>" /> <?php echo $language->name; ?>
							</label>
						</td>
						<td>
							<span class="badge"><?php echo $language->version; ?></span>
						</td>
					</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</TBODY>
		</table>
		<input type="hidden" name="task" value="languages.installLanguages" />
		<?php echo JHtml::_('form.token'); ?>
	<?php endif; ?>
</form>
