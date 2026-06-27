# Application de Gestion de Parking

Une application web moderne en PHP (architecture MVC) pour la gestion et la réservation de places de parking.

## 🚀 Fonctionnalités

### 👤 Espace Client
- **Inscription & Connexion** : Inscription sécurisée avec envoi de code de validation (simulation) et système de double facteur.
- **Tableau de Bord** : Vue d'ensemble sur le profil, les réservations actives et l'historique.
- **Réservation de place** : Sélection de la place en temps réel avec calcul automatique du tarif en fonction de la durée et du type de place.
- **Mes Réservations** : Modification (changement de place ou d'horaires) et annulation des réservations en cours.
- **Mon Compte** : Gestion des informations personnelles, mot de passe et préférences de paiement.

### 👑 Espace Administration
- **Gestion des Utilisateurs** : Visualisation, filtrage, activation ou désactivation des comptes clients.
- **Gestion des Places** : Suivi du statut des places (libre, occupée, maintenance) et modification de leurs types (standard, handicapée, réservée).
- **Gestion des Réservations** : Consultation et suivi de l'ensemble des réservations en cours et passées.
- **Rapports & Statistiques** : Visualisation des revenus générés et taux d'occupation des places.
- **Gestion des Règles & Tarifs** : Ajustement des tarifs horaires selon les périodes (Journée, Nuit, Week-end).

---


## 📦 Installation & Configuration

1. **Cloner le projet** dans votre répertoire de travail local.
    ```env
    git clone
   ```
2. **Base de données** :
   - Créez une base de données MySQL (ex: `parking_db`).
   - Importez le fichier database.sql
3. **Configuration de l'environnement** :
   - Créez un fichier `.env` à la racine du projet avec les informations de connexion :
     ```env
     DB_HOST=localhost
     DB_NAME=parking_db
     DB_USER=votre_utilisateur
     DB_PASS=votre_mot_de_passe
     ```
