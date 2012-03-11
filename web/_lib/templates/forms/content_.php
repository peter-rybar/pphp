<?
$form = $data['form'];
?>

<div class="one-column">

	<h2>Form</h2>

	<form action="./" method="post">
		<dl>
			<dt>
				<label for="text">Text</label>
			</dt>
				<dd>
					<span><input id="text" type="text" name="text" value="<?= htmlentities_utf8($form->value('text')) ?>" size="20" maxlength="70" /></span>
					<div class="error"><?= $form->error('text') ?></div>
				</dd>
			<dt>
				<label for="number">Number</label>
			</dt>
				<dd>
					<span><input id="number" type="text" name="number" value="<?= htmlentities_utf8($form->value('number')) ?>" size="20" maxlength="70" /></span>
					<div class="error"><?= $form->error('number') ?></div>
				</dd>
			<dt>
				<label for="date">Date</label>
			</dt>
				<dd>
					<span><input id="date" type="text" name="date" value="<?= htmlentities_utf8($form->value('date')) ?>" size="20" maxlength="70" /></span>
					<div class="error"><?= $form->error('date') ?></div>
				</dd>
			<dt>
				<label for="checkbox">Checkbox</label>
			</dt>
				<dd>
					<span><input id="checkbox" type="checkbox" name="checkbox" <?= $form->value('checkbox') ? 'checked="checked"' : '' ?>/></span>
					<div class="error"><?= $form->error('checkbox') ?></div>
				</dd>
			<dt>
				<label for="select">Select</label>
			</dt>
				<dd>
					<input id="select" type="radio" name="select" value="one" <?= $form->value('select') == 'one' ? 'checked="checked"' : ''?>/>
					<label for="select">One</label>
					<br />
					<input id="select-two" type="radio" name="select" value="two" <?= $form->value('select') == 'two' ? 'checked="checked"' : ''?>/>
					<label for="select-two">Two</label>

					<div class="error"><?= $form->error('select') ?></div>
				</dd>
			<dt>
				<label for="array">Array[Text]</label>
			</dt>
				<dd>
					<span><input id="array-text" type="text" name="array[text]" value="<?= htmlentities_utf8($form->value('array', 'text')) ?>" size="20" maxlength="70" /></span>
					<div class="error"><?= $form->error('array', 'text') ?></div>
				</dd>
			<dt>
				<label for="array">Array[Number]</label>
			</dt>
				<dd>
					<span><input id="array-number" type="text" name="array[number]" value="<?= htmlentities_utf8($form->value('array', 'number')) ?>" size="20" maxlength="70" /></span>
					<div class="error"><?= $form->error('array', 'number') ?></div>
				</dd>
		</dl>
		<div>
			<input type="submit" name="submit" value="Submit" />
		</div>
	</form>

	<h2>Data</h2>

	<pre><?= print_r($form->values())?></pre>

</div>

