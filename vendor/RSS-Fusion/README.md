RSS-Fusion
----------
**Fusion de flux RSS multiples.**
 * Sans base de données
 * Script auto hébergeable
 * Gestion des flux RSS/ATOM
 * Flux global
 * Surcharge de personnalisation


Démo et exemple d'utilisation
----------
Un **[exemple d'utilisation en ligne]** est parfois disponible, selon la sucharge du serveur en mousse qui l'héberge. Les sources de cet exemple sont disponibles **[par ici].**

Par défaut
----------
 * Système de mise en cache des flux (durée = 1h)
 * Données triées par date/heure décroissante
 * Possibilité de spécifier des mots clés ("star and bad words")


Prérequis
---------
 * Serveur Web (tel que Apache2)
 * A partir de PHP 5.4 avec les modules suivants : `cUrl`, `JSON`, `SimpleXML`, `DOMDocument` (et potentiellement quelques autres selon votre configuration)


Installation
------------
Récupération des sources
```
$ git clone https://git.framasoft.org/Erase/RSS-Fusion.git
```
ou en [téléchargeant l'archive zip]

~~Vérifier/assigner les droits en écriture au sein du répertoire :~~
 * ~~`/data`~~



Personnalisation
----------
En se basant sur les paramètres précisés dans le fichier `/system/config/default.php`, il est possible de surcharger les informations en les précisant dans le fichier `/system/config/localconfig.php`, notamment les mots clés.

Le reste des traitements à personnaliser peuvent se baser sur ceux présents dans le fichier `index.php`


Informations annexes
--------
 * RSS-Fusion utilise la librairie [SimplePie] pour parser les flux RSS. 
 * Les données en caches sont dans le répertoire `/system/cache`
 * La recherche des mots-clés se fait sur le titre et la description de chaque élément des flux RSS
 * Deux types de mots clés sont pris en charge : **star** pour des éléments à mettre en avant et **bad** pour des éléments à masquer


Structuration des éléments des flux
--------
```
array(11) {
    ["link"]         => URL de l'élément
    ["title"]        => Titre de l'élément
    ["permalink"]    => Permalien de l'élément
    ["description"]  => Description de l'élément
    ["pubdate"]      => Timestamp de publication
    ["date_read"]    => Date de publication au format jour-mois-année heures:minutes
    ["category"]     => Catégorie de l'élément
    ["base"]         => URL du flux RSS source
    ["show"]         => true (default) | false (bad word) | star (star word)
    ["enclosure"]    => Elément inclus 
}
```


Licence
-------
 En dehors des différentes licences spécifiques aux outils utilisés, le reste du code est distribué sous licence [Creative Commons BY-NC-SA 4.0]


Auteur
------
 * [Un simple développeur paysagiste] :) avec un peu de temps libre et aucune prétention - contact_at_green-effect.fr




[//]: # 
   [téléchargeant l'archive zip]: <https://framagit.org/Erase/RSS-Fusion/repository/archive.zip>
   [SimplePie]: <http://simplepie.org/>
   [Creative Commons BY-NC-SA 4.0]: <http://creativecommons.org/licenses/by-nc-sa/4.0/>
   [Un simple développeur paysagiste]: <http://www.green-effect.fr>
   [exemple d'utilisation en ligne]: <http://rss-fusion.green-effect.fr/>
   [par ici]: <https://framagit.org/Erase/RSS-Fusion-Demo>