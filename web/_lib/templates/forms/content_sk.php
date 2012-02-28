<?
$form = $data['form'];
?>

<div class="one-column">

	<h2>Výpočet pojistného</h2>

	<form action="./" method="post">
		<dl>
			<dt>
				<label for="pohlavi_muz">Pohlaví</label>
			</dt>
				<dd>
					<input id="pohlavi_muz" type="radio" name="pohlavi" value="muz" <?= $form->value('pohlavi') == 'muz' ? 'checked="checked"' : ''?> />
					<label for="pohlavi_muz">Muž</label>
					<br />
					<input id="pohlavi_zena" type="radio" name="pohlavi" value="zena" <?= $form->value('pohlavi') == 'zena' ? 'checked="checked"' : ''?> />
					<label for="pohlavi_zena">Žena</label>

					<div class="error"><?= $form->error('pohlavi') ?></div>
				</dd>
			<dt>
				<label for="castka">Celkové jednorázové pojistné</label>
			</dt>
				<dd>
					<span><input id="castka" type="text" name="castka" value="<?= htmlentities_utf8($form->value('castka')) ?>" size="20" maxlength="7" /></span>
					<div class="error"><?= $form->error('castka') ?></div>
				</dd>
		</dl>
		<div>
			<input type="submit" name="submit" value="Vypočítat" />
		</div>
	</form>

</div>
