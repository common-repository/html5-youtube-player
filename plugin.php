<?php
/*
Plugin Name: HTML5 Youtube Player
Plugin URI:  http://vargrid.com
Description: Embed Youtube with a HTML5 Player
Version:     1.0.1
Author:      Vargrid
Author URI:  http://vargrid.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: html5youtube
Domain Path: /languages
*/


require 'inc/embeder.php'; 
require 'inc/mp3.php';


function html5youtube_youtube_id_from_url($url) {
            $pattern = 
                '%^# Match any youtube URL
                (?:https?://)?  # Optional scheme. Either http or https
                (?:www\.)?      # Optional www subdomain
                (?:             # Group host alternatives
                  youtu\.be/    # Either youtu.be,
                | youtube\.com  # or youtube.com
                  (?:           # Group path alternatives
                    /embed/     # Either /embed/
                  | /v/         # or /v/
                  | /watch\?v=  # or /watch\?v=
                  )             # End path alternatives.
                )               # End host alternatives.
                ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
                $%x'
                ;
            $result = preg_match($pattern, $url, $matches);
            if ($result) {
                return $matches[1];
            }
            return false;
        }


function html5youtube_video_shortcode( $atts, $content = null ) {
    
    $a = shortcode_atts( array(
		'height' => '100%',
        'width' => '100%',
        'type' => 'video',
	), $atts );
    
    if($a['type'] == "video" || empty($a['width'])){
        
  
   $the_id =  html5youtube_youtube_id_from_url($content);
    $youtube = new Html5\Youtube\Youtube();
    $links = $youtube->getDownloadLinks($the_id);
    $mp4 = $links["MP4"];
    $video = '<video width="' . esc_attr($a['width']) . '" height="' . esc_attr($a['height']) . '" controls>';
    foreach($mp4 as $link){
         $video .= '<source src="'.$link.'" type="video/mp4">';
    }
    
     $video .= '</video>'; 
        
        return $video;
        
          }
    elseif($a['type'] == "audio") {
        $audio = '<audio controls>';
        $link = YoutubeToMP3::get($content, YoutubeToMP3::LINK);
        $audio .= '<source src="'.$link.'" type="audio/ogg">';
        $audio .= 'Your browser does not support the audio element.
</audio>';
        
        return $audio;
        
    }
    
	
}
add_shortcode( 'varvideo', 'html5youtube_video_shortcode' );