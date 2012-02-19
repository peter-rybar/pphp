<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

		<meta name="author" content="<?= $SITE['author'] ?>, <?= $SITE['author_mail'] ?>"/>
		<meta name="copyright" content="<?= $SITE['author'] ?>, <?= $SITE['author_mail'] ?>"/>
		<meta name="version" content="<?= $SITE['version'] ?>" />
<?= $head ?>
		<meta name="robots" content="index,follow" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<link rel="stylesheet" title="Screen" media="screen" type="text/css" href="<?= $SITE['root_url'] ?>/css/screen.css"/>
		<link rel="stylesheet" media="screen and (min-width: 700px)" href="<?= $SITE['root_url'] ?>/css/screen_wide.css" />

		<link rel="home" href="<?= $SITE['root_url'] ?>/" title="<?= $SITE['author'] ?>" />
	</head>
	<body>

		<h1 id="header">
			<a href="<?= $SITE['root_url'] ?>/">
				pPHP - pico PHP framework
			</a>
		</h1>

		<p id="langs" align="right">
			<a href="?lang=">en</a> |
			<strong>sk</strong>
		</p>
		<ul id="menu">
			<li class="current_page_">
				<a href="<?= $SITE['root_url'] ?>">pPHP</a>
			</li>
			<li>
				<a href="<?= $SITE['root_url'] ?>/forms">Formuláre</a>
			</li>
			<li>
				<a href="<?= $SITE['root_url'] ?>/news">Novinky</a>
			</li>
		</ul>

		<div id="content">

			<?= $content ?>

			<div class="clear"></div>
		</div>

		<p id="footer" align="right">
			<small>
				Designe by
				<a href="<?= $SITE['author_web'] ?>"><?= $SITE['author'] ?></a>
				&lt;<a href="mailto:<?= $SITE['author_mail'] ?>"><?= $SITE['author_mail'] ?></a>&gt;
			</small>
		</p>
	</body>
</html>
