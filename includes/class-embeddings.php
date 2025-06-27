<?php

if (!defined('ABSPATH')) {
    exit;
}

class MII_Embeddings {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mii_embeddings';

        add_action('save_post', array($this, 'maybe_update_embedding'), 10, 2);
        add_action('delete_post', array($this, 'delete_embedding'));
    }

    public function maybe_update_embedding($post_id, $post) {
        // Vérifier si le post type est dans la liste des types à mettre à jour automatiquement
        $auto_update_post_types = get_option('mii_auto_update_post_types', array());

        if (!in_array($post->post_type, $auto_update_post_types)) {
            return;
        }

        // Vérifier si le post est publié
        if ($post->post_status !== 'publish') {
            return;
        }

        // Éviter les révisions et les auto-saves
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        // Générer l'embedding
        $this->generate_embedding($post_id);
    }

    public function delete_embedding($post_id) {
        global $wpdb;

        $wpdb->delete(
            $this->table_name,
            array('post_id' => $post_id),
            array('%d')
        );
    }

    public function generate_embedding($post_id) {
        $post = get_post($post_id);

        if (!$post) {
            return false;
        }

        // Extraire le contenu textuel
        $content = $this->extract_text_content($post);

        if (empty($content)) {
            return false;
        }

        // Appeler l'API pour générer l'embedding
        $api_client = new MII_API_Client();
        $embedding = $api_client->generate_embedding($content);

        if (!$embedding) {
            return false;
        }

        // Sauvegarder en base
        return $this->save_embedding($post_id, $post->post_type, $embedding);
    }

    private function extract_text_content($post) {
        // Titre + contenu
        $content = $post->post_title . ' ' . $post->post_content;

        // Nettoyer le HTML
        $content = wp_strip_all_tags($content);

        // Supprimer les shortcodes
        $content = strip_shortcodes($content);

        // Nettoyer les espaces multiples
        $content = preg_replace('/\s+/', ' ', $content);

        // Limiter la longueur (max 8000 tokens pour OpenAI)
        $content = substr(trim($content), 0, 32000);

        return $content;
    }

    private function save_embedding($post_id, $post_type, $embedding) {
        global $wpdb;

        $embedding_json = json_encode($embedding);

        $result = $wpdb->replace(
            $this->table_name,
            array(
                'post_id' => $post_id,
                'post_type' => $post_type,
                'embedding_vector' => $embedding_json,
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s')
        );

        return $result !== false;
    }

    public function get_embedding($post_id) {
        global $wpdb;

        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT embedding_vector FROM {$this->table_name} WHERE post_id = %d",
            $post_id
        ));

        if (!$result) {
            return null;
        }

        return json_decode($result, true);
    }

    public function get_all_embeddings($post_type = null) {
        global $wpdb;

        $where = '';
        if ($post_type) {
            $where = $wpdb->prepare(" WHERE post_type = %s", $post_type);
        }

        $results = $wpdb->get_results(
            "SELECT post_id, post_type, embedding_vector FROM {$this->table_name}{$where}"
        );

        $embeddings = array();
        foreach ($results as $result) {
            $embeddings[$result->post_id] = array(
                'post_type' => $result->post_type,
                'embedding' => json_decode($result->embedding_vector, true)
            );
        }

        return $embeddings;
    }

    public function bulk_generate_embeddings($post_types = array(), $force_regenerate = false) {
        $query_args = array(
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids'
        );

        $post_ids = get_posts($query_args);
        $total = count($post_ids);
        $processed = 0;
        $errors = array();

        foreach ($post_ids as $post_id) {
            // Vérifier si l'embedding existe déjà
            if (!$force_regenerate && $this->get_embedding($post_id)) {
                $processed++;
                continue;
            }

            $result = $this->generate_embedding($post_id);

            if ($result) {
                $processed++;
            } else {
                $errors[] = $post_id;
            }

            // Éviter les limites de taux d'API
            usleep(100000); // 0.1 seconde
        }

        return array(
            'total' => $total,
            'processed' => $processed,
            'errors' => $errors
        );
    }

    public function get_stats() {
        global $wpdb;

        $total_embeddings = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_name}"
        );

        $by_post_type = $wpdb->get_results(
            "SELECT post_type, COUNT(*) as count FROM {$this->table_name} GROUP BY post_type"
        );

        $recent_updates = $wpdb->get_results(
            "SELECT post_id, post_type, updated_at FROM {$this->table_name} ORDER BY updated_at DESC LIMIT 10"
        );

        return array(
            'total' => $total_embeddings,
            'by_post_type' => $by_post_type,
            'recent_updates' => $recent_updates
        );
    }

    public function clear_all_embeddings() {
        global $wpdb;

        return $wpdb->query("TRUNCATE TABLE {$this->table_name}");
    }

    public function clear_embeddings_by_post_type($post_type) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            array('post_type' => $post_type),
            array('%s')
        );
    }
}
?>