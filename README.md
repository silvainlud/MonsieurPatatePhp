# Monsieur Patate

Récupération de l'emploi du temps de ADE ULCO (https://edt.univ-littoral.fr/) et gestion des devoirs.

Ce dépôt s'inscrit dans le cadre d'un projet composé de 3 dépôts :
- **MonsieurPatatePhp** : Site web peremettant la consultation de l'emploi du temps et la gestion des devoirs. Cette application embarque également les scripts réalisant l'actualisation de l'emploi du temps (et d'envoyer les notifications en conséquences)
- [MonsieurPatatePlanning](https://github.com/silvain-eu/MonsieurPatatePlanning) : Script JS faisant des captures d'écran de l'emploi du temps
- [MonsieurPatateBot](https://github.com/silvain-eu/MonsieurPatateBot) : Bot discord pour consulter l'emploi du temps

## Technologie

Ce projet est développé en PHP avec le framework Symfony `6.0`. Pour des raisons d'organisation, ce projet n'utilise pas la hiérarchie de dossiers par défaut : 
- `src/Http` : contient l'ensemble des éléments qui concerne directement la couche Http (ex : Controller)
- `src/Domain/{name}` : correspond à la logique métier (Entity, Repository, Fomulaire liè à une Entity, ...).
- `src/Infrastrcuture/{name}` : cela définit les éléments de l'infrastructure (ex : envoie de notification Push ou Discord, ... )

Cette organisation est inspirée : [https://grafikart.fr/blog/structure-code-symfony](https://grafikart.fr/blog/structure-code-symfony).


## Fonctionnalités

- Consulter les captures d'écran de l'emploi du temps, et importation des nouvelles
- Gestion des devoirs avec système de rappel
- Notification Discord et WebPush
- Application PWA
- Récupération et analyze du fichier ICal fourni par l'université du Littoral Côte d'Opale avec notification en cas de changement


## Installation de l'environnement de développement

### Prerequisites

Pour démarrer l'environnement, vous devez avoir les outils suivants :
- [Docker](https://docs.docker.com/compose/)
- [Docker Compose](https://docs.docker.com/)

### Instruction

Pour lancer l'application, il suffit d'utiliser le fichier de configuration de Docker Compose (à la racine du projet) :

```bash
make dev
```
Il faut alors configurer l'application, pour cela il suffit de créer un fichier `.env.local` en se basant sur le fichier `.env`.

Une fois le site web configuré, il faut créer la base données : 

```bash
make cmd # pour se connecter au containeur PHP
php bin/console doctrine:database:create # création de la base de données
php bin/console doctrine:migration:migrate --allow-no-migration -n # application des migrations sur la base de données
```

L'application est alors accessible à l'adresse  : http://localhost:8000


## Déploiement

Ce projet est déployé automatiquement par un service [Drone.Io](https://www.drone.io/) à l'adresse [mpatate.silvain.eu](https://mpatate.silvain.eu/). 
Pour cela, j'utilise notamment ansible avec [ansistrano](https://github.com/ansistrano/deploy) pour automatiser cette tâche : [/tools/ansible/deploy/deploy.yml](https://github.com/silvain-eu/MonsieurPatatePhp/tree/main/tools/ansible/deploy).

L'objectif de cette automatisation est de lancer les containeurs :
 - Image PHP avec les fichiers de l'application
 - Nginx avec les fichiers statique de l'application
 - Redis pour le cache
 - Mysql pour la base de données

## License

This project is licensed under the [GNU General Public License v3.0](LICENSE.md)- see the [LICENSE.md](LICENSE.md) file for
details
