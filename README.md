# ToDoList  

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/db4350e3a3074aaf96fc304d98360c7e)](https://www.codacy.com/gh/fredsko77/todo-co/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=fredsko77/todo-co&amp;utm_campaign=Badge_Grade)
========

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1


## Build With  

- Symfony 4.4
- Bootstrap 4

## Installation

1. Clôner le projet
```
git clone https://github.com/fredsko77/todo-co.git <répertoire>
```

2. Assurez vous que composer soit installé sinon installer composer -> [Doc installation Composer](https://getcomposer.org/download/)

3. Installer les dépendances nécessaires à l'application
```
composer install
```

4. Configurer votre accès à la base de données en mofiant la ligne ci-dessous dans le fichier .env
```
DATABASE_URL=
```

5. Créer la base de données
```php bin/console doctrine:database:create```

6. Générer les fichiers de migrations 
```
php bin/console make:migration
``` 
En cas d'erreur, executer la commande **`mkdir migrations`** (ou créer un repertoire **migrations** à la racine du projet) puis relancer la commande **`php bin/console make:migration`**

7. Executer les fichiers de migrations 
``` 
php bin/console doctrine:migrations:migrate
```

8. Executer les fixtures (jeu de données initiales)
``` 
php bin/console doctrine:fixtures:load
```

## Prendre en main rapide l'application
Maintenant que vous avez charger le jeu de données initiales, vous pouvez vous connecter à l'application avec les utilisateurs suivants: 
Lien de connexion :

```/login```

Un compte avec le rôle utilisateur a déjà été crée, utilisez le pour tester l'application :

```
"username" : "user@todo.fr",
"password" : "Passuser123"
```

Un compte avec le rôle administrateur a déjà été crée, utilisez le pour tester l'application :

```
"username" : "admin@todo.fr",
"password" : "Passadmin123"
```


## Tests

> **Attention:** Avant de faire les test .
>
> Télécharger l'extension php xdebug et configurer le fichier php.ini.

1. Mettre à jour le fichier **phpunit.xml.dist** à la racine du projet pour créer une base de données de tests
```
<env name="DATABASE_URL" value="DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db__test_name"/>
```

2. Créer la base de données de test 
``` 
php bin/console doctrine:database:create --env=test
```

3. Executer les fichiers de migrations 
``` 
php bin/console doctrine:migrations:migrate --env=test
```

4. Générer les données de test 
``` 
php bin/console doctrine:fixtures:load --env=test
```

5. Lancer les tests
```
php bin/phpunit
```

6. Afficher les détails sur la couverture de code des tests dans un fichier HTML 
```
php bin/phpunit --coverage-html=tests/coverage
```

## Blackfire 

Installer [blackfire](/blob/master/README.md) pour analyser la performance de votre codes

## Contributing

To contribute see [CONTRIBUTING.md](https://github.com/JeanD34/p8-sf4/blob/master/CONTRIBUTING.md)
