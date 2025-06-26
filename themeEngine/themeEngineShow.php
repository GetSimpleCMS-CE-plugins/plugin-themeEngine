<style>
	html {
		scroll-behavior: smooth;
	}

	.bodycontent {
		grid-template-columns: 100% 0%;
	}

	#sidebar {
		display: none;
	}

	.former-te :is(input, textarea, select) {
		border-radius: 0 !important;
		padding: 10px !important;
	}
	
	te-wrapper{
		width:100%;
		margin:10px 0;
		display:flex;
		flex-direction:column;
	}
	
	.te-select{
		width:98%;
		padding:10px;
	}
	
	.te-date{
		width:98%;
		padding:10px;
	}
	
	.te-text{
		width:98%;
		padding:10px;
	}
	
	.te-textarea{
		width:98%;
		padding:10px;
		height:250px;
	}
	
	.formedit-file{
		display:grid;
		grid-template-columns:9fr 1fr;
		gap:10px;
	}

	.formedit-file button{
		background:darkorange;
		border:none;
		margin-top:30px; 
		padding:4px 6px
	}
	
	.formedit{
		display:grid;
		grid-template-columns:9fr 1fr;
		gap:10px;
	}

	.formedit button{
		background:darkorange;
		border:none;
		margin-top:30px; 
		padding:4px 6px
	}
	input.btn-save-te{
		background:#4CAF50!important;
		color:white;
		margin-top:20px;
	}

	.goto {
		display: inline-block;
		background: black;
		color: #fff !important;
		text-decoration: none !important;
		padding: 10px;

	}

	.lister {
		display: flex;
		gap: 10px;
		margin: 10px 0;
	}
</style>

<h3>😾 ThemeEngine Fields</h3>

<div class="lister">

	<?php



	$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');

	$result = $db->query('SELECT * FROM items ORDER BY item_order');
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
		echo '<a href="#' . $row['id'] . '" class="goto">' . $row['name'] . '</a>';
	}
	;
	$db->close();

	?>
</div>
<hr>

<form class="former-te" method="POST">
	<?php

		global $SITEURL;
		global $GSADMIN;
		global $USR;

		$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');

		$result = $db->query('SELECT * FROM items ORDER BY item_order');
		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

			echo '
		
			<div class="te-wrapper"  id="'.$row['id'].'" style="' . ($row['type'] !== 'separator' ? 'margin:15px 0; border:solid 1px #ddd; padding:10px; ' : '') . '">

			<h4 style="margin-bottom:20px"><b>' . ($row['type'] !== 'separator' ? $row['name'] : '') . '</b></h4>
			<input style="display:none" name="id[]" value="' . $row['id'] . '" > 
			<input  style="display:none" name="name[]"  value="' . $row['name'] . '"  > 
			<input style="display:none" name="type[]" value="' . $row['type'] . '">
			<input style="display:none" name="option_dropdown[]" >';

			if ($row['type'] == 'text' || $row['type'] == 'date') {
				echo '<input type="' . $row['type'] . '" class="te-text" name="value[]" value="' . $row['value'] . '">';
			}

			if ($row['type'] == 'color') {
				echo '<input type="' . $row['type'] . '" style="width:100%;padding:0 !important" name="value[]" value="' . $row['value'] . '">';
			}

			if ($row['type'] == 'textarea') {
				echo '<textarea class="te-textarea">' . $row['value'] . '</textarea>';
			}

			if ($row['type'] == 'ckeditor') {
				echo '<textarea id="post-content" name="value[]">' . html_entity_decode($row['value']) . '</textarea>';
			}

			if ($row['type'] == 'file') {
				echo '
				<div class="formedit-file">
					<input type="text" class="te_file file " name="value[]"  data-id="' . $row['id'] . '" value="' . $row['value'] . '" style="margin-top:30px">
					<button class="choose-file">
					<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 16 16"><path fill="#fff" fill-rule="evenodd" d="M11 13.5H5A1.5 1.5 0 0 1 3.5 12V4A1.5 1.5 0 0 1 5 2.5h2V5a3 3 0 0 0 3 3h2.5v4a1.5 1.5 0 0 1-1.5 1.5m1.303-7a1.5 1.5 0 0 0-.242-.318L8.818 2.939a1.5 1.5 0 0 0-.318-.242V5A1.5 1.5 0 0 0 10 6.5zm.818-1.379A3 3 0 0 1 14 7.243V12a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V4a3 3 0 0 1 3-3h2.757a3 3 0 0 1 2.122.879z" clip-rule="evenodd"/></svg></button>
				</div>
				';
			}

			if ($row['type'] == 'image') {
				echo '
				<div class="formedit">
					<input type="text" class="te_foto foto" data-id="' . $row['id'] . '" style="margin-top:30px" name="value[]"   value="' . $row['value'] . '">
			 
					<button class="btn-choose choose-image">
					<svg xmlns="http://www.w3.org/2000/svg" width="22px" height="22px" viewBox="0 0 24 24"><path fill="#fff" d="M19 2H5a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a2.81 2.81 0 0 0 .49-.05l.3-.07h.12l.37-.14l.13-.07c.1-.06.21-.11.31-.18a3.79 3.79 0 0 0 .38-.32l.07-.09a2.69 2.69 0 0 0 .27-.32l.09-.13a2.31 2.31 0 0 0 .18-.35a1 1 0 0 0 .07-.15c.05-.12.08-.25.12-.38v-.15a2.6 2.6 0 0 0 .1-.6V5a3 3 0 0 0-3-3M5 20a1 1 0 0 1-1-1v-4.31l3.29-3.3a1 1 0 0 1 1.42 0l8.6 8.61Zm15-1a1 1 0 0 1-.07.36a1 1 0 0 1-.08.14a.94.94 0 0 1-.09.12l-5.35-5.35l.88-.88a1 1 0 0 1 1.42 0l3.29 3.3Zm0-5.14L18.12 12a3.08 3.08 0 0 0-4.24 0l-.88.88L10.12 10a3.08 3.08 0 0 0-4.24 0L4 11.86V5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1Z"/></svg></button>
				</div>
				';
			}

			if ($row['type'] == 'link') {

				echo '<select class="w3-select w3-border w3-round w3-margin-bottom te-select" name="value[]">';

				foreach (glob(GSDATAPAGESPATH . "*.{xml}", GLOB_BRACE) as $page) {
					$path_parts = pathinfo($page);
					global $SITEURL;
					global $USR;
					echo "<option value='" . $SITEURL . $path_parts['filename'] . "' " . ($row['value'] == $SITEURL . $path_parts['filename'] ? 'selected' : '') . "  >" . $path_parts['filename'] . "</option>";
				}
				;

				echo '</select>';

			}

			if ($row['type'] == 'dropdown') {
				$ars = explode('||', $row['value']);

				echo '
				<input type="hidden" name="value[]" value="' . htmlspecialchars($row['value'], ENT_QUOTES, 'UTF-8') . '">
				<select name="option_dropdown[' . $row['id'] . ']" class="te-select">
				<option value="">-- Choose option --</option>';

				foreach ($ars as $sel) {
					$trimmedSel = trim($sel);
					echo '<option value="' . htmlspecialchars($trimmedSel, ENT_QUOTES, 'UTF-8') . '" ' . ($row['option_dropdown'] === $trimmedSel ? 'selected' : '') . '>' . htmlspecialchars($trimmedSel, ENT_QUOTES, 'UTF-8') . '</option>';
				}

				echo '</select>';
			}

			if ($row['type'] == 'separator') {
				echo '
				<input style="display:none" name="value[]" value="' . $row['value'] . '">

				<h5 style="font-size:1rem;padding:10px;">' . $row['value'] . '</h5>	
				';
			}

			echo '</div>';
		};
		$db->close();
	?>

	<input type="submit" name="saveToDB" class="btn-save-te" value="Save Changes">
</form>

<hr style="margin:30px 0;">
		
<footer>
	<p class="w3-small clear w3-margin-bottom w3-margin-left">Made with <span class="credit-icon">❤️</span> especially for "<b><?php echo $USR; ?></b>". Is this plugin useful to you?
	<span class="w3-btn w3-khaki w3-border w3-border-red w3-round-xlarge"><a href="https://getsimple-ce.ovh/donate" target="_blank" class="donateButton"><b>Buy Us A Coffee </b><svg xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" fill-opacity="0" d="M17 14v4c0 1.66 -1.34 3 -3 3h-6c-1.66 0 -3 -1.34 -3 -3v-4Z"><animate fill="freeze" attributeName="fill-opacity" begin="0.8s" dur="0.5s" values="0;1"/></path><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="48" stroke-dashoffset="48" d="M17 9v9c0 1.66 -1.34 3 -3 3h-6c-1.66 0 -3 -1.34 -3 -3v-9Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="48;0"/></path><path stroke-dasharray="14" stroke-dashoffset="14" d="M17 9h3c0.55 0 1 0.45 1 1v3c0 0.55 -0.45 1 -1 1h-3"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.6s" dur="0.2s" values="14;0"/></path><mask id="lineMdCoffeeHalfEmptyFilledLoop0"><path stroke="#fff" d="M8 0c0 2-2 2-2 4s2 2 2 4-2 2-2 4 2 2 2 4M12 0c0 2-2 2-2 4s2 2 2 4-2 2-2 4 2 2 2 4M16 0c0 2-2 2-2 4s2 2 2 4-2 2-2 4 2 2 2 4"><animateMotion calcMode="linear" dur="3s" path="M0 0v-8" repeatCount="indefinite"/></path></mask><rect width="24" height="0" y="7" fill="currentColor" mask="url(#lineMdCoffeeHalfEmptyFilledLoop0)"><animate fill="freeze" attributeName="y" begin="0.8s" dur="0.6s" values="7;2"/><animate fill="freeze" attributeName="height" begin="0.8s" dur="0.6s" values="0;5"/></rect></g></svg></a></span></p>
</footer>

<?php
	global $SITEURL;
	global $GSADMIN;

	function createSlug($string)
	{
		$slug = strtolower($string);
		$slug = str_replace(
			['ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż'],
			['a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z'],
			$slug
		);
		$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
		$slug = trim($slug, '-');
		return $slug;
	}

	if (isset($_POST['saveToDB'])) {
		try {
			$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');

			// Logowanie danych POST dla debugowania
			file_put_contents('debug.log', "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

			$validTypes = ['text', 'ckeditor', 'textarea', 'color', 'date', 'image', 'file', 'link', 'dropdown', 'separator'];

			foreach ($_POST['name'] as $key => $name) {
				if (empty($name)) {
					continue;
				}

				$id = isset($_POST['id'][$key]) ? (int) $_POST['id'][$key] : 0;
				$type = isset($_POST['type'][$key]) && in_array($_POST['type'][$key], $validTypes) ? $_POST['type'][$key] : '';
				$value = isset($_POST['value'][$key]) ? trim($_POST['value'][$key]) : '';
				// Pobierz option_dropdown dla typu dropdown, używając id jako klucza
				$optionDropdown = ($type === 'dropdown' && isset($_POST['option_dropdown'][$id])) ? trim($_POST['option_dropdown'][$id]) : '';
				$slug = createSlug($name);

				// Logowanie danych dla tego rekordu
				file_put_contents('debug.log', "Record ID: $id, Type: $type, Value: $value, OptionDropdown: $optionDropdown\n", FILE_APPEND);

				// Sprawdzenie unikalności slug'a
				$slugCheckStmt = $db->prepare('SELECT id FROM items WHERE slug = :slug AND id != :id');
				$slugCheckStmt->bindValue(':slug', $slug, SQLITE3_TEXT);
				$slugCheckStmt->bindValue(':id', $id, SQLITE3_INTEGER);
				$slugCheckResult = $slugCheckStmt->execute()->fetchArray(SQLITE3_ASSOC);
				if ($slugCheckResult) {
					throw new Exception('Slug "' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '" is already in use');
				}

				// Walidacja dla dropdown: upewnij się, że option_dropdown jest jedną z opcji w value
				if ($type === 'dropdown' && !empty($optionDropdown)) {
					$options = array_map('trim', explode('||', $value));
					if (!in_array($optionDropdown, $options)) {
						throw new Exception('Invalid option selected for dropdown: ' . htmlspecialchars($optionDropdown));
					}
				}

				if ($id > 0) {
					$stmt = $db->prepare('UPDATE items SET name = :name, slug = :slug, type = :type, value = :value, option_dropdown = :option_dropdown WHERE id = :id');
					$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
				} else {
					$stmt = $db->prepare('INSERT INTO items (name, slug, type, value, option_dropdown) VALUES (:name, :slug, :type, :value, :option_dropdown)');
				}

				$stmt->bindValue(':name', $name, SQLITE3_TEXT);
				$stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
				$stmt->bindValue(':type', $type, SQLITE3_TEXT);
				$stmt->bindValue(':value', $value, SQLITE3_TEXT);
				$stmt->bindValue(':option_dropdown', $optionDropdown, SQLITE3_TEXT);

				$stmt->execute();
			}

			$db->close();
			echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($SITEURL . $GSADMIN . '/load.php?id=themeEngine&settings&success=updated', ENT_QUOTES, 'UTF-8') . '">';
			exit;
		} catch (Exception $e) {
			$db->close();
			echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($SITEURL . $GSADMIN . '/load.php?id=themeEngine&settings&error=' . urlencode($e->getMessage()), ENT_QUOTES, 'UTF-8') . '">';
			exit;
		}
	}

	global $EDTOOL;
	global $toolbar;
	global $options;
	global $EDOPTIONS;

	if (isset($EDTOOL))
		$EDTOOL = returnJsArray($EDTOOL);
	if (isset($toolbar))
		$toolbar = returnJsArray($toolbar);
	else if (strpos(trim($EDTOOL), '[[') !== 0 && strpos(trim($EDTOOL), '[') === 0) {
		$EDTOOL = "[$EDTOOL]";
	}

	if (isset($toolbar) && strpos(trim($toolbar), '[[') !== 0 && strpos($toolbar, '[') === 0) {
		$toolbar = "[$toolbar]";
	}
	$toolbar = isset($EDTOOL) ? ",toolbar: " . trim($EDTOOL, ",") : '';
	$options = isset($EDOPTIONS) ? ',' . trim($EDOPTIONS, ",") : '';

?>

<script type="text/javascript" src="template/js/ckeditor/ckeditor.js?t=3.3.16"></script>
<script type="text/javascript">
	document
		.querySelectorAll(`#post-content`)
		.forEach(c => {

			var editor = CKEDITOR.replace(c, {
				skin: 'getsimple',
				forcePasteAsPlainText: true,
				language: 'en',
				defaultLanguage: 'en',
				<?php
				global $TEMPLATE;
				if (file_exists(GSTHEMESPATH . $TEMPLATE . "/editor.css")) {
					$fullpath = suggest_site_path();
					?>
				contentsCss: '<?php echo $fullpath; ?>theme/<?php echo $TEMPLATE; ?>/editor.css',
				<?php } ?>
				entities: true,
				// uiColor : '#FFFFFF',
				height: '300px',
				baseHref: '<?php global $SITEURL;
				echo $SITEURL; ?>',
				tabSpaces: 10,
				filebrowserBrowseUrl: 'filebrowser.php?type=all',
				filebrowserImageBrowseUrl: 'filebrowser.php?type=images',
				filebrowserWindowWidth: '730',
				filebrowserWindowHeight: '500'
				<?php
				echo $toolbar; ?>
				<?php
				echo $options; ?>
			});
		});
</script>

<script>
	if (document.querySelector('.te_foto') !== null) {

		let data = 0;

		document
			.querySelectorAll('.formedit')
			.forEach((e, i) => {

				e.querySelector('.choose-image')
					.addEventListener('click', y => {
						y.preventDefault();

						const url = "<?php global $SITEURL;
						echo $SITEURL . "plugins/themeEngine/browser/imagebrowser.php?"; ?>&func=" + e.querySelector('input[type="text"]').getAttribute('data-id');

						const win = window.open(url, "myWindow", "tolbar=no,scrollbars=no,menubar=no,width=500,height=500");

						win.window.focus();
					});

			})
	};

	if (document.querySelector('.te_file') !== null) {
		let data = 0;

		document
			.querySelectorAll('.formedit-file')
			.forEach((e, i) => {
				e.querySelector('.choose-file')
					.addEventListener('click', y => {
						y.preventDefault();
						const url = "<?php global $SITEURL;
						echo $SITEURL . "plugins/themeEngine/browser/filebrowser.php?"; ?>&type=all&func=" + e.querySelector('input[type="text"]').getAttribute('data-id');

						const win = window.open(url, "myWindow", "tolbar=no,scrollbars=no,menubar=no,width=500,height=500");
						win.window.focus();
					});
			})
	}
</script>
