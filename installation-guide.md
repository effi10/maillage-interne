# Guide d'installation - Maillage Interne Intelligente

## Prérequis système

- WordPress 6.0 ou supérieur
- PHP 8.0 ou supérieur  
- Base de données MySQL 5.7+ ou MariaDB 10.3+
- Une clé API OpenAI ou Google Gemini

## Installation

### 1. Téléchargement du plugin

Téléchargez tous les fichiers du plugin et placez-les dans un dossier nommé `maillage-interne-intelligente`.

### 2. Upload sur le serveur

Copiez le dossier `maillage-interne-intelligente` dans le répertoire `/wp-content/plugins/` de votre installation WordPress.

### 3. Activation

1. Connectez-vous à l'administration WordPress
2. Allez dans **Extensions** > **Extensions installées**
3. Trouvez "Maillage Interne Intelligente" 
4. Cliquez sur **Activer**

### 4. Configuration des API

#### Option A : OpenAI

1. Créez un compte sur https://platform.openai.com
2. Générez une clé API dans la section "API Keys"
3. Dans WordPress, allez dans **Maillage Interne** > **Embeddings**
4. Sélectionnez "OpenAI" comme fournisseur
5. Collez votre clé API et choisissez le modèle
6. Cliquez sur "Tester la connexion"

#### Option B : Google Gemini

1. Créez un compte sur https://ai.google.dev
2. Générez une clé API dans Google AI Studio
3. Dans WordPress, allez dans **Maillage Interne** > **Embeddings**
4. Sélectionnez "Google Gemini" comme fournisseur
5. Collez votre clé API et choisissez le modèle
6. Cliquez sur "Tester la connexion"

### 5. Configuration des paramètres

1. Allez dans **Maillage Interne** > **Paramètres**
2. Configurez vos préférences par défaut :
   - Nombre d'éléments à afficher
   - Taille et ratio des images
   - Balises HTML pour les titres
   - Couleurs
   - Colonnes et responsive

### 6. Génération des embeddings

1. Allez dans **Maillage Interne** > **Embeddings** > **Calcul des embeddings**
2. Sélectionnez les types de contenu à traiter
3. Cliquez sur "Calculer les embeddings"
4. Attendez la fin du traitement

### 7. Test des blocs

1. Créez une nouvelle page ou article
2. Ajoutez le bloc "Pages Associées" ou "Publications Dynamiques"
3. Configurez les paramètres selon vos besoins
4. Prévisualisez le résultat

## Vérification de l'installation

### Tables de base de données

Le plugin crée automatiquement la table `wp_mii_embeddings` lors de l'activation. Vérifiez sa présence dans votre base de données.

### Fichiers JavaScript

Si les blocs Gutenberg ne s'affichent pas :
1. Vérifiez que les fichiers JS sont présents dans `assets/js/`
2. Vérifiez les erreurs dans la console du navigateur
3. Construisez les assets avec `npm run build` si nécessaire

### Permissions

Assurez-vous que les utilisateurs administrateurs ont accès au menu "Maillage Interne".

## Développement (optionnel)

Si vous souhaitez modifier les assets JavaScript :

```bash
# Installation des dépendances
npm install

# Mode développement avec hot reload
npm run start

# Build pour production
npm run build
```

## Dépannage

### Problème : Les blocs n'apparaissent pas

**Solution :**
- Vérifiez que le plugin est activé
- Videz le cache WordPress
- Vérifiez les erreurs JavaScript dans la console

### Problème : Erreur API lors des embeddings

**Solution :**
- Vérifiez votre clé API dans les paramètres
- Testez la connexion dans l'interface d'administration
- Vérifiez les logs d'erreur WordPress

### Problème : Similarité ne fonctionne pas

**Solution :**
- Assurez-vous que les embeddings sont générés
- Vérifiez les statistiques dans l'onglet dédié
- Régénérez les embeddings si nécessaire

### Problème : Affichage responsive

**Solution :**
- Vérifiez le point de bascule dans les paramètres
- Testez sur différentes tailles d'écran
- Videz le cache CSS

## Support

En cas de problème :
1. Consultez ce guide d'installation
2. Vérifiez les logs d'erreur WordPress
3. Testez en mode debug (`WP_DEBUG = true`)
4. Désactivez temporairement les autres plugins

## Performance

Pour optimiser les performances :
- Générez les embeddings en dehors des heures de pointe
- Limitez le nombre d'éléments affichés par bloc
- Utilisez un cache Redis ou Memcached si disponible
- Optimisez les images pour le web

## Sécurité

Le plugin implémente les bonnes pratiques WordPress :
- Vérification des nonces
- Échappement des données
- Validation des entrées utilisateur
- Contrôle des capacités

Assurez-vous de maintenir WordPress et le plugin à jour.