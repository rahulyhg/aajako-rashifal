<?php
/*
Plugin Name: &#2310;&#2332;&#2325;&#2379; &#2352;&#2366;&#2358;&#2367;&#2347;&#2354;
Plugin URI: http://aajako.com/rashifal/
Description: &#2310;&#2332;&#2325;&#2379; &#2352;&#2366;&#2358;&#2367;&#2347;&#2354;&#2325;&#2379; &#2347;&#2368;&#2337; &#2340;&#2346;&#2366;&#2312;&#2325;&#2379; &#2360;&#2366;&#2311;&#2337;&#2348;&#2366;&#2352;&#2350;&#2366;
Author: Aajako
Author URI: https://profiles.wordpress.org/aajako
Version: 1.3
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

define('AJ_RASHIFAL_VER','1.3');

if(!class_exists('Aj_Rashifal'))
{
  
  class Aj_Rashifal
  {
    private $plugin_url;
    
    public function __construct()
    {
      add_action( 'widgets_init',         array($this,'register_rashifal_widget'));
      add_action( 'wp_enqueue_scripts',   array($this,'rashifal_assests') );
      register_activation_hook( __FILE__, array($this,'Aj_activate') );
      add_action( 'wp_ajax_aj_rashifal',    array($this,'ajax') );
      add_action( 'wp_ajax_aj_rashifal',    array($this,'ajax') );
      
      $this->plugin_url                   = plugins_url('/',__FILE__);
    }
    
    public function register_rashifal_widget()
    {
      include 'rashifal-widget.php';
      register_widget( 'Aj_Rashifal_Widget' );
    }
    
    public function rashifal_assests()
    {
      wp_enqueue_script('jquery');
      wp_enqueue_style( 'rashifal-style', $this->plugin_url . 'style.css' );
    }
    
    public function Aj_activate(){}
    
    public function ajax()
    {
      $site_url                           = 'http://api.aajako.com/rashifal/?type=json&wp_ver='.AJ_RASHIFAL_VER;
      $ch                                 = curl_init();
      header('Content-Type: application/json');
      curl_setopt($ch, CURLOPT_URL, $site_url);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
      curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST'].urldecode($_POST['_c']).'#wp_ver='.AJ_RASHIFAL_VER);
      curl_exec($ch);
      curl_close($ch);
      die;  
    }

  }
  
  new Aj_Rashifal;
  
}
