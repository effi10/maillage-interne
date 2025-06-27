<?php
// Sécurité : empêcher l'accès direct.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistre les blocs Gutenberg via leur block.json
 */
function mii_register_blocks_with_json() {
    // Chemins vers les dossiers des blocs (à adapter si nécessaire)
    register_block_type(__DIR__ . '/../assets/blocks/pages-associees');
    register_block_type(__DIR__ . '/../assets/blocks/publications-dynamiques');
}
add_action('init', 'mii_register_blocks_with_json');