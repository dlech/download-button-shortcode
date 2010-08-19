<?php
/**
 * Plugin name: Download Button Shortcode
 * Plugin URI: http://blog.ppfeufer.de/wordpress-button-fuer-downloads-erzeugen/
 * Author: H.-Peter Pfeufer
 * Author URI: http://ppfeufer.de
 * Version: 1.1.1
 * Description: Add a shortcode to your wordpress for a nice downloadbutton. <code>&#91;dl url="" title="" desc=""&#93;</code>. Graphics made by: <a href="http://kkoepke.de">Kai Köpke</a>. If you made your own graphic for this button, feel free to write it in the commants under <a href="http://blog.ppfeufer.de/wordpress-button-fuer-downloads-erzeugen/">http://blog.ppfeufer.de/wordpress-button-fuer-downloads-erzeugen/</a>.
 */


/**
 * Changelog:
 *		= 1.1.1 =
 *		Renamed to Download Button Shortcode
 *		Upload to Wordpress Plugin Directory
 *
 *		= 1.1.0 =
 *		Added Shortcodeoption for Buttonposition <code>&#91;dl url="" title="" desc="" align=""&#93;</code>. This ist optional. If not set, the button will appear centered. Possible options are "center", "left" and "right"
 *
 *		= 1.0.0 =
 *		Initial Release
 */


/**
 * Known Issues:
 * 		If you use WP-Minify, you have to exclude downloadbutton.css from minify. This is for Webkit based browsers.
 */

if(!is_admin()) { // Nur wenn keine Adminseite
	define('DOWNLOADBUTTON_VERSION', '1.1.1');

	$css_url = plugins_url(basename(dirname(__FILE__)) . '/css/downloadbutton.css');

	/**
	 * CSS in Wordpress einbinden
	 */
	wp_register_style('downloadbutton-for-wordpress', $css_url, array(), DOWNLOADBUTTON_VERSION, 'screen');
	wp_enqueue_style('downloadbutton-for-wordpress');

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
?>