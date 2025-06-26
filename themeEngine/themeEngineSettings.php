<?php
global $SITEURL;
global $GSADMIN;
global $USR;

// Function to create a slug (unchanged)
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

// Handle delete action
if (isset($_GET['delete_id'])) {
	try {
		$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');
		$deleteId = (int) $_GET['delete_id'];
		if ($deleteId <= 0) {
			throw new Exception('Invalid ID');
		}

		// Get the item_order of the deleted item
		$stmt = $db->prepare('SELECT item_order FROM items WHERE id = :id');
		$stmt->bindValue(':id', $deleteId, SQLITE3_INTEGER);
		$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
		$deletedOrder = $result ? $result['item_order'] : 0;

		// Delete the item
		$stmt = $db->prepare('DELETE FROM items WHERE id = :id');
		$stmt->bindValue(':id', $deleteId, SQLITE3_INTEGER);
		$stmt->execute();

		// Reorder remaining items
		$stmt = $db->prepare('UPDATE items SET item_order = item_order - 1 WHERE item_order > :deletedOrder');
		$stmt->bindValue(':deletedOrder', $deletedOrder, SQLITE3_INTEGER);
		$stmt->execute();

		$db->close();
		echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($SITEURL . $GSADMIN . '/load.php?id=themeEngine&success=Field Deleted') . '">';
		exit;
	} catch (Exception $e) {
		$db->close();
		echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($SITEURL . $GSADMIN . '/load.php?id=themeEngine&error=' . urlencode($e->getMessage())) . '">';
		exit;
	}
}

// Handle form submission
if (isset($_POST['saveToDB'])) {
	try {
		$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');

		$validTypes = ['text', 'ckeditor', 'textarea', 'color', 'date', 'image', 'file', 'link', 'dropdown', 'separator'];

		foreach ($_POST['name'] as $key => $name) {
			if (empty($name)) {
				continue;
			}

			$id = isset($_POST['id'][$key]) ? (int) $_POST['id'][$key] : 0;
			$type = isset($_POST['type'][$key]) && in_array($_POST['type'][$key], $validTypes) ? $_POST['type'][$key] : '';
			$value = $_POST['value'][$key] ?? '';
			$slug = createSlug($name);
			$item_order = $key; // Use array index as item_order

			// Check if slug is unique
			$slugCheckStmt = $db->prepare('SELECT id FROM items WHERE slug = :slug AND id != :id');
			$slugCheckStmt->bindValue(':slug', $slug, SQLITE3_TEXT);
			$slugCheckStmt->bindValue(':id', $id, SQLITE3_INTEGER);
			$slugCheckResult = $slugCheckStmt->execute()->fetchArray(SQLITE3_ASSOC);
			if ($slugCheckResult) {
				throw new Exception('Slug "' . htmlspecialchars($slug) . '" is already in use');
			}

			if ($id > 0) {
				// Update existing record
				$stmt = $db->prepare('UPDATE items SET name = :name, slug = :slug, type = :type, value = :value, item_order = :item_order WHERE id = :id');
				$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
			} else {
				// Insert new record
				$stmt = $db->prepare('INSERT INTO items (name, slug, type, value, item_order) VALUES (:name, :slug, :type, :value, :item_order)');
			}

			$stmt->bindValue(':name', $name, SQLITE3_TEXT);
			$stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
			$stmt->bindValue(':type', $type, SQLITE3_TEXT);
			$stmt->bindValue(':value', $value, SQLITE3_TEXT);
			$stmt->bindValue(':item_order', $item_order, SQLITE3_INTEGER);

			$stmt->execute();
		}

		$db->close();
		echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($SITEURL . $GSADMIN . '/load.php?id=themeEngine&success=updated') . '">';
		exit;
	} catch (Exception $e) {
		$db->close();
		echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($SITEURL . $GSADMIN . '/load.php?id=themeEngine&error=' . urlencode($e->getMessage())) . '">';
		exit;
	}
}
?>

<style>
	html{
		scroll-behavior: smooth;
	}
	.former-te :is(input, textarea, select) {
		border-radius: 0 !important;
		padding: 10px !important;
	}
	.former-te textarea{
		height: 50px;
		width:98%;
	}
	.set-te{
		min-height:200px;
		background:#fafafa;
		border:solid 1px #ddd;
		margin:10px 0;
		padding:10px;
		border-bottom:solid 2px black;
	}
	.te-object{
		background:#fff;
		margin:10px 0;
		display:flex;
		flex-direction:column;
		border:solid 1px #ddd;
		padding:10px;
		position:relative;
		margin-bottom:20px;
	}
	.te-object a{
		margin:10px;
		position:absolute;
		top:0;
		right:0;
	}
	.te-input{
		padding:5px;
		border-radius:5px;
	}
	.te-textarea{
		width:98%;
		height:120px;
		margin:20px 0;
		color:blue;
	}
	.te-code{
		background:#ddd;
		border:solid 1px #111;
		margin:10px 0;
		padding:5px;
		display:block;
		margin-top:30px;
		color:darkblue;
	}
	.te-select{
		padding:5px;
		border-radius:5px;
		margin-top:10px;
	}
	.btn-te,.btn-te-de {
		background:darkorange;
		color:white;
		border:none; 
		padding:10px;
		cursor:pointer
	}
	input.btn-save-te{
		background:#4CAF50!important;
		color:white
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
		flex-wrap:wrap;
	}
</style>

<h3>😾 themeEngine Settings</h3>

<hr style="margin:20px 0">
<div class="lister">
	<?php
	$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');
	$result = $db->query('SELECT * FROM items ORDER BY item_order ASC'); // Sort by item_order
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

		echo '<a href="#' . $row['id'] . '" class="goto">' . $row['name'] . '</a>';
	}
	;
	$db->close();
	?>
</div>
<button class="btn-te-de">Add New Theme Field to Top</button>
<button class="btn-te">Add New Theme Field to Bottom</button>

<form class="former-te" method="POST">
	<div id="set-te">
		<?php
		$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');
		$result = $db->query('SELECT * FROM items ORDER BY item_order ASC'); // Sort by item_order
		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
			echo '
			<div class="te-object" id="' . $row['id'] . '">
				<a class="delme" href="' . $SITEURL . $GSADMIN . '/load.php?id=themeEngine&delete_id=' . $row['id'] . '">
					<svg width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<g id="Icon-Set" transform="translate(-568.000000, -1087.000000)" fill="#ff0000">
								<path d="M584,1117 C576.268,1117 570,1110.73 570,1103 C570,1095.27 576.268,1089 584,1089 C591.732,1089 598,1095.27 598,1103 C598,1110.73 591.732,1117 584,1117 L584,1117 Z M584,1087 C575.163,1087 568,1094.16 568,1103 C568,1111.84 575.163,1119 584,1119 C592.837,1119 600,1111.84 600,1103 C600,1094.16 592.837,1087 584,1087 L584,1087 Z M589.717,1097.28 C589.323,1096.89 588.686,1096.89 588.292,1097.28 L583.994,1101.58 L579.758,1097.34 C579.367,1096.95 578.733,1096.95 578.344,1097.34 C577.953,1097.73 577.953,1098.37 578.344,1098.76 L582.58,1102.99 L578.314,1107.26 C577.921,1107.65 577.921,1108.29 578.314,1108.69 C578.708,1109.08 579.346,1109.08 579.74,1108.69 L584.006,1104.42 L588.242,1108.66 C588.633,1109.05 589.267,1109.05 589.657,1108.66 C590.048,1108.27 590.048,1107.63 589.657,1107.24 L585.42,1103.01 L589.717,1098.71 C590.11,1098.31 590.11,1097.68 589.717,1097.28 Z" id="cross-circle"></path>
							</g>
						</g>
					</svg>
				</a>
				<code class="te-code" onclick="navigator.clipboard.writeText(`&lt;?php themeEngine(&#34;' . $row['slug'] . '&#34;);?>`);alert(`copied to clipboard!`)">&lt;?php themeEngine("' . $row['slug'] . '");?> or &lt;?php themeEngine_r("' . $row['slug'] . '");?></code>
				<input type="hidden" name="id[]" placeholder="Name" value="' . $row['id'] . '" class="te-input"> 
				<input name="name[]" placeholder="Name" value="' . $row['name'] . '" class="te-input"> 
				<select class="te-select" name="type[]">
					<option value="text" ' . ($row['type'] == 'text' ? 'selected' : '') . '>Text</option>
					<option value="ckeditor" ' . ($row['type'] == 'ckeditor' ? 'selected' : '') . '>ckEditor</option>
					<option value="textarea" ' . ($row['type'] == 'textarea' ? 'selected' : '') . '>TextArea</option>
					<option value="color" ' . ($row['type'] == 'color' ? 'selected' : '') . '>Color</option>
					<option value="date" ' . ($row['type'] == 'date' ? 'selected' : '') . '>Date</option>
					<option value="image" ' . ($row['type'] == 'image' ? 'selected' : '') . '>Image</option>
					<option value="file" ' . ($row['type'] == 'file' ? 'selected' : '') . '>File</option>
					<option value="link" ' . ($row['type'] == 'link' ? 'selected' : '') . '>Page Link</option>
					<option value="dropdown" ' . ($row['type'] == 'dropdown' ? 'selected' : '') . '>Dropdown</option>
					<option value="separator" ' . ($row['type'] == 'separator' ? 'selected' : '') . '>Separator (backend only)</option>
				</select>
				<textarea class="te-textarea" name="value[]" placeholder="value ( dropdown - example||example2 )">' . $row['value'] . '</textarea>
			</div>';
		}
		$db->close();
		?>
	</div>

	<input class="btn-save-te" type="submit" name="saveToDB" value="Save Settings">
</form>

<hr style="margin:30px 0;">
		
<footer>
	<p class="w3-small clear w3-margin-bottom w3-margin-left">Made with <span class="credit-icon">❤️</span> especially for "<b><?php echo $USR; ?></b>". Is this plugin useful to you?
	<span class="w3-btn w3-khaki w3-border w3-border-red w3-round-xlarge"><a href="https://getsimple-ce.ovh/donate" target="_blank" class="donateButton"><b>Buy Us A Coffee </b><svg xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" fill-opacity="0" d="M17 14v4c0 1.66 -1.34 3 -3 3h-6c-1.66 0 -3 -1.34 -3 -3v-4Z"><animate fill="freeze" attributeName="fill-opacity" begin="0.8s" dur="0.5s" values="0;1"/></path><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="48" stroke-dashoffset="48" d="M17 9v9c0 1.66 -1.34 3 -3 3h-6c-1.66 0 -3 -1.34 -3 -3v-9Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="48;0"/></path><path stroke-dasharray="14" stroke-dashoffset="14" d="M17 9h3c0.55 0 1 0.45 1 1v3c0 0.55 -0.45 1 -1 1h-3"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.6s" dur="0.2s" values="14;0"/></path><mask id="lineMdCoffeeHalfEmptyFilledLoop0"><path stroke="#fff" d="M8 0c0 2-2 2-2 4s2 2 2 4-2 2-2 4 2 2 2 4M12 0c0 2-2 2-2 4s2 2 2 4-2 2-2 4 2 2 2 4M16 0c0 2-2 2-2 4s2 2 2 4-2 2-2 4 2 2 2 4"><animateMotion calcMode="linear" dur="3s" path="M0 0v-8" repeatCount="indefinite"/></path></mask><rect width="24" height="0" y="7" fill="currentColor" mask="url(#lineMdCoffeeHalfEmptyFilledLoop0)"><animate fill="freeze" attributeName="y" begin="0.8s" dur="0.6s" values="7;2"/><animate fill="freeze" attributeName="height" begin="0.8s" dur="0.6s" values="0;5"/></rect></g></svg></a></span></p>
</footer>

<script>
	document.querySelector('.btn-te').addEventListener('click', () => {
		document.querySelector('#set-te').insertAdjacentHTML('beforeEnd', `
			<div style="background:#fff;width:100%;margin:10px 0;display:flex;flex-direction:column;border:solid 1px #ddd;padding:10px;border-bottom:solid 2px black;" class="te-object">
				<input type="hidden" name="id[]" placeholder="Name" style="padding:5px;border-radius:5px"> 
					<a class="delme"  onclick="event.preventDefault();this.parentElement.remove()" >
					<svg width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<g id="Icon-Set" transform="translate(-568.000000, -1087.000000)" fill="#000000">
								<path d="M584,1117 C576.268,1117 570,1110.73 570,1103 C570,1095.27 576.268,1089 584,1089 C591.732,1089 598,1095.27 598,1103 C598,1110.73 591.732,1117 584,1117 L584,1117 Z M584,1087 C575.163,1087 568,1094.16 568,1103 C568,1111.84 575.163,1119 584,1119 C592.837,1119 600,1111.84 600,1103 C600,1094.16 592.837,1087 584,1087 L584,1087 Z M589.717,1097.28 C589.323,1096.89 588.686,1096.89 588.292,1097.28 L583.994,1101.58 L579.758,1097.34 C579.367,1096.95 578.733,1096.95 578.344,1097.34 C577.953,1097.73 577.953,1098.37 578.344,1098.76 L582.58,1102.99 L578.314,1107.26 C577.921,1107.65 577.921,1108.29 578.314,1108.69 C578.708,1109.08 579.346,1109.08 579.74,1108.69 L584.006,1104.42 L588.242,1108.66 C588.633,1109.05 589.267,1109.05 589.657,1108.66 C590.048,1108.27 590.048,1107.63 589.657,1107.24 L585.42,1103.01 L589.717,1098.71 C590.11,1098.31 590.11,1097.68 589.717,1097.28 Z" id="cross-circle"></path>
							</g>
						</g>
					</svg>
				</a>
				<input name="name[]" placeholder="Name" required style="padding:5px;border-radius:5px;margin-top:34px"> 
				<select style="padding:5px;border-radius:5px;margin-top:10px;" name="type[]">
					<option value="text">Text</option>
					<option value="ckeditor">ckEditor</option>
					<option value="textarea">Textarea</option>
					<option value="color">Color</option>
					<option value="date">Date</option>
					<option value="image">Image</option>
					<option value="file">File</option>
					<option value="link">Page Link</option>
					<option value="dropdown">Dropdown</option>
					<option value="separator">Separator (backend only)</option>
				</select>
				<textarea name="value[]" placeholder="value ( dropdown - example||example2 )" class="te-textarea"></textarea>
			</div>
		`);
	});

		document.querySelector('.btn-te-de').addEventListener('click', () => {
		document.querySelector('#set-te').insertAdjacentHTML('afterBegin', `
			<div style="background:#fff;width:100%;margin:10px 0;display:flex;flex-direction:column;border:solid 1px #ddd;padding:10px;border-bottom:solid 2px black;" class="te-object">
				<input type="hidden" name="id[]" placeholder="Name" style="padding:5px;border-radius:5px"> 
						<a class="delme" onclick="event.preventDefault();this.parentElement.remove()" onclick="" >
					<svg width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<g id="Icon-Set" transform="translate(-568.000000, -1087.000000)" fill="#000000">
								<path d="M584,1117 C576.268,1117 570,1110.73 570,1103 C570,1095.27 576.268,1089 584,1089 C591.732,1089 598,1095.27 598,1103 C598,1110.73 591.732,1117 584,1117 L584,1117 Z M584,1087 C575.163,1087 568,1094.16 568,1103 C568,1111.84 575.163,1119 584,1119 C592.837,1119 600,1111.84 600,1103 C600,1094.16 592.837,1087 584,1087 L584,1087 Z M589.717,1097.28 C589.323,1096.89 588.686,1096.89 588.292,1097.28 L583.994,1101.58 L579.758,1097.34 C579.367,1096.95 578.733,1096.95 578.344,1097.34 C577.953,1097.73 577.953,1098.37 578.344,1098.76 L582.58,1102.99 L578.314,1107.26 C577.921,1107.65 577.921,1108.29 578.314,1108.69 C578.708,1109.08 579.346,1109.08 579.74,1108.69 L584.006,1104.42 L588.242,1108.66 C588.633,1109.05 589.267,1109.05 589.657,1108.66 C590.048,1108.27 590.048,1107.63 589.657,1107.24 L585.42,1103.01 L589.717,1098.71 C590.11,1098.31 590.11,1097.68 589.717,1097.28 Z" id="cross-circle"></path>
							</g>
						</g>
					</svg>
				</a>
				<input name="name[]" placeholder="Name" required style="padding:5px;border-radius:5px;margin-top:34px;"> 
				<select style="padding:5px;border-radius:5px;margin-top:10px;" name="type[]">
					<option value="text">Text</option>
					<option value="ckeditor">ckEditor</option>
					<option value="textarea">Textarea</option>
					<option value="color">Color</option>
					<option value="date">Date</option>
					<option value="image">Image</option>
					<option value="file">File</option>
					<option value="link">Page Link</option>
					<option value="dropdown">Dropdown</option>
					<option value="separator">Separator (backend only)</option>
				</select>
				<textarea name="value[]" placeholder="value ( dropdown - example||example2 )" class="te-textarea"></textarea>
			</div>
		`);
	});
</script>

<script src="<?php echo $SITEURL; ?>plugins/themeEngine/js/Sortable.min.js"></script>
<script>
	const element = document.getElementById('set-te');
	if (element) {
		new Sortable(element, {
			animation: 150,
			ghostClass: 'sortable-ghost',
		});
	}

	document.querySelectorAll('.delme').forEach(element => {
		element.addEventListener('click', (e) => {
			if (!confirm('Are you sure?')) {
				e.preventDefault(); 
				return false; 
			}
		});
	});
</script>
