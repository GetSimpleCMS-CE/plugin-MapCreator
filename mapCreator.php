<?php

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

# add in this plugin's language file
i18n_merge('mapCreator') || i18n_merge('mapCreator', 'en_US');

# register plugin
register_plugin(
	$thisfile, //Plugin id
	'MapCreator', 	//Plugin name
	'5.0', 		//Plugin version
	'Multicolor',  //Plugin author
	'https://discord.gg/vkySHPxpg2', //author website
	i18n_r('readTime/LANG_Description'), //Plugin description
	'plugins', //page type - on which admin tab to display
	'mapCreator'  //main function (administration)
);

#script
register_style('mapcreator', $SITEURL . 'plugins/mapCreator/css/leaflet.css', '1.0', 'all');
queue_style('mapcreator', GSFRONT);

#nav
add_action('plugins-sidebar', 'createSideMenu', array($thisfile, i18n_r('mapCreator/LANG_Settings')));


function mapCreator()
{
	global $SITEURL;

	$html = '<link rel="stylesheet" href="' . $SITEURL . 'plugins/mapCreator/css/leaflet.css">';

	$html .= '<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

		<style>.mapcreator .uil{font-size:1.1rem}</style>

		<h3>' . i18n_r('mapCreator/LANG_Create_Map') . '  📍</h3>
		<div style="margin:10px 0; display:block; width:100%; border:solid 1px #ddd; background:#fafafa; margin-bottom:15px; padding:10px; box-sizing:border-box;">
	  
			<h3>' . i18n_r('mapCreator/LANG_Get_Coordinates') . ':</h3>

			<ul>
				<li>' . i18n_r('mapCreator/LANG_In_Browser') . ' <a href="https://www.google.com/maps" target="_blank">' . i18n_r('mapCreator/LANG_Google_Maps') . '</a>.</li>
				<li>' . i18n_r('mapCreator/LANG_Right_Click') . '</li>
				<li>' . i18n_r('mapCreator/LANG_Open_Popup') . '</li>
				<li>' . i18n_r('mapCreator/LANG_Copy_Coordinates') . '</li>
				<li>' . i18n_r('mapCreator/LANG_Paste_into') . '</li>
			</ul>
		
		</div>

		<form method="POST" action="#" class="mapcreator" style="background:#fafafa; border:solid 1px #ddd; box-sizing:border-box; padding:10px;">
		
			<i class="uil uil-info-circle" style="padding-right:10px;"></i><input type="text" name="namemap" required placeholder="' . i18n_r('mapCreator/LANG_Map_Name') . '" style="padding:5px; width:95%; margin-bottom:10px; box-sizing:border-box;">
			
			<i class="uil uil-arrows-shrink-v" style="padding-right:10px;"></i><input type="text" name="height"  required placeholder="' . i18n_r('mapCreator/LANG_Map_Height') . '" style="padding:5px; width:95%; box-sizing:border-box; margin-bottom:10px;">
			
			<i class="uil uil-search-plus" style="padding-right:10px;"></i><input type="text" name="zoom"  required placeholder="' . i18n_r('mapCreator/LANG_Map_Zoom') . '" style="padding:5px; width:95%; box-sizing:border-box; margin-bottom:10px;">

			<i class="uil uil-map" style="padding-right:10px;"></i><input type="text" name="localization"  required placeholder="' . i18n_r('mapCreator/LANG_Map_Center') . '" style="padding:5px; width:95%; box-sizing:border-box; margin-bottom:10px;">

			<p style="margin:0; margin-bottom:10px;"> ' . i18n_r('mapCreator/LANG_Mouse_Scroll') . '</p>

			<i class="uil uil-mouse" style="padding-right:10px;"></i> <select name="mousescroll" style="padding:6px; display:inline-block; width:95%; box-sizing:border-box; margin-bottom:10px; border:solid 1px rgba(0,0,0,0.3); background:#fff;">
				<option value="true">' . i18n_r('mapCreator/LANG_Yes') . '</option>
				<option value="false">' . i18n_r('mapCreator/LANG_No') . '</option>
			</select>

			<div style="display:flex; align-items:center; justify-content:space-between; background-image:linear-gradient(to right,#ddd,transparent); border-right:none; padding:10px; width:96%; margin-left:0; border-left:5px solid #D61C4E; margin-top:10px;">
				<b>' . i18n_r('mapCreator/LANG_Add_Point_on_Map') . '</b><br> 
				<button class="addnewpoint" style="background:#D61C4E; color:#fff; border:none; padding:10px; cursor: pointer;" title="' . i18n_r('mapCreator/LANG_Add_Point') . '"><i class="uil uil-plus-square"></i></button>
			</div>

			<div class="nextpoint"></div>
			<div style="width:100%; height:30px"></div>

			<input type="submit" style="background:#000; color:#fff; padding:10px 15px; border:none; margin-top:15px; cursor: pointer;" value="' . i18n_r('mapCreator/LANG_Create_Map') . '" name="createmap">

		</form>';

	$html .= '
		<script src="' . $SITEURL . 'admin/template/js/ckeditor/ckeditor.js"></script>';
	$html .= "<script>CKEDITOR.replace(popupcontent, {
				filebrowserBrowseUrl: 'filebrowser.php?type=all',
				filebrowserImageBrowseUrl: 'filebrowser.php?type=images',
				filebrowserWindowWidth: '730',
				filebrowserWindowHeight: '500'
				, toolbar: 'advanced'
			});
		</script>";

	echo $html;

	echo "
		<script>
			let countPoint = 1;
			const addBtn = document.querySelector('.addnewpoint');

			addBtn.addEventListener('click',e=>{
				e.preventDefault();

				document.querySelector('.nextpoint').insertAdjacentHTML('afterbegin',`

					<div style='position:relative; width:100%; height:auto; padding:10px; border:solid 1px #ddd; margin:10px 0; box-sizing:border-box; padding-top:30px;'>

						<button class='closeitem' style='position:absolute; right:0;top:0; background:#D61C4E; color:#fff; padding:6px; border:none;' onclick='event.preventDefault();this.parentNode.remove()'>" . i18n_r('mapCreator/LANG_Close') . "</button>

						<i class='uil uil-map-marker' style='padding-right:10px;'></i><input type='text' name='localizationpoint[]'  required placeholder='" . i18n_r('mapCreator/LANG_Marker_Coordinates') . "' style='margin-top:10px; padding:5px; width:95%; box-sizing:border-box; margin-bottom:10px;'>
						<br>

						<i class='uil uil-chat' style='padding-right:10px;'></i><input type='checkbox' name='showpopup[]' style='margin-top:10px; margin-bottom:10px;'> " . i18n_r('mapCreator/LANG_Show_Popup') . "

						<textarea name='popupcontent[]' id='popupcontent`+countPoint+`'></textarea>
				`)

				CKEDITOR.replace('popupcontent'+countPoint, {
					filebrowserBrowseUrl: 'filebrowser.php?type=all',
					filebrowserImageBrowseUrl: 'filebrowser.php?type=images',
					filebrowserWindowWidth: '730',
					filebrowserWindowHeight: '500'
					, toolbar: 'advanced'
				});

				countPoint++;
			});
		</script>";

	#listmap

	$path = GSDATAOTHERPATH . '/mapCreator/*.txt';

	$filenames = glob($path);

	foreach ($filenames as $filename) {
		echo '
			<form method="post" style="background:#fafafa; border:solid 1px #ddd; box-sizing:border-box; padding:10px; margin-top:20px;">';

		$base = basename($filename);

		$nename = substr($base, 0, -4);

		echo  '<h3 style="margin-top:10px;">' . $nename . '</h3>   ';

		echo '<p>' . i18n_r('mapCreator/LANG_Add') . ' <span style="color:blue;"> &#60;?php getMapCreator("' . $nename . '") ;?&#62; </span> ' . i18n_r('mapCreator/LANG_In_Template') . ' <span style="color:green">[% mapcreator=' . $nename . ' %] </span> ' . i18n_r('mapCreator/LANG_In_Editor') . '.</p>';

		echo '<input type="text" name="dir" value="' . $filename . '" style="display:none">
				<textarea name="editcontent" style="width:100%; height:250px; background:#003865; color:#fff; box-sizing:border-box; padding:5px;">';

		echo file_get_contents($filename);

		echo '</textarea>
				<div style="height:300px; overflow:hidden; margin-top:10px; border:solid 2px #ddd;">';

		echo file_get_contents($filename);

		echo '
				</div>
				<input type="submit" name="SaveEdit" value="' . i18n_r('mapCreator/LANG_Save_Edited') . '" style="margin-top:10px; margin-right:10px; background:#003865; padding:10px 15px; color:#fff; border:none; cursor: pointer;">
		 
				<input type="submit" name="deletemap" onclick="return confirm(`' . i18n_r('mapCreator/LANG_Are_You_Sure') . '`)"  value="Delete Map" style="cursor:pointer; background:#D61C4E; padding:10px 15px; color:#fff; border:none; pointer:cursor; margin-bottom:5px;">
	  
			</form>';

		if (isset($_POST['SaveEdit'])) {
			file_put_contents($_POST['dir'], $_POST['editcontent']);
			echo ("<meta http-equiv='refresh' content='0'>");
		};

		if (isset($_POST['deletemap'])) {
			unlink($_POST['dir']);
			echo ("<meta http-equiv='refresh' content='0'>");
		};
	}
	#endlistmap

	if (isset($_POST['createmap'])) {
		global $SITEURL;

		$nameFromPost = $_POST['namemap'];


		function toCamelCase($string)
		{
			$string = strtolower($string);
			$string = ucwords($string);
			$string = str_replace(' ', '', $string);
			$string = lcfirst($string);
			return $string;
		};

		$namemap = toCamelCase($nameFromPost);

		$localization = $_POST['localization'];
		$zoom = $_POST['zoom'];

		$localizationPoint = $_POST['localizationpoint'];
		$showpopup = $_POST['showpopup'];
		$popupcontent = $_POST['popupcontent'];
		$height = $_POST['height'];
		$mousescroll = $_POST['mousescroll'];

		// Set up the data
		$data = "
			<style>#" . $namemap . "{height:" . $height . ";}</style>
			<div id='" . $namemap . "'></div>
			
			<script src='" . $SITEURL . "plugins/mapCreator/js/leaflet.js'></script>
			<script>
				let $namemap = L.map('" . $namemap . "', {
					center: [" . $localization . "],
					zoom:" . $zoom . ",
					scrollWheelZoom:" . $mousescroll . ",
				});
				";

		foreach ($localizationPoint as $key => $n) {
			if ($showpopup[$key] == "on") {
				$data .= "L.marker([" . $n . "]).addTo(" . $namemap . ").bindPopup(`" . $popupcontent[$key] . "`,{autoClose:false}).openPopup();";
			} else {
				$data .= "L.marker([" . $n . "]).addTo(" . $namemap . ");";
			};
		};

		$data .= "	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: '© OpenStreetMap',
				}).addTo(" . $namemap . ");
			</script>";

		$folder        = GSDATAOTHERPATH . '/mapCreator/';
		$filename      = $folder . $namemap . '.txt';
		$chmod_mode    = 0755;
		$folder_exists = file_exists($folder) || mkdir($folder, $chmod_mode);

		// Save the file (assuming that the folder indeed exists)
		if ($folder_exists) {
			file_put_contents($filename, $data);
		}

		echo ("<meta http-equiv='refresh' content='0'>");
	}

	echo '
		<form action="https://www.paypal.com/cgi-bin/webscr" class="moneyshot" method="post" target="_top" style="display:flex; flex-direction:column; margin-top:10px; padding:20px; box-sizing:border-box; width:100%; align-items:center; justify-content:space-between; background:#D61C4E; color:#fff;">
			<p style="margin:0; padding:10px;">' . i18n_r('mapCreator/LANG_PayPal') . '</p>
			<input type="hidden" name="cmd" value="_s-xclick" />
			<input type="hidden" name="hosted_button_id" value="KFZ9MCBUKB7GL" />
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
			<img alt="" border="0" src="https://www.paypal.com/en_PL/i/scr/pixel.gif" width="1" height="1" />
		</form>
		<div style="width:100%; margin: 15px 0 -25px 0; opacity: 0.5; text-align:center;">
			<p>' . i18n_r('mapCreator/LANG_Based_On') . ' <a href="https://leafletjs.com/" style="text-decoration:none;" target="_blank">leaflet.js</a></p>
		</div>
		';
}

add_action('theme-header', 'pageBeginMapCreator');
function pageBeginMapCreator()
{
	global $content;
	$newcontent = preg_replace_callback(
		'/\\[% mapcreator=(.*) %\\]/i',
		'getMapCreatorShortcode',
		$content
	);
	$content = $newcontent;
};

function getMapCreator($name)
{
	global $SITEURL;
	$file = GSDATAOTHERPATH . '/mapCreator/' . $name . '.txt';
	if (file_exists($file)) {
		echo file_get_contents($file);
	}
};

function getMapCreatorShortcode($matches)
{
	$name =  $matches[1];
	global $SITEURL;
	$file = GSDATAOTHERPATH . '/mapCreator/' . $name . '.txt';
	if (file_exists($file)) {
		return file_get_contents($file);
	}
};
