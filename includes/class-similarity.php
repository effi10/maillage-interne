<?php

if (!defined('ABSPATH')) {
    exit;
}

class MII_Similarity {

    private $embeddings;

    public function __construct() {
        $this->embeddings = new MII_Embeddings();
    }

    public function get_similar_posts($post_id, $post_type = 'post', $limit = 5) {
        // Obtenir l'embedding du post actuel
        $current_embedding = $this->embeddings->get_embedding($post_id);

        if (!$current_embedding) {
            return array();
        }

        // Obtenir tous les embeddings du même post type
        $all_embeddings = $this->embeddings->get_all_embeddings($post_type);

        // Retirer le post actuel
        unset($all_embeddings[$post_id]);

        if (empty($all_embeddings)) {
            return array();
        }

        // Calculer les similarités
        $similarities = array();

        foreach ($all_embeddings as $other_post_id => $data) {
            $similarity = $this->cosine_similarity($current_embedding, $data['embedding']);

            if ($similarity > 0) {
                $similarities[$other_post_id] = $similarity;
            }
        }

        // Trier par similarité décroissante
        arsort($similarities);

        // Limiter les résultats
        $similar_post_ids = array_slice(array_keys($similarities), 0, $limit);

        // Récupérer les objets WP_Post
        $posts = array();
        foreach ($similar_post_ids as $similar_post_id) {
            $post = get_post($similar_post_id);
            if ($post && $post->post_status === 'publish') {
                $posts[] = $post;
            }
        }

        return $posts;
    }

    public function cosine_similarity($vector_a, $vector_b) {
        if (count($vector_a) !== count($vector_b)) {
            return 0;
        }

        $dot_product = 0;
        $magnitude_a = 0;
        $magnitude_b = 0;

        for ($i = 0; $i < count($vector_a); $i++) {
            $dot_product += $vector_a[$i] * $vector_b[$i];
            $magnitude_a += $vector_a[$i] * $vector_a[$i];
            $magnitude_b += $vector_b[$i] * $vector_b[$i];
        }

        $magnitude_a = sqrt($magnitude_a);
        $magnitude_b = sqrt($magnitude_b);

        if ($magnitude_a == 0 || $magnitude_b == 0) {
            return 0;
        }

        return $dot_product / ($magnitude_a * $magnitude_b);
    }

    public function find_similar_content($content, $post_type = 'post', $limit = 5) {
        // Générer l'embedding pour le contenu fourni
        $api_client = new MII_API_Client();
        $content_embedding = $api_client->generate_embedding($content);

        if (!$content_embedding) {
            return array();
        }

        // Obtenir tous les embeddings du post type
        $all_embeddings = $this->embeddings->get_all_embeddings($post_type);

        if (empty($all_embeddings)) {
            return array();
        }

        // Calculer les similarités
        $similarities = array();

        foreach ($all_embeddings as $post_id => $data) {
            $similarity = $this->cosine_similarity($content_embedding, $data['embedding']);

            if ($similarity > 0) {
                $similarities[$post_id] = $similarity;
            }
        }

        // Trier par similarité décroissante
        arsort($similarities);

        // Limiter les résultats
        $similar_post_ids = array_slice(array_keys($similarities), 0, $limit);

        // Récupérer les objets WP_Post avec leurs scores
        $posts = array();
        foreach ($similar_post_ids as $post_id) {
            $post = get_post($post_id);
            if ($post && $post->post_status === 'publish') {
                $post->similarity_score = $similarities[$post_id];
                $posts[] = $post;
            }
        }

        return $posts;
    }

    public function get_similarity_matrix($post_ids) {
        $embeddings_data = array();

        // Récupérer tous les embeddings
        foreach ($post_ids as $post_id) {
            $embedding = $this->embeddings->get_embedding($post_id);
            if ($embedding) {
                $embeddings_data[$post_id] = $embedding;
            }
        }

        $matrix = array();

        // Calculer toutes les similarités
        foreach ($embeddings_data as $post_id_a => $embedding_a) {
            $matrix[$post_id_a] = array();

            foreach ($embeddings_data as $post_id_b => $embedding_b) {
                if ($post_id_a === $post_id_b) {
                    $matrix[$post_id_a][$post_id_b] = 1.0;
                } else {
                    $similarity = $this->cosine_similarity($embedding_a, $embedding_b);
                    $matrix[$post_id_a][$post_id_b] = $similarity;
                }
            }
        }

        return $matrix;
    }

    public function get_content_clusters($post_type = 'post', $threshold = 0.7) {
        $all_embeddings = $this->embeddings->get_all_embeddings($post_type);

        if (empty($all_embeddings)) {
            return array();
        }

        $post_ids = array_keys($all_embeddings);
        $clusters = array();
        $processed = array();

        foreach ($post_ids as $post_id) {
            if (in_array($post_id, $processed)) {
                continue;
            }

            $cluster = array($post_id);
            $current_embedding = $all_embeddings[$post_id]['embedding'];

            foreach ($post_ids as $other_post_id) {
                if ($post_id === $other_post_id || in_array($other_post_id, $processed)) {
                    continue;
                }

                $similarity = $this->cosine_similarity(
                    $current_embedding,
                    $all_embeddings[$other_post_id]['embedding']
                );

                if ($similarity >= $threshold) {
                    $cluster[] = $other_post_id;
                    $processed[] = $other_post_id;
                }
            }

            if (count($cluster) > 1) {
                $clusters[] = $cluster;
            }

            $processed[] = $post_id;
        }

        return $clusters;
    }
}
?>