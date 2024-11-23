# Créer base de donnée skills :

```sql
CREATE DATABASE skills_management;
```

```sql
INSERT INTO skills (user_id, skill_name, level)
VALUES 
(1, 'Maquetter des interfaces', 1),
(1, 'Interface Utilisateur', 1),
(1, 'Développer la partie dynamique', 1),
(1, 'Installer et configurer son environnement', 1),
(1, 'Développer les composant SQL / NoSQL', 1),
(1, 'Développer des composants coté serveur', 1),
(1, 'Documenter le déploiement', 1),
(1, 'Mettre en place une DB', 1);
```

# Passer un utilisateur en mode Administrateur

```sql
UPDATE users
SET is_admin = 1
WHERE username = 'admin'; -- ici admin = utilisateur à passer en mode administrateur
```


# Arborescence du projet :

```
competence_table
├─ auth.php
├─ db.php 
├─ index.php
├─ login.php
├─ logout.php
└─ register.php
```