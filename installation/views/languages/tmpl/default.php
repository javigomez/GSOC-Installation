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
					<?php if(!$this->items) : ?>
						<p style="text-align: center;" class="error"><?php echo JText::_('Joomla! was not able to connect to the languages server. Please finish the installation process. <br/>Note: You will be able to install languages later using the Joomla! administrator') ?></p>
						<p style="text-align: center;">
							<input class="button" type="button" name="instDefault" value="<?php echo JText::_('Return to last installation step'); ?>" onclick="return Install.goToPage('complete');"/>
						</p>
					<?php else : ?>
					<h4 class="title-smenu" title="<?php echo JText::_('Basic'); ?>">
						Choose languages
					</h4>
					<div class="section-smenu">
						<table class="content2">
							<TBODY>
								<?php foreach($this->items as $i => $lang) : ?>
								<tr>
									<td>
										<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $lang->update_id; ?>" />
										<label for="<?php echo "cb$i" ?>"><?php echo $lang->name; ?></label>
									</td>
								</tr>
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
