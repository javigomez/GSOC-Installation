<?php
/**
 * @package    Joomla.Installation
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div id="step">
	<div class="far-right">
	<?php if ($this->document->direction == 'ltr') : ?>
		<div class="button1-right"><div class="prev"><a href="index.php?view=languages" onclick="return Install.goToPage('languages');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a></div></div>
		<div class="button1-left"><div class="next"><a href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a></div></div>
	<?php elseif ($this->document->direction == 'rtl') : ?>
		<div class="button1-right"><div class="prev"><a href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a></div></div>
		<div class="button1-left"><div class="next"><a href="index.php?view=languages" onclick="return Install.goToPage('languages');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a></div></div>
		<?php endif; ?>
	</div>
	<h2><?php echo JText::_('Choose the default language'); ?></h2>
</div>
<form action="index.php" method="post" id="adminForm" class="form-validate">
	<div id="installer">
		<div class="m">
			<h3><?php echo JText::_('Language for the administrator'); ?></h3>
			<div class="install-text">
				<?php echo JText::_('Joomla was able to install the listed langauges, if you want you can choose the admin language... lorem ipsum'); ?>
			</div>
			<div class="install-body">
				<div class="m">
					<h4 class="title-smenu" title="<?php echo JText::_('Basic'); ?>">
						Choose languages
					</h4>
					<div class="section-smenu">
						<table class="content2">
							<THEAD>
								<tr>
									<th width="10">
										Install
									</th>
									<th>
										Language
									</th>
								</tr>
							</THEAD>
							<TBODY>
							<tr>
								<td>
									<input type="radio" name="id" value="English" checked="true" />
								</td>
								<td>
									English
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="id" value="Spanish" />
								</td>
								<td>
									Spanish
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="id" value="Spanish" />
								</td>
								<td>
									Others
								</td>
							<tr>
								<td>
									<input type="radio" name="id" value="Spanish" />
								</td>
								<td>
									...
								</td>
							</tr>
							</tr>
							</TBODY>
						</table>
					</div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<input type="hidden" name="task" value="setup.setDefaultLanguage" />
	<?php echo JHtml::_('form.token'); ?>
</form>