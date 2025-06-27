<?php
/*
Plugin Name: Maillage Interne Intelligent  
Plugin URI: https://www.effi10.com/plugin-worpress-maillage-interne/
Description: Plugin WordPress Gutenberg pour automatiser et améliorer le maillage interne avec deux blocs personnalisés et gestion des embeddings IA.
Version: 1.0.0
Author: Cédric GIRARD
Author URI: https://www.effi10.com
License: GPL v2 or later
Text Domain: maillage-interne-intelligente
Domain Path: /languages
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Network: false
*/

// Sécurité : Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Définir les constantes du plugin
define('MII_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MII_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MII_PLUGIN_VERSION', '1.0.0');
define('MII_TEXT_DOMAIN', 'maillage-interne-intelligente');

// Classe principale du plugin
class MaillageInterneIntelligente {

    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function init() {
        // Charger les classes
        $this->load_dependencies();

        // Initialiser les composants
        new MII_Admin();
        new MII_Blocks();
        new MII_Embeddings();
        new MII_Ajax();
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            MII_TEXT_DOMAIN,
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    private function load_dependencies() {
        require_once MII_PLUGIN_PATH . 'includes/class-admin.php';
        require_once MII_PLUGIN_PATH . 'includes/class-blocks.php';
        require_once MII_PLUGIN_PATH . 'includes/class-embeddings.php';
        require_once MII_PLUGIN_PATH . 'includes/class-ajax.php';
        require_once MII_PLUGIN_PATH . 'includes/class-similarity.php';
        require_once MII_PLUGIN_PATH . 'includes/class-api-client.php';
    }

    public function activate() {
        // Créer les tables nécessaires
        $this->create_tables();

        // Définir les options par défaut
        add_option('mii_default_elements', 3);
        add_option('mii_default_image_size', 'medium');
        add_option('mii_default_image_ratio', '16:9');
        add_option('mii_default_title_tag', 'h3');
        add_option('mii_default_columns', 2);
        add_option('mii_responsive_breakpoint', 768);
        add_option('mii_embedding_provider', 'openai');
        add_option('mii_openai_model', 'text-embedding-3-small');
        add_option('mii_auto_update_post_types', array('post', 'page'));
    }

    public function deactivate() {
        // Nettoyage si nécessaire
    }

    private function create_tables() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'mii_embeddings';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            post_type varchar(20) NOT NULL,
            embedding_vector longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY post_id (post_id),
            KEY post_type (post_type)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Initialiser le plugin
new MaillageInterneIntelligente();
?>