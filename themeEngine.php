<?php

$choose = 'plugins';

if (isset($_GET['settings'])) {
	$choose = 'themes';
};

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 			//Plugin id
	'ThemeEngine 😾', 	//Plugin name
	'1.2',				//Plugin version
	'CE Team',			//Plugin author
	'https://getsimple-ce.ovh/donate', //author website
	'Field settings for your themes. (based on sqlite3)', //Plugin description
	$choose, 			//page type - on which admin tab to display
	'themeEngine_show'  //main function (administration)
);

# activate filter 

# add a link in the admin tab 'theme'
add_action('plugins-sidebar', 'createSideMenu', array($thisfile, 'ThemeEngine Settings 😾', 'creator'));
add_action('theme-sidebar', 'createSideMenu', array($thisfile, 'ThemeEngine Fields 😾', 'settings'));

add_action('header', 'themeEngineMakeDB');

function themeEngineMakeDB() {
	try {
		$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');

		$db->exec('CREATE TABLE IF NOT EXISTS items (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			name TEXT NOT NULL UNIQUE,
			slug TEXT NOT NULL,
			type TEXT,
			value TEXT,
			option_dropdown TEXT,
			item_order INTEGER DEFAULT 0
		)');

		$db->close();
	} catch (Exception $e) {
		die("Error creating database: " . htmlspecialchars($e->getMessage()));
	}
}

# functions
function themeEngine($slug) {
	try {
		$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');

		$slug = $slug;
		$stmt = $db->prepare('SELECT * FROM items WHERE slug = :slug');
		$stmt->bindValue(':slug', $slug, SQLITE3_TEXT);

		$result = $stmt->execute();

		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
			if ($row['type'] == 'dropdown') {
				echo $row['option_dropdown'];
			}elseif($row['type'] == 'file' || $row['type'] == 'image'){
				global $SITEURL;
				echo $SITEURL.$row['value'];
			} else {
				echo $row['value'];
			}
		}

		$db->close();
	} catch (Exception $e) {
		echo "Błąd: " . $e->getMessage();
	}
}

function themeEngine_r($slug){
	try {
		$db = new SQLite3(GSDATAOTHERPATH . 'themeEngine.db');

		$slug = $slug;
		$stmt = $db->prepare('SELECT * FROM items WHERE slug = :slug');
		$stmt->bindValue(':slug', $slug, SQLITE3_TEXT);

		$result = $stmt->execute();

		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
			if ($row['type'] == 'dropdown') {
				return $row['option_dropdown'];
			}elseif($row['type'] == 'file' || $row['type'] == 'image'){
				global $SITEURL;
				return $SITEURL.$row['value'];
			} else {
				return $row['value'];
			}
		}

		$db->close();
	} catch (Exception $e) {
		echo "Błąd: " . $e->getMessage();
	}
}

function themeEngine_show() {
	if (isset($_GET['settings'])) {
		include(GSPLUGINPATH . 'themeEngine/themeEngineShow.php');
	} else {
		include(GSPLUGINPATH . 'themeEngine/themeEngineSettings.php');
	}
	;
}

?>
