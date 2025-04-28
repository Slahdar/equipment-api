# API de Base de Données d'Équipements Techniques

Cette API fournit un système complet pour la gestion des données d'équipements techniques, comprenant les domaines d'équipements, les familles, les types, les marques, les produits et la documentation associée.

## Fonctionnalités

- Opérations CRUD complètes pour toutes les ressources
- Authentification et autorisation basée sur les permissions
- Téléchargement et gestion de fichiers pour la documentation technique
- Fonctionnalité d'association de produits
- Gestion d'inventaire des équipements
- Contrôle d'accès basé sur les rôles

## Installation

### Prérequis

- PHP 8.1 ou supérieur
- Composer
- MySQL 5.7 ou supérieur

### Configuration

1. Cloner le dépôt
```bash
git clone https://github.com/slahdar/equipment-api.git
cd equipment-api
```

2. Installer les dépendances
```bash
composer install
```

3. Créer le fichier d'environnement et générer la clé
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurer votre base de données dans le fichier `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=equipment_api
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

5. Exécuter les migrations avec les données initiales
```bash
php artisan migrate
```

6. Créer un lien symbolique pour le stockage
```bash
php artisan storage:link
```
7. Créer l'admin
```bash
php artisan db:seed
```

Ce sera :
```bash
    'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password')
```

8. Démarrer le serveur
```bash
php artisan serve
```

## Authentification

L'API utilise Laravel Sanctum pour l'authentification basée sur des tokens.

### Enregistrer un nouvel utilisateur

```
POST /api/register

{
  "name": "Nom Utilisateur",
  "email": "utilisateur@exemple.com",
  "password": "mot_de_passe",
  "password_confirmation": "mot_de_passe"
}
```

### Connexion

```
POST /api/login

{
  "email": "utilisateur@exemple.com",
  "password": "mot_de_passe"
}
```

La réponse inclura un token d'accès :

```json
{
  "success": true,
  "message": "Utilisateur connecté avec succès",
  "data": {
    "user": { ... },
    "token": "1|abcdefghijklmnopqrstuvwxyz123456",
    "permissions": [...],
    "roles": [...]
  }
}
```

### Utilisation de l'authentification

Pour tous les endpoints protégés, incluez le token dans l'en-tête Authorization :

```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456
```

### Déconnexion

```
POST /api/logout
```

## Endpoints de l'API

Toutes les réponses de l'API suivent un format standard :

```json
{
  "success": true|false,
  "message": "Message décrivant le résultat",
  "data": { ... }
}
```

En cas d'erreur :

```json
{
  "success": false,
  "message": "Message d'erreur",
  "errors": { ... }
}
```

### Domaines

| Méthode | Endpoint | Description | Permission |
|---------|----------|-------------|------------|
| GET     | /api/domains | Lister tous les domaines | view domains |
| GET     | /api/domains/{domain} | Obtenir les détails d'un domaine | view domains |
| POST    | /api/domains | Créer un nouveau domaine | create domains |
| PUT     | /api/domains/{domain} | Mettre à jour un domaine | edit domains |
| DELETE  | /api/domains/{domain} | Supprimer un domaine | delete domains |

#### Exemple : Créer un domaine

```
POST /api/domains

{
  "name": "Électronique"
}
```

### Familles

| Méthode | Endpoint | Description | Permission |
|---------|----------|-------------|------------|
| GET     | /api/families | Lister toutes les familles | view families |
| GET     | /api/families/{family} | Obtenir les détails d'une famille | view families |
| GET     | /api/domains/{domain}/families | Lister les familles par domaine | view families |
| POST    | /api/families | Créer une nouvelle famille | create families |
| PUT     | /api/families/{family} | Mettre à jour une famille | edit families |
| DELETE  | /api/families/{family} | Supprimer une famille | delete families |

#### Exemple : Créer une famille

```
POST /api/families

{
  "name": "Équipement de sécurité",
  "domain_id": 1
}
```

### Types d'équipements

| Méthode | Endpoint | Description | Permission |
|---------|----------|-------------|------------|
| GET     | /api/equipment-types | Lister tous les types d'équipements | view equipment_types |
| GET     | /api/equipment-types/{equipmentType} | Obtenir les détails d'un type d'équipement | view equipment_types |
| GET     | /api/families/{family}/equipment-types | Lister les types d'équipements par famille | view equipment_types |
| POST    | /api/equipment-types | Créer un nouveau type d'équipement | create equipment_types |
| PUT     | /api/equipment-types/{equipmentType} | Mettre à jour un type d'équipement | edit equipment_types |
| DELETE  | /api/equipment-types/{equipmentType} | Supprimer un type d'équipement | delete equipment_types |

#### Exemple : Créer un type d'équipement

```
POST /api/equipment-types

{
  "title": "Extincteur",
  "subtitle": "Dispositif portable de sécurité incendie",
  "family_id": 1,
  "inventory_required": true,
  "additional_fields": {
    "capacity": {
      "type": "string",
      "required": true,
      "label": "Capacité"
    },
    "product_type": {
      "type": "string",
      "required": true,
      "label": "Type de produit"
    }
  }
}
```

### Marques

| Méthode | Endpoint | Description | Permission |
|---------|----------|-------------|------------|
| GET     | /api/brands | Lister toutes les marques | view brands |
| GET     | /api/brands/{brand} | Obtenir les détails d'une marque | view brands |
| POST    | /api/brands | Créer une nouvelle marque | create brands |
| PUT     | /api/brands/{brand} | Mettre à jour une marque | edit brands |
| DELETE  | /api/brands/{brand} | Supprimer une marque | delete brands |

#### Exemple : Créer une marque

```
POST /api/brands

{
  "name": "FireSafe"
}
```

### Types de documents

| Méthode | Endpoint | Description | Permission |
|---------|----------|-------------|------------|
| GET     | /api/document-types | Lister tous les types de documents | view document_types |
| GET     | /api/document-types/{documentType} | Obtenir les détails d'un type de document | view document_types |
| POST    | /api/document-types | Créer un nouveau type de document | create document_types |
| PUT     | /api/document-types/{documentType} | Mettre à jour un type de document | edit document_types |
| DELETE  | /api/document-types/{documentType} | Supprimer un type de document | delete document_types |

#### Exemple : Créer un type de document

```
POST /api/document-types

{
  "name": "Manuel d'utilisation"
}
```

### Documents

| Méthode | Endpoint | Description | Permission |
|---------|----------|-------------|------------|
| GET     | /api/documents | Lister tous les documents | view documents |
| GET     | /api/documents/{document} | Obtenir les détails d'un document | view documents |
| GET     | /api/products/{product}/documents | Lister les documents par produit | view documents |
| GET     | /api/documents/{document}/download | Télécharger un fichier de document | view documents |
| POST    | /api/documents | Créer un nouveau document | create documents |
| PUT     | /api/documents/{document} | Mettre à jour un document | edit documents |
| PATCH   | /api/documents/{document}/archive | Archiver/désarchiver un document | archive documents |
| DELETE  | /api/documents/{document} | Supprimer un document | delete documents |

#### Exemple : Créer un document

```
POST /api/documents

{
  "name": "Manuel d'utilisation de l'extincteur",
  "document_type_id": 1,
  "file": (données binaires du fichier),
  "issue_date": "2025-01-15",
  "expiry_date": "2030-01-15",
  "version": "1.2",
  "reference": "MAN-FE-2025",
  "product_ids": [1, 3]
}
```

### Produits

| Méthode | Endpoint | Description | Permission |
|---------|----------|-------------|------------|
| GET     | /api/products | Lister tous les produits | view products |
| GET     | /api/products/{product} | Obtenir les détails d'un produit | view products |
| GET     | /api/brands/{brand}/products | Lister les produits par marque | view products |
| GET     | /api/equipment-types/{equipmentType}/products | Lister les produits par type d'équipement | view products |
| GET     | /api/products/{product}/associated-products | Lister les produits associés | view products |
| POST    | /api/products | Créer un nouveau produit | create products |
| PUT     | /api/products/{product} | Mettre à jour un produit | edit products |
| POST    | /api/products/{product}/associate | Associer avec un autre produit | associate products |
| DELETE  | /api/products/{product}/dissociate/{associatedProduct} | Dissocier des produits | associate products |
| POST    | /api/products/{product}/documents/{document} | Attacher un document à un produit | edit products |
| DELETE  | /api/products/{product}/documents/{document} | Détacher un document d'un produit | edit products |
| DELETE  | /api/products/{product} | Supprimer un produit | delete products |

#### Exemple : Créer un produit

```
POST /api/products

{
  "name": "Extincteur CO2 5kg",
  "brand_id": 1,
  "equipment_type_id": 1,
  "document_ids": [1, 2]
}
```

#### Exemple : Associer des produits

```
POST /api/products/1/associate

{
  "associated_product_id": 2
}
```

### Inventaires

| Méthode | Endpoint | Description | Permission |
|---------|----------|-------------|------------|
| GET     | /api/inventories | Lister tous les inventaires | view inventories |
| GET     | /api/inventories/{inventory} | Obtenir les détails d'un inventaire | view inventories |
| GET     | /api/products/{product}/inventories | Lister les inventaires par produit | view inventories |
| POST    | /api/inventories | Créer un nouvel inventaire | create inventories |
| PUT     | /api/inventories/{inventory} | Mettre à jour un inventaire | edit inventories |
| DELETE  | /api/inventories/{inventory} | Supprimer un inventaire | delete inventories |

#### Exemple : Créer un inventaire

```
POST /api/inventories

{
  "product_id": 1,
  "location": "Bâtiment A, Étage 2, Salle 201",
  "brand_id": 1,
  "commissioning_date": "2025-02-10",
  "additional_fields": {
    "capacity": "5kg",
    "product_type": "CO2"
  }
}
```

### Gestion des utilisateurs (Admin uniquement)

| Méthode | Endpoint | Description | Permission |
|---------|----------|-------------|------------|
| GET     | /api/users | Lister tous les utilisateurs | view users |
| GET     | /api/users/{user} | Obtenir les détails d'un utilisateur | view users |
| POST    | /api/users | Créer un nouvel utilisateur | create users |
| PUT     | /api/users/{user} | Mettre à jour un utilisateur | edit users |
| DELETE  | /api/users/{user} | Supprimer un utilisateur | delete users |
| POST    | /api/users/{user}/assign-role | Attribuer un rôle à un utilisateur | manage permissions |
| POST    | /api/users/{user}/remove-role | Retirer un rôle d'un utilisateur | manage permissions |
| POST    | /api/users/{user}/assign-permission | Attribuer une permission à un utilisateur | manage permissions |
| POST    | /api/users/{user}/remove-permission | Retirer une permission d'un utilisateur | manage permissions |

#### Exemple : Créer un utilisateur

```
POST /api/users

{
  "name": "Nouveau Responsable",
  "email": "responsable@exemple.com",
  "password": "mot_de_passe",
  "password_confirmation": "mot_de_passe",
  "role": "manager"
}
```

## Rôles et Permissions

L'API implémente trois rôles principaux :

1. **Admin** - Accès complet à toutes les fonctionnalités
2. **Manager** - Peut voir, créer et modifier la plupart des ressources, mais ne peut pas les supprimer ou gérer les permissions
3. **Utilisateur** - Accès de base en lecture seule

### Permissions disponibles

| Permission | Description |
|------------|-------------|
| view domains | Voir la liste et les détails des domaines |
| create domains | Créer de nouveaux domaines |
| edit domains | Mettre à jour les domaines existants |
| delete domains | Supprimer des domaines |
| view families | Voir la liste et les détails des familles |
| create families | Créer de nouvelles familles |
| edit families | Mettre à jour les familles existantes |
| delete families | Supprimer des familles |
| view equipment_types | Voir la liste et les détails des types d'équipements |
| create equipment_types | Créer de nouveaux types d'équipements |
| edit equipment_types | Mettre à jour les types d'équipements existants |
| delete equipment_types | Supprimer des types d'équipements |
| view brands | Voir la liste et les détails des marques |
| create brands | Créer de nouvelles marques |
| edit brands | Mettre à jour les marques existantes |
| delete brands | Supprimer des marques |
| view document_types | Voir la liste et les détails des types de documents |
| create document_types | Créer de nouveaux types de documents |
| edit document_types | Mettre à jour les types de documents existants |
| delete document_types | Supprimer des types de documents |
| view documents | Voir la liste et les détails des documents |
| create documents | Créer de nouveaux documents |
| edit documents | Mettre à jour les documents existants |
| delete documents | Supprimer des documents |
| archive documents | Archiver/désarchiver des documents |
| view products | Voir la liste et les détails des produits |
| create products | Créer de nouveaux produits |
| edit products | Mettre à jour les produits existants |
| delete products | Supprimer des produits |
| associate products | Associer/dissocier des produits |
| view inventories | Voir la liste et les détails des inventaires |
| create inventories | Créer de nouveaux inventaires |
| edit inventories | Mettre à jour les inventaires existants |
| delete inventories | Supprimer des inventaires |
| view users | Voir la liste et les détails des utilisateurs |
| create users | Créer de nouveaux utilisateurs |
| edit users | Mettre à jour les utilisateurs existants |
| delete users | Supprimer des utilisateurs |
| manage permissions | Attribuer/retirer des rôles et des permissions |

## Gestion des erreurs

L'API renvoie des codes de statut HTTP appropriés ainsi que des messages d'erreur :

- `400 Bad Request` - Entrée invalide ou violation de règle métier
- `401 Unauthorized` - Authentification manquante ou invalide
- `403 Forbidden` - Authentification valide mais permissions insuffisantes
- `404 Not Found` - Ressource non trouvée
- `422 Unprocessable Entity` - Erreurs de validation
- `500 Internal Server Error` - Erreur côté serveur

Exemple de réponse d'erreur :

```json
{
  "success": false,
  "message": "Échec de la validation",
  "errors": {
    "name": ["Le champ nom est obligatoire."],
    "brand_id": ["L'identifiant de marque sélectionné est invalide."]
  }
}
```

## Stockage de fichiers

Les documents sont stockés dans le répertoire `storage/app/public/documents`. Pour les rendre accessibles, assurez-vous que le lien symbolique est créé :

```bash
php artisan storage:link
```

Cela rend les fichiers accessibles via le chemin d'URL `/storage/documents/`.

## Considérations de sécurité

- Tous les endpoints de l'API (sauf login et register) nécessitent une authentification
- Les permissions basées sur les rôles restreignent l'accès à des fonctionnalités spécifiques
- Les mots de passe sont hachés en utilisant le hachage sécurisé de Laravel
- Authentification basée sur des tokens avec expiration configurable
- Validation des entrées pour prévenir les attaques par injection
- Validation des fichiers pour les téléchargements de documents (seuls les fichiers PDF sont autorisés)

## Relations entre les données

- Un **Domaine** possède plusieurs **Familles**
- Une **Famille** appartient à un **Domaine** et possède plusieurs **Types d'équipements**
- Un **Type d'équipement** appartient à une **Famille** et possède plusieurs **Produits**
- Une **Marque** possède plusieurs **Produits** et **Inventaires**
- Un **Produit** appartient à une **Marque** et un **Type d'équipement**
- Un **Produit** possède plusieurs **Documents** (relation plusieurs-à-plusieurs)
- Un **Produit** possède plusieurs **Produits associés** (relation plusieurs-à-plusieurs autoréférentielle)
- Un **Produit** possède plusieurs **Inventaires**
- Un **Document** appartient à un **Type de document** et possède plusieurs **Produits** (relation plusieurs-à-plusieurs)
- Un **Inventaire** appartient à un **Produit** et une **Marque**

## Améliorations futures

Améliorations potentielles à considérer :

1. Implémentation de la pagination pour les grands ensembles de données
2. Ajout de capacités de recherche et de filtrage
3. Implémentation de mise en cache pour améliorer les performances
4. Ajout du support pour plus de formats de documents (au-delà du PDF)
5. Implémentation du versionnement pour les documents
6. Ajout de capacités d'exportation pour les rapports
7. Implémentation de webhooks pour l'intégration avec d'autres systèmes
8. Ajout d'authentification à deux facteurs pour une sécurité renforcée