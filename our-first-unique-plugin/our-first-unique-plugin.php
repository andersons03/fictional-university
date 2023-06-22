<?php

/*
  Plugin Name: Our Test Plugin
  Description: A truly amazing plugin.
  Version: 1.0
  Author: Brad
  Author URI: https://www.udemy.com/user/bradschiff/
  Text Domain: wcpdomain
  Domain Path: /languages

*/

// add_filter( "the_content", 'addToEndOfPost');

// function addToEndOfPost($content){
//   if(is_page() and is_main_query()){
//     return "$content <p>Olá, eu sou o goku</p>";
//   }

//   return $content;
// }

/*

  Método simples, para criação de paginas, porém, muito mais trabalhoso para manter.

  add_action('admin_menu', 'ourPluginSettingLnk');

  function ourPluginSettingLnk(){
    add_options_page( "Opções do contador de palavra", "Contador de palavras", "manage_options", "pagina-configuracao-conta-palavras", "ourSettingsPageHTML");
  }

  function ourSettingsPageHTML(){
    ?>
    <h1>Olá fellas</h1>
    <?php
  }
*/

class PluginContadordDePalavraETempo{
  function __construct() {
    add_action('admin_menu', [$this, 'adminPage']);
    add_action("admin_init", [$this, 'settings']);
    add_filter( "the_content", [$this, "ifWrap"] );
    add_action( "init", [$this, "languages"]);
  }

  function languages(){
    load_plugin_textdomain( "wcpdomain", false, dirname(plugin_basename(__FILE__)) . "/languages" );
  }

  function adminPage(){
    add_options_page( "Opções do contador de palavra", __("Contador de palavras", "wcpdomain"), "manage_options", "pagina-configuracao-conta-palavras", [$this, "ourHTML"]);
  }

  function settings(){
    add_settings_section("wcp_first_section", null, null, "pagina-configuracao-conta-palavras");

    add_settings_field( "wcp_location", "Localização", [$this, "locationHTML"], "pagina-configuracao-conta-palavras", "wcp_first_section");
    register_setting("wordcountgroupplugin", "wcp_location", ["sanitize_callback" => [$this, "sanitize_location"], "default" => "0"] );

    add_settings_field( "wcp_headline", "Titulo", [$this, "headlineHTML"], "pagina-configuracao-conta-palavras", "wcp_first_section");
    register_setting("wordcountgroupplugin", "wcp_headline", array('sanitize_callback' => 'sanitize_text_field', 'default' => 'post statistic'));

    add_settings_field( "wcp_wordcount", "Contador de palavras", [$this, "createCheckboxHTML"], "pagina-configuracao-conta-palavras", "wcp_first_section", ['field' => "wcp_wordcount"]);
    register_setting("wordcountgroupplugin", "wcp_wordcount", array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));

    add_settings_field( "wcp_charactercount", "Contador de charactere", [$this, "createCheckboxHTML"], "pagina-configuracao-conta-palavras", "wcp_first_section", ['field' => "wcp_charactercount"]);
    register_setting("wordcountgroupplugin", "wcp_charactercount", array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));

    add_settings_field( "wcp_readtime", "Tempo de leitura", [$this, "createCheckboxHTML"], "pagina-configuracao-conta-palavras", "wcp_first_section", ['field' => "wcp_readtime"]);
    register_setting("wordcountgroupplugin", "wcp_readtime", array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
  }

  function ifWrap($content){
    if(
      is_main_query() and 
      is_single() and 
      (
        get_option( "wcp_wordcount", "1" ) or
        get_option( "wcp_charactercount", "1" ) or
        get_option( "wcp_readtime", "1" )
      )
    ){
      return $this->createHTML($content);
    }

    return $content;
  }

  function createHTML($content){
    $title = esc_html(get_option( "wcp_headline", "Informação do post"));
    $stripedTags = strip_tags($content);

    $html = "<h3>$title</h3><p>";

    if (get_option( "wcp_wordcount", 'on' ) == "on" or get_option( "wcp_readtime", 'on' ) == "on") {
      $wordCount = str_word_count($stripedTags);
    }

    if (get_option( "wcp_wordcount", 'on' ) == "on") {
      $html .= esc_html__("Este post contem", "wcpdomain") . " " . $wordCount . " " . esc_html__("palavras", "wcpdomain") ."<br>";
    }

    if (get_option( "wcp_charactercount", 'on' ) == "on") {
      $chracterCount = strlen($stripedTags);
      $html .= esc_html__("Este post contem", "wcpdomain") . " " . $chracterCount . " " . esc_html__("letras", "wcpdomain") ."<br>";
    }

    if (get_option( "wcp_readtime", 'on' ) == "on") {
      $readTime = round($wordCount/225);
      $html .= esc_html__("Tempo de leitura:", "wcpdomain") . " " . $chracterCount . " " . esc_html__("minuto(s)", "wcpdomain") ."<br>";
    }

    $html .= "</p>";

    if (get_option( "wcp_location", '1' ) == "1") {
      return $content . $html;
    }

    return $html . $content;
  }

  // HTML dos campos que vão no setting field
  function createCheckboxHTML($args){
    ?>
    <input type="checkbox" name="<?= $args['field'] ?>" <?php checked(get_option($args['field']), "on") ?>>
    <?php
  }

  function locationHTML(){
    ?>
    <select name="wcp_location">
      <option value="0" <?php selected( get_option("wcp_location"), 0) ?>>Começo do post</option>
      <option value="1" <?php selected( get_option("wcp_location"), 1) ?>>Final do post</option>
    </select>
    <?php
  }

  function sanitize_location($input){
    if($input != "0" AND $input != "1"){
      // var_dump($field != 0);
      add_settings_error( "wcp_location", "wcp_location_error", "O valor deve ser entre 0 e 1");
      return get_option("wcp_location");
    }

    return $input;
  }

  function headlineHTML(){
    ?>
    <input type="text" name="wcp_headline" value="<?= esc_attr(get_option( "wcp_headline" )); ?>">
    <?php
  }

  // HTML principal da pagina
  function ourHTML(){
    ?>
    <div class="wrap">
      <h1>Contador de palavras</h1>
      <form action="options.php" method="POST">
        <?php 
          settings_fields("wordcountgroupplugin");
          do_settings_sections("pagina-configuracao-conta-palavras"); 
          submit_button();
        ?>
      </form>
    </div>
    <?php
  }
}

$pluginContadordDePalavraETempo = new PluginContadordDePalavraETempo();