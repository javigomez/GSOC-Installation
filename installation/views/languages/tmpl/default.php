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
		<div class="button1-right">
			<div class="prev">
				<a href="index.php?view=complete" onclick="return Install.goToPage('complete');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a>
			</div>
		</div>
		<div class="button1-left">
			<div class="next">
				<a href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a>
			</div>
		</div>
	<?php elseif ($this->document->direction == 'rtl') : ?>
		<div class="button1-right">
			<div class="prev">
				<a href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a>
			</div>
		</div>
		<div class="button1-left">
			<div class="next">
				<a href="index.php?view=complete" onclick="return Install.goToPage('complete');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<h2>Install languages</h2>
</div>
<form action="index.php" method="post" id="adminForm" class="form-validate">
	<div id="installer">
		<div class="m">
			<h3>Joomla in multiple languages</h3>
			<div class="install-text">
				Joomla allows you to create multilingual sites if you want. At the same time your users can choose the language of their session... lorem ipsum
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
							<?php foreach($this->items as $lang) : ?>
							<tr>
								<td>
									<input type="checkbox" id="cb1" name="cid[]" value="<?php echo $lang->update_id; ?>" />
								</td>
								<td>
									<?php echo $lang->name; ?>
								</td>
							</tr>
							<?php endforeach; ?>
							</TBODY>
						</table>
					</div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<input type="hidden" name="task" value="setup.installLanguages" />
	<?php echo JHtml::_('form.token'); ?>
</form>