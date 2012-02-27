<?php
/**
 * Plugin name: Download Button Shortcode
 * Plugin URI: http://blog.ppfeufer.de/wordpress-button-fuer-downloads-erzeugen/
 * Author: H.-Peter Pfeufer
 * Author URI: http://ppfeufer.de
 * Version: 1.2
 * Description: Add a shortcode to your wordpress for a nice downloadbutton. <code>&#91;dl url="" title="" desc=""&#93;</code>. Graphics made by: <a href="http://kkoepke.de">Kai Köpke</a>. If you made your own graphic for this button, feel free to write it in the comments under <a href="http://blog.ppfeufer.de/wordpress-button-fuer-downloads-erzeugen/">http://blog.ppfeufer.de/wordpress-button-fuer-downloads-erzeugen/</a>.
 */

if(!is_admin()) { // Nur wenn keine Adminseite
	/**
	 * CSS in Wordpress einbinden
	 */
	function download_button_css() {
		$image_url = plugins_url(basename(dirname(__FILE__)) . '/img/button.png');

		echo '
		<!-- Downloadbutton Shortcode -->
		<style type="text/css">
		#downloadbutton {width:304px; height:75px; background:url("' . $image_url . '") top right; text-align:center;}
		#downloadbutton:hover {background:url("' . $image_url . '") bottom right; color:#ffffff;}
		#downloadbutton a {width:100%; height:100%; display:block; text-decoration:none;}
		#downloadbutton a:hover {color:#ffffff;}
		#downloadbutton a span {font:normal 190%/130% "Trebuchet MS", Tahoma, Arial; color:#5f6970; display:block; padding:11px 0 0 0; width:100%}
		#downloadbutton a em {font:normal 110%/80% "Trebuchet MS", Tahoma, Arial; color:#5f6970; display:block; width:100%;}
		.dluttonright {float:right;}
		.dlbuttonleft {float:left;}
		.dlbuttoncenter {margin:0 auto;}
		.dlbutton-floatreset {clear:both;}
		</style>
		';
	}
	add_action('wp_head', 'download_button_css');

	/**
	 * Shortcode in HTML-Code umwandeln
	 * @param $atts
	 */
	function sc_downloadButton($atts) {
		extract(shortcode_atts(array(
			"url" => '',
			"title" => '',
			"desc" => '',
			"align" => ''
		), $atts));

		if ($align == '') {
			$align='center';
		}

		/**
		 * Auszugebendes HTML erstellen
		 * @var string
		 */
		$var_sHTML = '';
		$var_sHTML .= '<div id="downloadbutton" class="dlbutton' . $align . '">
							<a href="' . $url . '">
								<span>' . $title . '</span>
								<em>' . $desc . '</em>
							</a>
						</div>';

		/**
		 * Nur wenn gefloatet wird, einen Clearer einbauen
		 */
		if ($align == 'right' || $align == 'left') {
			$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
		}

		return $var_sHTML;
	}

	/**
	 * Shortcode zu Wordpress hinzufügen
	 */
	add_shortcode('dl', 'sc_downloadButton');
}

if(!function_exists('download_buttons_update_notice')) {
	function download_buttons_update_notice() {
		$array_DLBSC_Data = get_plugin_data(__FILE__);
		$var_sUserAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:5.0) Gecko/20100101 Firefox/5.0 WorPress Plugin Download Button Shortcode (Version: ' . $array_DLBSC_Data['Version'] . ') running on: ' . get_bloginfo('url');
		$url_readme = 'http://plugins.trac.wordpress.org/browser/download-button-shortcode/trunk/readme.txt?format=txt';
		$data = '';

		if(ini_get('allow_url_fopen')) {
			$data = file_get_contents($url_readme);
		} else {
			if(function_exists('curl_init')) {
				$cUrl_Channel = curl_init();
				curl_setopt($cUrl_Channel, CURLOPT_URL, $url_readme);
				curl_setopt($cUrl_Channel, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($cUrl_Channel, CURLOPT_USERAGENT, $var_sUserAgent);
				$data = curl_exec($cUrl_Channel);
				curl_close($cUrl_Channel);
			} // END if(function_exists('curl_init'))
		} // END if(ini_get('allow_url_fopen'))

		if($data) {
			$matches = null;
			$regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote($array_DLBSC_Data['Version']) . '\s*=|$)~Uis';

			if(preg_match($regexp, $data, $matches)) {
				$changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));

				echo '</div><div class="update-message" style="font-weight: normal;"><strong>What\'s new:</strong>';
				$ul = false;
				$version = 99;

				foreach($changelog as $index => $line) {
					if(version_compare($version, $array_DLBSC_Data['Version'], ">")) {
						if(preg_match('~^\s*\*\s*~', $line)) {
							if(!$ul) {
								echo '<ul style="list-style: disc; margin-left: 20px;">';
								$ul = true;
							} // END if(!$ul)

							$line = preg_replace('~^\s*\*\s*~', '', $line);
							echo '<li>' . $line . '</li>';
						} else {
							if($ul) {
								echo '</ul>';
								$ul = false;
							} // END if($ul)

							$version = trim($line, " =");
							echo '<p style="margin: 5px 0;">' . htmlspecialchars($line) . '</p>';
						} // END if(preg_match('~^\s*\*\s*~', $line))
					} // END if(version_compare($version, TWOCLICK_SOCIALMEDIA_BUTTONS_VERSION,">"))
				} // END foreach($changelog as $index => $line)

				if($ul) {
					echo '</ul><div style="clear: left;"></div>';
				} // END if($ul)


				echo '</div>';
			} // END if(preg_match($regexp, $data, $matches))
		} else {
			/**
			 * Returning if we can't use file_get_contents or cURL
			 */
			return;
		} // END if($data)
	} // END function download_buttons_update_notice()
} // END if(!function_exists('download_buttons_update_notice'))

/* Nur wenn User auch der Admin ist, sind die Adminoptionen zu sehen */
if(is_admin()) {
	// Updatemeldung
	if(ini_get('allow_url_fopen') || function_exists('curl_init')) {
		add_action('in_plugin_update_message-' . plugin_basename(__FILE__), 'download_buttons_update_notice');
	}
}
?>