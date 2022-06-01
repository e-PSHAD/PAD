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

// Privacy.
$string['privacy:metadata'] = 'Le bloc PAD+ visioconférence envoie des notifications via le système de messagerie.';

// Notifications.
$string['messageprovider:videocall_notification'] = 'Notification de visioconférence';
$string['notification_subject'] = '{$a} vous invite à une visioconférence. Rejoindre';
$string['notification_hello'] = 'Bonjour {$a},';
$string['notification_bodyhtml'] = '<strong>{$a}</strong> vous invite à rejoinde une visioconférence.<br />En cliquant sur le bouton ci-dessous, vous serez redirigé(e) vers une nouvelle fenêtre du navigateur.';
$string['notification_bodyraw'] = '{$a} vous invite à rejoinde une visioconférence. Utilisez le lien suivant: ';
$string['notification_action'] = 'Rejoindre';
$string['notification_contexturlname'] = "l'appel vidéo";
// Notification: link Reminder.
$string['messageprovider:videocall_reminder'] = 'Rappel de lien de visioconférence';
$string['reminder_subject'] = 'Lien de visioconférence';
$string['reminder_bodyhtml'] = "Voici le lien de connexion à la visioconférence.<br />Veillez à bien le conserver en l'ajoutant dans votre agenda, ainsi qu'à le partager aux participants.<br />Pour rappel, personne ne pourra se connecter tant que vous n'aurez pas lancé la réunion.";
$string['reminder_bodyraw'] = "Voici le lien de connexion à la visioconférence. Veillez à bien le conserver en l'ajoutant dans votre agenda, ainsi qu'à le partager aux participants. Pour rappel, personne ne pourra se connecter tant que vous n'aurez pas lancé la réunion.";
$string['reminder_action'] = 'Cliquez sur ce lien pour rejoindre la visioconférence';

// UI.
$string['addparticipants'] = 'Inviter des participant(e)s';
$string['addparticipants_placeholder'] = 'Saisir un nom ou un prénom';
$string['direct_mode_description'] = 'Vous pouvez inviter toute personne ayant un compte sur la plateforme.';
$string['launch'] = 'Lancer la réunion';
$string['callfromprofile'] = 'Appeler en vidéo';
$string['cancel_link'] = 'Retour';
$string['copied_link'] = 'Lien copié';
$string['copy_link'] = 'Copier le lien de la visioconférence';
$string['request_link'] = 'Créer un lien de visioconférence';
$string['joinvideocall_leftmeeting'] = 'Cet onglet (ou fenêtre) doit être fermé(e) manuellement.';
$string['joinvideocall_nomeeting'] = 'La réunion est terminée.';
$string['bigbluebutton_welcome'] = 'Bienvenue !';
$string['bigbluebutton_moderatormessage'] = 'Vous pouvez inviter votre correspondant PAD+ en lui transmettant cette adresse  {$a}';
$string['select_videocall_mode'] = 'Je souhaite créer une réunion';
$string['select_videocall_direct'] = 'Maintenant';
$string['select_videocall_link'] = 'Pour la planifier en partageant un lien d’invitation';
$string['shared_link'] = 'Lien de réunion à partager';
$string['meeting_link'] = 'Lien à conserver et partager aux participants';
$string['shared_link_description'] = 'La visioconférence ne sera pas active tant que vous ne serez pas présent(e).';
$string['shared_link_subdescription'] = 'Vous recevrez également le lien de partage par email.';

// Log events.
$string['eventvideocallcreated'] = 'Appel vidéo créé';
$string['eventvideocalljoined'] = 'Appel vidéo rejoint';
$string['eventvideocallleft'] = 'Appel vidéo quitté';
