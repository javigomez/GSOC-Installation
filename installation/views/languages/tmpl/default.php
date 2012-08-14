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
<div id="step">
	<div class="far-right">
	<?php if ($this->document->direction == 'ltr') : ?>
		<div class="button1-right">
			<div class="prev">
				<a href="index.php?view=complete" onclick="return Install.goToPage('complete');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a>
			</div>
		</div>

		<?php if($this->items) : ?>
		<div class="button1-left">
			<div class="next">
				<a href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a>
			</div>
		</div>
		<?php endif; ?>
	<?php elseif ($this->document->direction == 'rtl') : ?>
		<div class="button1-right">
			<div class="prev">
				<a href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a>
			</div>
		</div>
		<?php if($this->items) : ?>
		<div class="button1-left">
			<div class="next">
				<a href="index.php?view=complete" onclick="return Install.goToPage('complete');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a>
			</div>
		</div>
		<?php endif; ?>
	<?php endif; ?>
	</div>
	<h2><?php echo JText::_('INSTL_LANGUAGES'); ?></h2>
</div>
<form action="index.php" method="post" id="adminForm" class="form-validate">
	<div id="installer">
		<div class="m">
			<h3><?php echo JText::_('INSTL_LANGUAGES_HEADER'); ?></h3>
			<div class="install-text">
				<?php echo JText::_('INSTL_LANGUAGES_DESC'); ?>
			</div>
			<div class="install-body">
				<div class="m">
					<?php if(!$this->items) : ?>
						<p style="text-align: center;" class="error"><?php echo JText::_('INSTL_LANGUAGES_WARNING_NO_INTERNET') ?></p>
						<p style="text-align: center;">
							<input class="button" type="button" name="instDefault" value="<?php echo JText::_('INSTL_LANGUAGES_WARNING_BACK_BUTTON'); ?>" onclick="return Install.goToPage('complete');"/>
						</p>
					<?php else : ?>
					<h4 class="title-smenu">
						<?php echo JText::_('INSTL_LANGUAGES_TABLE_HEADER'); ?>
					</h4>
					<div class="section-smenu">
						<table class="content2">
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
									if(substr($language->version, 0, 3) == $ver->RELEASE) : ?>
									<tr>
										<td>
											<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $language->update_id; ?>" />
											<label for="<?php echo "cb$i" ?>"><?php echo $language->name; ?></label>
										</td>
										<td>
											<?php echo $language->version; ?>
										</td>
									</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							</TBODY>
						</table>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<input type="hidden" name="task" value="setup.installLanguages" />
	<?php echo JHtml::_('form.token'); ?>
</form>
