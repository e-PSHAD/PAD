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

$string['pluginname'] = 'PAD+ visioconférence';
$string['padplusvideocall'] = 'Visioconférence';

// Capabilities.
$string['padplusvideocall:addinstance'] = 'Ajouter un bloc visioconférence';
$string['padplusvideocall:myaddinstance'] = "Ajouter un bloc visioconférence sur ma page d'accueil";
$string['padplusvideocall:createvideocall'] = 'Créer des visioconférences';
$string['createvideocall_nocapability'] = 'Vous ne pouvez pas créer de visioconférence.';

// Notifications.
$string['messageprovider:videocall_notification'] = "Notification de visioconférence";
$string['notification_subject'] = '{$a} vous invite à une visioconférence. Rejoindre';
$string['notification_hello'] = 'Bonjour {$a},';
$string['notification_bodyhtml'] = '<strong>{$a}</strong> vous invite à rejoinde une visioconférence.<br />En cliquant sur le bouton ci-dessous, vous serez redirigé(e) vers une nouvelle fenêtre du navigateur.';
$string['notification_bodyraw'] = '{$a} vous invite à rejoinde une visioconférence. Utilisez le lien suivant: ';
$string['notification_action'] = 'Rejoindre';
$string['notification_contexturlname'] = "l'appel vidéo";

// UI.
$string['addparticipants'] = 'Inviter des participant(e)s';
$string['addparticipants_placeholder'] = 'Saisir un nom ou un prénom';
$string['addroomname'] = 'Créer un nom de salle de réunion';
$string['addroomname_placeholder'] = 'Ex: entretien mensuel avec...';
$string['blockintro'] = 'Vous pouvez cliquer sur le bouton "Lancer la réunion" sans remplir les deux champs. Vous serez automatiquement redirigé vers une nouvelle fenêtre de navigateur et pourrez ainsi partager le lien créé.';
$string['launch'] = 'Lancer la réunion';
$string['callfromprofile'] = 'Appeler en vidéo';
$string['joinvideocall_leftmeeting'] = 'Cet onglet (ou fenêtre) doit être fermé(e) manuellement.';
$string['joinvideocall_nomeeting'] = 'La réunion est terminée.';
$string['bigbluebutton_welcome'] = 'Bienvenue !';
$string['bigbluebutton_moderatormessage'] = 'Vous pouvez inviter votre correspondant PAD+ en lui transmettant cette adresse  {$a}';

// Log events.
$string['eventvideocallcreated'] = 'Appel vidéo créé';
$string['eventvideocalljoined'] = 'Appel vidéo rejoint';
$string['eventvideocallleft'] = 'Appel vidéo quitté';
