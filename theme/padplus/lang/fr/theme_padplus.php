<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language file.
 *
 * @package   theme_padplus
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// A description shown in the admin theme selector.
$string['choosereadme'] = 'Le thème PAD+ est un thème enfant de Boost.';
// The name of our plugin.
$string['pluginname'] = 'PAD+';
// We need to include a lang string for each block region.
$string['region-side-pre'] = 'Droit';

// Main strings.
$string['global-search'] = 'Rechercher des séquences, des personnes...';
$string['myhome-welcome'] = 'Bienvenue  {$a}';

// Sidebar menu strings.
$string['aria-main-nav'] = 'Navigation principale';
$string['categories-menu-nav'] = 'Catégories';
$string['allcategories-menu'] = 'Toutes les catégories';
$string['allcourses-menu'] = 'Tous les cours';
$string['workshop-menu'] = 'Les ateliers collectifs';
$string['catalog-menu'] = 'Ressources partagées';

// My Courses page strings.
$string['mycatalog-courses'] = 'Mes ressources partagées';
$string['aria:mycatalog-courses'] = 'Afficher mes ressources partagées';
$string['catalog-course'] = 'Ressource partagée';
$string['workshop-course'] = 'Atelier collectif';

// Theme settings strings.
$string['settings-color-title'] = 'Couleurs du thème';
$string['settings-color-desc'] = "Couleurs qui définissent l'identité de votre site";
$string['settings-primarycolor'] = 'Couleur principale';
$string['settings-primarycolor-desc'] = 'Couleur utilisée pour les éléments principaux';
$string['settings-complementary-color'] = 'Couleur complémentaire';
$string['settings-complementary-color-desc'] = 'Couleur utilisée pour les éléments secondaires';
$string['settings-sidebarcolor'] = 'Couleur de la barre latérale';
$string['settings-sidebarcolor-desc'] = "Couleur utilisée pour l'arrière-plan de la barre latérale";
$string['settings-footer'] = 'Pied de page';
$string['settings-footer-desc'] = 'Configuration des éléments du pied de page';
$string['settings-helplink'] = "Lien d'aide";
$string['settings-helplink-desc'] = "URL vers un site externe hébergeant l'aide PAD+";
$string['settings-supportlink'] = 'Support';
$string['settings-supportlink-desc'] = 'URL vers le formulaire de support PAD+';
$string['settings-contactlink'] = 'Contact';
$string['settings-contactlink-desc'] = 'URL vers le formulaire de contact PAD+';
$string['settings-legalnoticeslink'] = 'Mentions légales';
$string['settings-legalnoticeslink-desc'] = 'URL vers les mentions légales PAD+';
$string['settings-privacylink'] = 'Politique de confidentialité';
$string['settings-privacylink-desc'] = 'URL vers la politique de confidentialité PAD+';
$string['settings-copyright'] = 'Copyright';
$string['settings-copyright-desc'] = 'Information du pied de page';
$string['settings-sidebarmenu'] = 'Menu de navigation latérale';
$string['settings-sidebarmenu-desc'] = "Paramètres d'affichage du menu de navigation latérale.";
$string['settings-workshopids-desc'] = "Si l'utilisateur a accès à une des catégories sélectionnées, alors elle apparaît avec l'intitulé 'Les ateliers collectifs' dans la navigation latérale.";
$string['settings-catalogid-desc'] = "Si l'utilisateur a accès à la catégorie sélectionnée, alors elle apparaît avec l'intitulé 'Ressources partagées' dans son propre groupe dans la navigation latérale.";
$string['settings-catalogid-none'] = '[aucune]';
$string['settings-allcourses-desc'] = "La première catégorie accessible à l'utilisateur (non sélectionnée par les options ci-dessus) apparaît avec l'intitulé 'Toutes les séquences' dans la navigation latérale.";

// Enrolment strings.
$string['unenrolme'] = 'Me désinscrire';

// Categories & course page strings.
$string['addnewworkshop'] = 'Ajouter un atelier';
$string['btn-coursebox'] = 'Consulter cette séquence';
$string['btn-workshopbox'] = 'En savoir plus';
$string['no-participants-enrolled'] = 'aucun stagiaire inscrit';
$string['participants-enrolled'] = 'Stagiaires inscrits';
$string['settings-category'] = 'Gestion de cette catégorie';
$string['workshop-teacher'] = 'Organisateur';
