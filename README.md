# ToDo!

Copier "db.inc.php.example" dans "db.inc.php" et configurer la base de données

Copier "login.php.example" dans "login.php" et configurer le mot de passe

Créer les tables suivantes :

```
CREATE TABLE `weekly_categories` (
 `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 `priority` tinyint(3) NOT NULL DEFAULT '1',
 `special_monday` tinyint(1) NOT NULL DEFAULT '0',
 `special_tuesday` tinyint(1) NOT NULL DEFAULT '0',
 `special_wednesday` tinyint(1) NOT NULL DEFAULT '0',
 `special_thursday` tinyint(1) NOT NULL DEFAULT '0',
 `special_friday` tinyint(1) NOT NULL DEFAULT '0',
 `special_saturday` tinyint(1) NOT NULL DEFAULT '0',
 `special_sunday` tinyint(1) NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tasks` (
 `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
 `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 `done` tinyint(1) NOT NULL DEFAULT '0',
 `deadline` date DEFAULT NULL,
 `weekly_category_id` tinyint(3) unsigned DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `weekly_category` (`weekly_category_id`),
 CONSTRAINT `weekly_category` FOREIGN KEY (`weekly_category_id`) REFERENCES `weekly_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```
