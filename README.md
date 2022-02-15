<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Installation

1 - Cloner le dépot via github desktop <br>
2 - Ouvrir un terminal et faire composer install pour installer les dépendances php et addons ex : bootstrap <br>
3 - Copier le fichier .env.example en .env <br>
4 - Sur terminal : php artisan key:generate <br>
5 - Creer une bd sur mysql <br>
6 - Ouvrir le dossier .env copié manuellement et y inscrire le nom de la bd qui vient d'etre créer <br>
7 - Sur terminal : faire php artisan migrate --seed <br>
8 - Sur terminal : php artisan serve et ouvrir le site sur un navigateur <br>

8.1 - Si sur http://127.0.0.1:8000 une page blanche s'affiche, faites sur terminal : npm install puis npm run dev
