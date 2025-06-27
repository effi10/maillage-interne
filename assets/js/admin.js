(function($) {
    'use strict';

    // Initialisation
    $(document).ready(function() {
        initTabs();
        initProviderSwitch();
        initEventHandlers();
    });

    // Gestion des onglets
    function initTabs() {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();

            var target = $(this).attr('href');

            // Activer l'onglet
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            // Afficher le contenu
            $('.tab-content').hide();
            $(target).show();
        });
    }

    // Gestion du changement de fournisseur
    function initProviderSwitch() {
        $('input[name="provider"]').on('change', function() {
            var provider = $(this).val();

            $('.provider-config').hide();
            $('#' + provider + '-config').show();
        });
    }

    // Gestionnaires d'événements
    function initEventHandlers() {
        // Test de connexion API
        $('#test-api-connection').on('click', testApiConnection);

        // Sauvegarde des paramètres API
        $('#mii-api-form').on('submit', saveApiSettings);

        // Génération des embeddings
        $('#mii-generate-form').on('submit', generateEmbeddings);

        // Suppression des embeddings
        $('#clear-embeddings').on('click', clearEmbeddings);

        // Actualisation des statistiques
        $('#refresh-stats').on('click', refreshStats);
    }

    // Test de connexion API
    function testApiConnection() {
        var provider = $('input[name="provider"]:checked').val();
        var apiKey = $('#' + provider + '_api_key').val();

        if (!apiKey) {
            showNotice('error', miiAjax.strings.error + ': ' + 'Clé API manquante');
            return;
        }

        var button = $(this);
        button.prop('disabled', true).text('Test en cours...');

        $.ajax({
            url: miiAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'mii_test_api_connection',
                nonce: miiAjax.nonce,
                provider: provider,
                api_key: apiKey
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', 'Connexion réussie avec ' + response.data.provider);
                } else {
                    showNotice('error', 'Erreur de connexion: ' + response.data.message);
                }
            },
            error: function() {
                showNotice('error', 'Erreur de communication avec le serveur');
            },
            complete: function() {
                button.prop('disabled', false).text('Tester la connexion');
            }
        });
    }

    // Sauvegarde des paramètres API
    function saveApiSettings(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=mii_save_api_settings';

        $.ajax({
            url: miiAjax.ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data);
                } else {
                    showNotice('error', response.data);
                }
            },
            error: function() {
                showNotice('error', 'Erreur de communication avec le serveur');
            }
        });
    }

    // Génération des embeddings
    function generateEmbeddings(e) {
        e.preventDefault();

        var postTypes = [];
        $('input[name="post_types[]"]:checked').each(function() {
            postTypes.push($(this).val());
        });

        if (postTypes.length === 0) {
            showNotice('error', 'Veuillez sélectionner au moins un type de contenu');
            return;
        }

        var forceRegenerate = $('#force_regenerate').is(':checked');

        showProgress(true);
        updateProgress(0, 'Démarrage du traitement...');

        $.ajax({
            url: miiAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'mii_generate_embeddings',
                nonce: miiAjax.nonce,
                post_types: postTypes,
                force_regenerate: forceRegenerate
            },
            success: function(response) {
                if (response.success) {
                    updateProgress(100, response.data.message);
                    showNotice('success', response.data.message);
                } else {
                    showNotice('error', response.data);
                }
            },
            error: function() {
                showNotice('error', 'Erreur lors de la génération des embeddings');
            },
            complete: function() {
                setTimeout(function() {
                    showProgress(false);
                }, 2000);
            }
        });
    }

    // Suppression des embeddings
    function clearEmbeddings() {
        if (!confirm(miiAjax.strings.confirm_delete)) {
            return;
        }

        $.ajax({
            url: miiAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'mii_clear_embeddings',
                nonce: miiAjax.nonce,
                post_type: 'all'
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data);
                    refreshStats();
                } else {
                    showNotice('error', response.data);
                }
            },
            error: function() {
                showNotice('error', 'Erreur lors de la suppression');
            }
        });
    }

    // Actualisation des statistiques
    function refreshStats() {
        $('#stats-content').html('<p>Chargement des statistiques...</p>');

        $.ajax({
            url: miiAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'mii_get_embedding_stats',
                nonce: miiAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayStats(response.data);
                } else {
                    $('#stats-content').html('<p>Erreur lors du chargement des statistiques</p>');
                }
            },
            error: function() {
                $('#stats-content').html('<p>Erreur de communication avec le serveur</p>');
            }
        });
    }

    // Affichage des statistiques
    function displayStats(stats) {
        var html = '<div class="stats-grid">';

        html += '<div class="stats-card">';
        html += '<div class="stats-number">' + stats.total + '</div>';
        html += '<div>Total des embeddings</div>';
        html += '</div>';

        if (stats.by_post_type) {
            stats.by_post_type.forEach(function(item) {
                html += '<div class="stats-card">';
                html += '<div class="stats-number">' + item.count + '</div>';
                html += '<div>' + item.post_type + '</div>';
                html += '</div>';
            });
        }

        html += '</div>';

        if (stats.recent_updates && stats.recent_updates.length > 0) {
            html += '<h3>Dernières mises à jour</h3>';
            html += '<table class="widefat">';
            html += '<thead><tr><th>Post ID</th><th>Type</th><th>Date</th></tr></thead>';
            html += '<tbody>';

            stats.recent_updates.forEach(function(update) {
                html += '<tr>';
                html += '<td>' + update.post_id + '</td>';
                html += '<td>' + update.post_type + '</td>';
                html += '<td>' + update.updated_at + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
        }

        $('#stats-content').html(html);
    }

    // Affichage de la barre de progression
    function showProgress(show) {
        if (show) {
            $('#embeddings-progress').show();
        } else {
            $('#embeddings-progress').hide();
        }
    }

    // Mise à jour de la progression
    function updateProgress(percent, text) {
        $('.progress-fill').css('width', percent + '%');
        $('.progress-text').text(text);
    }

    // Affichage des notifications
    function showNotice(type, message) {
        var noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        var notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');

        $('.wrap h1').after(notice);

        // Auto-suppression après 5 secondes
        setTimeout(function() {
            notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

})(jQuery);