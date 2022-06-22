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

// Privacy.
$string['privacy:metadata'] = "Le plugin Thème PAD+ n'enregistre aucune donnée personnelle.";

// Main strings.
$string['global-search'] = 'Rechercher des séquences, des personnes...';
$string['myhome-welcome'] = 'Bienvenue  {$a}';

// Sidebar menu strings.
$string['aria-main-nav'] = 'Navigation principale';
$string['categories-menu-nav'] = 'Catégories';
$string['allcategories-menu'] = 'Toutes les catégories';
$string['allcourses-menu'] = 'Tous les cours';
$string['workshop-menu'] = 'Ateliers complémentaires';
$string['catalog-menu'] = 'Ressources professionnelles';

// Login page strings.
$string['show-password'] = 'Montrer le mot de passe';
$string['hide-password'] = 'Cacher le mot de passe';

// My Courses page strings.
$string['mycourses-page'] = 'Mes séquences';
$string['mycourses-page_help'] = "• Mes séquences en cours sont celles où vous êtes inscrit(e) et qui ont débuté.<br>
                                  • Mes séquences à venir sont celles où vous êtes inscrit(e) et dont la date de début est à venir.<br>
                                  • Mes séquences passées sont celles qui sont terminées ou dont la date butoir est passée.<br>
                                  • Mes séquences favorites sont celles que vous avez ajoutées avec l’icône “étoile”.";
$string['mycourse-page_help_label'] = 'Aide sur mes séquences';
$string['eventmycoursesviewed'] = 'Page mes séquences consultée';
$string['mycatalog-courses'] = 'Mes ressources professionnelles';
$string['aria:mycatalog-courses'] = 'Afficher mes ressources professionnelles';
$string['catalog-course'] = 'Ressource professionnelle';
$string['workshop-course'] = 'Atelier complémentaire';

// My progress page strings.
$string['myprogress-professional-title'] = 'Parcours des stagiaires';
$string['goto-myprogress'] = 'Accéder à mon parcours';
$string['myprogress-professional-intro'] = 'Vous pouvez suivre la progression des stagiaires inscrits à vos séquences. Elles sont triées selon leur catégorie principale.';
$string['myprogress-professional-intro-sub'] = 'Les données affichées prennent en compte uniquement ce qui est effectué sur la plateforme.';
$string['myprogress-student-title'] = 'Mon parcours';
$string['myprogress-student-intro'] = 'Suivez la progression de vos séquences. Elles sont triées selon leur catégorie principale.';
$string['myprogress-student-intro-note'] = 'Les données affichées prennent en compte uniquement ce qui est effectué sur la plateforme.';
$string['myprogress-subtitle'] = 'Répartition des séquences';
$string['myprogress-subtitle-description'] = 'La vue détaillée permet d’afficher la catégorie parente des séquences ainsi que leur état d’avancement.';
$string['myprogress-loading'] = 'Recherche des données en cours...';
$string['eventmyprogressviewed'] = 'Page progrès consultée';
$string['user-progress'] = 'Suivi de {$a}';
$string['user-progress-search-placeholder'] = 'Rechercher un stagiaire';
$string['user-progress-no-selection'] = 'Aucun stagiaire sélectionné';
$string['course-singular'] = 'séquence';
$string['course-plural'] = 'séquences';
$string['course-done'] = 'séquence terminée';
$string['course-done-plural'] = 'séquences terminées';
$string['course-inprogress'] = 'séquence commencée';
$string['course-inprogress-plural'] = 'séquences commencées';
$string['course-todo'] = 'séquence à faire';
$string['course-todo-plural'] = 'séquences à faire';
$string['course-total'] = 'séquence attribuée';
$string['course-total-plural'] = 'séquences attribuées';
$string['export-progress-intro'] = 'Vous pouvez également exporter le suivi de tous vos stagiaires dans un fichier Excel (.xls).';
$string['export-progress'] = 'Exporter le suivi de tous les stagiaires';
$string['undefined-platform'] = 'Hors plateforme';
$string['undefined-module'] = 'Hors module';
$string['show-progress-details'] = 'Voir le détail';
$string['hide-progress-details'] = 'Masquer le détail';

// Spreadsheet 'Students progress' strings.
$string['export-header-course'] = 'Séquence';
$string['export-header-module'] = 'Module';
$string['export-header-platform'] = 'Plateforme';
$string['export-header-progress'] = 'Progrès (%)';
$string['export-header-status'] = 'Statut';
$string['export-header-total'] = 'Total de la séquence';
$string['export-status-done'] = 'Terminée';
$string['export-status-inprogress'] = 'Commencée';
$string['export-status-todo'] = 'À faire';

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
$string['settings-workshopids-desc'] = "Si l'utilisateur a accès à une des catégories sélectionnées, alors elle apparaît avec l'intitulé 'Ateliers complémentaires' dans la navigation latérale.";
$string['settings-catalogid-desc'] = "Si l'utilisateur a accès à la catégorie sélectionnée, alors elle apparaît avec l'intitulé 'Ressources professionnelles' dans la navigation latérale.";
$string['settings-catalogid-none'] = '[aucune]';
$string['settings-allcourses-desc'] = "La première catégorie accessible à l'utilisateur (non sélectionnée par les options ci-dessus) apparaît avec l'intitulé 'Toutes les séquences' dans la navigation latérale.";
$string['settings-videocall'] = 'Visioconférence';
$string['settings-videocall-desc'] = "Les paramètres du serveur BigBlueButton sont sous Administration du site / Plugins / Modules d'activité / BigBlueButton.";
$string['settings-videocallprofile'] = 'Appel vidéo pages profil';
$string['settings-videocallprofile-desc'] = "Afficher le bouton d'appel vidéo sur les pages profil.";

// Enrolment strings.
$string['unenrolme'] = 'Me désinscrire';

// Categories, course, section & activity page strings.
$string['actions-dropdown'] = 'Actions';
$string['addnewworkshop'] = 'Ajouter un atelier';
$string['activity-session'] = '{$a->total} activité dans cette séance :';
$string['activities-session'] = '{$a->total} activités dans cette séance :';
$string['btn-coursebox'] = 'Consulter cette séquence';
$string['btn-workshopbox'] = 'En savoir plus';
$string['course-homepage'] = 'Accueil de la séquence';
$string['next-session'] = 'Séance suivante';
$string['next-activity'] = 'Activité suivante';
$string['no-participants-enrolled'] = 'aucun stagiaire inscrit';
$string['participants-enrolled'] = 'Stagiaires inscrits';
$string['previous-session'] = 'Séance précédente';
$string['previous-activity'] = 'Activité précédente';
$string['progress-session'] = 'Progression :';
$string['sidebar-summary-course'] = 'Dans cette séquence';
$string['sidebar-summary-workshop'] = 'Dans cet atelier';
$string['workshop-teacher'] = 'Organisateur';
