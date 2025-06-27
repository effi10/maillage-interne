# Plugin WordPress - Maillage Interne Intelligente

## Description

Le plugin "Maillage Interne Intelligente" est un plugin WordPress Gutenberg qui automatise et améliore le maillage interne de votre site web grâce à l'intelligence artificielle.

## Fonctionnalités

### Blocs Gutenberg

1. **Bloc "Pages Associées"** - Affiche automatiquement les pages enfants ou sœurs de la page actuelle
2. **Bloc "Publications Dynamiques"** - Affiche des publications selon 3 modes :
   - Recherche par mot-clé
   - Filtrage par taxonomie
   - Similarité sémantique (IA)

### Interface d'administration

- **Paramètres globaux** : Configuration par défaut des blocs
- **Gestion des embeddings** : Interface complète pour gérer les vecteurs IA
- **Statistiques** : Suivi et monitoring des embeddings

### Intelligence Artificielle

- Support OpenAI (text-embedding-3-small, text-embedding-3-large, text-embedding-ada-002)
- Support Google Gemini (text-embedding-004, embedding-001)
- Calcul de similarité cosinus
- Mise à jour automatique des embeddings

## Installation

1. Téléchargez le dossier du plugin
2. Placez-le dans `/wp-content/plugins/`
3. Activez le plugin dans l'administration WordPress
4. Configurez vos clés API dans "Maillage Interne" > "Embeddings"

## Configuration

### Clés API

1. **OpenAI** : Obtenez votre clé sur https://platform.openai.com
2. **Gemini** : Obtenez votre clé sur https://ai.google.dev

### Paramètres par défaut

Configurez les valeurs par défaut dans "Maillage Interne" > "Paramètres" :
- Nombre d'éléments à afficher
- Taille et ratio des images
- Balises HTML pour les titres
- Couleurs et typographie
- Nombre de colonnes pour la grille
- Point de bascule responsive

## Utilisation

### Bloc "Pages Associées"

1. Ajoutez le bloc dans l'éditeur Gutenberg
2. Choisissez le type de relation (enfants ou sœurs)
3. Configurez l'affichage (images, titres, excerpts)
4. Ajustez la mise en page (liste ou grille)

### Bloc "Publications Dynamiques"

1. Ajoutez le bloc dans l'éditeur Gutenberg
2. Sélectionnez le type de contenu
3. Choisissez le mode de sélection :
   - **Recherche** : Entrez un mot-clé
   - **Taxonomie** : Sélectionnez une taxonomie et un terme
   - **Similarité** : Nombre d'éléments similaires (nécessite les embeddings)
4. Configurez l'affichage selon vos besoins

### Gestion des embeddings

1. Configurez vos clés API
2. Sélectionnez les types de contenu à traiter
3. Lancez la génération des embeddings
4. Activez la mise à jour automatique si souhaité

## Structure du plugin

```
maillage-interne-intelligente/
├── maillage-interne-intelligente.php (fichier principal)
├── includes/
│   ├── class-admin.php (interface d'administration)
│   ├── class-blocks.php (gestion des blocs Gutenberg)
│   ├── class-embeddings.php (gestion des embeddings)
│   ├── class-similarity.php (calculs de similarité)
│   ├── class-api-client.php (client API IA)
│   └── class-ajax.php (requêtes AJAX)
├── admin/
│   ├── settings-page.php (page de paramètres)
│   └── embeddings-page.php (page des embeddings)
├── assets/
│   ├── js/
│   │   ├── admin.js (JavaScript admin)
│   │   └── blocks.js (JavaScript Gutenberg)
│   └── css/
│       ├── admin.css (styles admin)
│       ├── blocks.css (styles front-end)
│       └── blocks-editor.css (styles éditeur)
└── languages/
    └── maillage-interne-intelligente.pot (fichier de traduction)
```

## Sécurité

- Vérification des nonces pour toutes les requêtes AJAX
- Validation et échappement des données
- Contrôle des capacités utilisateur
- Stockage sécurisé des clés API
- Protection contre les injections XSS

## Performances

- Chargement conditionnel des scripts
- Traitement par lots pour les embeddings
- Cache des requêtes de similarité
- Optimisation des requêtes de base de données

## Accessibilité

- Contrôles Gutenberg accessibles
- Balises HTML sémantiques
- Support des lecteurs d'écran
- Navigation au clavier

## Responsive

- Auto-switch en liste sur mobile
- Points de bascule configurables
- Grilles adaptatives
- Images responsive

## API et Hooks

### Actions

- `mii_embedding_generated` : Déclenché après génération d'un embedding
- `mii_embeddings_cleared` : Déclenché après suppression des embeddings

### Filtres

- `mii_embedding_content` : Filtre le contenu avant génération de l'embedding
- `mii_similarity_threshold` : Filtre le seuil de similarité
- `mii_block_attributes` : Filtre les attributs des blocs

## Compatibilité

- WordPress 6.0+
- PHP 8.0+
- Gutenberg/Block Editor
- Multisite compatible

## Support

Pour toute question ou problème :
1. Vérifiez la documentation
2. Consultez les logs d'erreur WordPress
3. Testez vos clés API dans l'interface d'administration

## Développement

### Prérequis

- Node.js 16+
- npm ou yarn
- wp-scripts (pour le build des assets JavaScript)

### Build

```bash
npm install
npm run build
```

### Mode développement

```bash
npm run start
```

## Changelog

### Version 1.0.0
- Version initiale
- Blocs Gutenberg "Pages Associées" et "Publications Dynamiques"
- Interface d'administration complète
- Support OpenAI et Gemini
- Calcul de similarité cosinus
- Système de traduction prêt

## Licence

GPL v2 or later

## Crédits

Développé pour optimiser le maillage interne des sites WordPress avec l'intelligence artificielle.
