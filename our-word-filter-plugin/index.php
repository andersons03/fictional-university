<?php
/*
    Plugin Name: Filtro de palavras
    Description: Muda uma lista de palavras
    Version: 1.0
    Author: Anderson
    Author URI: https://www.udemy.com/user/bradschiff/
*/

class ourWordFilterPlugin{

    function __construct() {
        add_action( "admin_menu", [$this, "ourMenu"]);
    }

    function ourMenu(){
        $mainPageHook = add_menu_page("Filtrar palavras", "Filtra palavras", "manage_options", "ourFilterWordPluginMenu", [$this, "wordFilterPage"], "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAyMEMxNS41MjI5IDIwIDIwIDE1LjUyMjkgMjAgMTBDMjAgNC40NzcxNCAxNS41MjI5IDAgMTAgMEM0LjQ3NzE0IDAgMCA0LjQ3NzE0IDAgMTBDMCAxNS41MjI5IDQuNDc3MTQgMjAgMTAgMjBaTTExLjk5IDcuNDQ2NjZMMTAuMDc4MSAxLjU2MjVMOC4xNjYyNiA3LjQ0NjY2SDEuOTc5MjhMNi45ODQ2NSAxMS4wODMzTDUuMDcyNzUgMTYuOTY3NEwxMC4wNzgxIDEzLjMzMDhMMTUuMDgzNSAxNi45Njc0TDEzLjE3MTYgMTEuMDgzM0wxOC4xNzcgNy40NDY2NkgxMS45OVoiIGZpbGw9IiNGRkRGOEQiLz4KPC9zdmc+", 100);
        add_submenu_page( "ourFilterWordPluginMenu", "Lista de palavras", "Lista palavras", "manage_options", "ourFilterWordPluginMenu", [$this, "wordFilterPage"]);
        add_submenu_page( "ourFilterWordPluginMenu", "Configurações", "Configurações", "manage_options", "ourFilterOptions", [$this, "optionSubMenu"]);
        add_action("load-{$mainPageHook}", [$this, "mainPageAssets"]);
    }

    function mainPageAssets(){
        wp_enqueue_style( "filterAdminCss", plugin_dir_url(__FILE__) . "styles.css");
    }

    function handleForm(){ 
        if(wp_verify_nonce($_POST["ourNonce"], "wordFilterPluginAction")):
            update_option( "plugin_words_to_filter", sanitize_textarea_field( $_POST['plugin_words_to_filter'] ));
    ?>
        <div class="updated">
            <p>Tudo certo irmão</p>
        </div>
    <? else: ?>
        <div class="error">
            <p>Não foi possivel salvar</p>
        </div>
    <?php
        endif;
    }

    function wordFilterPage(){

        ?>
        <div class="wrap">
            <h1>Filtro de palavras</h1>
            <?php if($_POST['saveForm'] == "true") $this->handleForm(); ?>
            <form method="post">
                <?php wp_nonce_field("wordFilterPluginAction", "ourNonce"); ?>
                <input type="hidden" name="saveForm" value="true">
                <label for="plugin_words_to_filter"><p>Insira uma lista de palavras separadas por virgula, que será filtrada do conteúdo</p></label>
                <div class="word-filter__flex-container">
                    <textarea name="plugin_words_to_filter" id="plugin_words_to_filter" placeholder="feio, chato, grosso, pnc"><?= esc_textarea( get_option( "plugin_words_to_filter") ) ; ?></textarea>
                </div>
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Salvar">
            </form>
        </div>
        <?php
    }

    function optionSubMenu(){
        ?>

        <?php
    }

}

$ourWordFilterPlugin = new ourWordFilterPlugin();