<?php
/*
   Plugin Name: Cookie Nonsense for YT
   Plugin URI: http://www.staub-berlin.de
   Version: 1.2.0
   Author: Christian Ladewig
   Description: Embed Videos (Youtube or Vimeo) in WPBakery with GDPR requirements
*/

	function ycn_init(){
		if(function_exists('vc_map')){
			vc_map(
				array(
					'name' => 'Youtube Video (GDPR)',
					'base' => 'ycn',
					'description' => 'Embed YT-Videos with GDPR in mind ',
					'category' => 'Advanced',
					'icon' => 'icon-wpb-video',
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => 'Video URL / Youtube Video ID',
							'param_name' => 'ycn_ytvid',
							'description' => 'Enter a Video URL from Youtube or Vimeo. You can still enter just a Youtube Video ID.',
							'value' => ''
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Video ratio',
							'param_name' => 'ycn_ratio',
							'value' => array(
								'16:9'   => '16_9',
								'4:3'   => '4_3',
								'21:9'   => '21_9',
								'5:4'   => '5_4',
							)
						),
						array(
							'type' => 'textfield',
							'heading' => 'Video max width',
							'param_name' => 'ycn_maxwidth',
							'value' => '',
							'description' => 'Max width for container'
						),
						array(
							'type' => 'textarea_raw_html',
							'heading' => 'Custom placeholder text',
							'param_name' => 'ycn_custom_preview_text',
							'description' => 'Replaces the standard html text with your own. please keep in mind that you need a button or link with ycn-btn class for the plugin to work.',
							'value' => ''
						),
						array(
							'type' => 'textfield',
							'heading' => 'CSS Class',
							'param_name' => 'ycn_class',
							'value' => ''
						),
					)
				)
			);
		}
	}

	function ycn_shortcode($attr){
		extract(shortcode_atts(array(
				'ycn_ytvid' => '',
				'ycn_ratio' => '',
		    'ycn_maxwidth' => '',
				'ycn_custom_preview_text' => '',
		    'ycn_class' => '',
		    'uid' => uniqid()
		), $attr));

		require("lib/VideoUrlParser.class.php");

		wp_enqueue_script('ycn-js', plugins_url('ycn.jquery.js',__FILE__), false, "1.1", true);

		$ycn_custom_preview_text = urldecode(base64_decode($ycn_custom_preview_text));

		if($ycn_ratio!="16_9"){
			switch ($ycn_ratio) {
				case '4_3':
					$ycn_ratio = round((100/(4/3)),2);
					break;
				case '5_4':
					$ycn_ratio = round((100/(5/4)),2);
					break;
				case '21_9':
					$ycn_ratio = round((100/(21/9)),2);
					break;
				default:
					$ycn_ratio = "";
					break;
			}
		}
		$url = $ycn_ytvid;

		$url = filter_var($url, FILTER_SANITIZE_URL);
		if(filter_var($url, FILTER_VALIDATE_URL) !== false){
			$service =  VideoUrlParser::identify_service($url);
			$embed_url = VideoUrlParser::get_url_embed($url);
			$url = $embed_url;
		} else {
			$url = "https://youtube.com/embed/".$ycn_ytvid."?autoplay=1";
			$service = "youtube";
		}

		if($service=="youtube"){
			$text = '<h2>YouTube Player noch nicht geladen.</h2><p>Um das Video anzuzeigen klicken Sie bitte auf &bdquo;Ich stimme zu&ldquo;.</p><p class="xs">Mit dem Laden des Videos stimmen Sie den <a href="https://policies.google.com/privacy?hl=de" target="_blank">Datenschutzbestimmungen von YouTube</a> zu.</p>';
		} else {
			$text = '<h2>Vimeo Player noch nicht geladen.</h2><p>Um das Video anzuzeigen klicken Sie bitte auf &bdquo;Ich stimme zu&ldquo;.</p><p class="xs">Mit dem Laden des Videos stimmen Sie den <a href="https://vimeo.com/privacy" target="_blank">Datenschutzbestimmungen von Vimeo</a> zu.</p>';
		}

		$ycn_custom_preview_text = urldecode(base64_decode($ycn_custom_preview_text));

		$output = "\n";
		$output.= '<div id="ycn-'.$uid.'" class="yt-cookie-nonsense-container '.$ycn_class.'" style="'.($ycn_maxwidth!=""?"max-width:".$ycn_maxwidth.";":"").'">'."\n";
		$output.= '<div class="yt-cookie-nonsense" data-videoid="'.$url.'" style="'.($ycn_ratio!=""?"padding-bottom:".$ycn_ratio."%;":"").'">'."\n";
		$output.= "\t".'<div class="ycn-video-preview">'."\n";
		if($ycn_custom_preview_text!=""){
			$output.= "\t\t\t".''.$ycn_custom_preview_text.''."\n";
		} else {
			$output.= "\t\t".'<div class="ycn-preview-text">'."\n";
			$output.= "\t\t".'<div class="ycn-preview-text-content">'."\n";
			$output.= $text;
			$output.= "\t\t".'</div>'."\n";
			$output.= "\t\t\t".'<p><a href="#" class="ycn-btn">Ich stimme zu</a>'."\n";
			$output.= "\t\t".'</div>'."\n";
		}
		$output.= "\t\t".'<!-- perhaps image here -->'."\n";
		$output.= "\t".'</div>'."\n";
		$output.= "\t".'<iframe class="ycn-iframe" src="about:blank" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>'."\n";
		$output.= '</div>'."\n";
		$output.= '</div>'."\n";
		return $output;
	}

add_action( 'wp_print_styles', 'ycn_cssfunc', 10 );
function ycn_cssfunc(){
  wp_enqueue_style('ycn-css', plugins_url('ycn.css',__FILE__));
}

	add_shortcode('ycn','ycn_shortcode');
	add_action('vc_before_init','ycn_init');
