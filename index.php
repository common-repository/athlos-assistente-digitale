<?php
 
/*
 
Plugin Name: Athlos Assistente Digitale
 
Description: Plugin per la connessione dell'assistente virtuale e inserimento nel footer
 
Version: 2.1
 
Author: Athlos
 
Author URI: https://www.athlos.biz
 
License: GPLv2 or later
 
Text Domain: athlos
 
*/

function athlos_install() {
  global $wpdb;
  $table_name = $wpdb->prefix . "assistente_athlos";
  $charset_collate = $wpdb->get_charset_collate();
  $sql = "CREATE TABLE IF NOT EXISTS $table_name 
  (id bigint(20) NOT NULL AUTO_INCREMENT,
    url text DEFAULT '' NOT NULL,
    PRIMARY KEY id (id)
  ) 
  $charset_collate;";
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);  
  
  
  
}    
add_action('init', 'athlos_install');




class Athlos_Plugin {
  function __construct() {
    // Hook into the admin menu
    add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
  }
  
  public function create_plugin_settings_page() {
    // Add the menu item and page
    $page_title = 'Athlos Assistente Digitale';
    $menu_title = 'Athlos Assistente Digitale';
    $capability = 'manage_options';
    $slug = 'athlos_fields';
    $callback = array( $this, 'plugin_settings_page_content' );
    $icon = 'dashicons-admin-plugins';
    $position = 100;
  
    add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
  }
  
  public function plugin_settings_page_content() {
    if (isset($_POST['submit'])) {
      global $wpdb;
      $table = $wpdb->prefix.'assistente_athlos';
      $data = array('url' => sanitize_url( $_POST['assistente_id']));
      $wpdb->insert($table,$data);
  }
//End isset
    global $wpdb;
    $last_url = $wpdb->get_var('SELECT url FROM '.$wpdb->prefix.'assistente_athlos ORDER BY id DESC LIMIT 1');
    $last_url_visual = str_replace("http://", "", $last_url);
    echo "<div class='row' style='display: flex;'>";
    echo "<div class='column' style='flex: 50%;'>";
      echo "<div id='logo' style='padding-top:20px'><img src='".plugin_dir_url( __FILE__ ) ."img/logo_viola.png'></div>";
      
        echo "<h3>Inserisci l'url ricevuto tramite mail</h3>";
        echo"<form action='' method='POST'>";
          printf('<input id="assistente_id" name="assistente_id" type="text" class="regular-text" value="'.
          ($last_url_visual).'"');
          submit_button();
        echo"</form>";

        echo"<h5>Se vuoi testare l'assistente inserisce l'url</h5>";
        echo"<h3>https://sdkathlos.it/avatar/freedemo/js_wordpress/sdk_main.js</h3>";
        echo"<h5>Visita il nostro sito <a href='https://www.athlos.biz' target='_blank'>Athlos.biz</a> per ottenere il tuo codice di attivazione</h5>";
      echo"</div>";
      echo "<div class='column' style='flex: 50%;'>";
        echo"<video autoplay muted loop id='closed_close'>";
        echo"<source src='".plugin_dir_url( __FILE__ ) ."img/close.webm' type='video/webm' >";
        echo"</video>";
      echo"</div>";
    "</div>";
  }
}
new Athlos_Plugin();

function athlos_footer() {
  global $wpdb;
  $last_url = $wpdb->get_var('SELECT url FROM '.$wpdb->prefix.'assistente_athlos ORDER BY id DESC LIMIT 1');
  $last_url = str_replace("http://", "", $last_url);
  if ($last_url === ''){
    echo "";
  }
  else {
    wp_enqueue_script( 'avatar_js',esc_url($last_url), false );
    
  }
}
add_action( 'wp_footer', 'athlos_footer' );