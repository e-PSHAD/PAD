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
// Notification: link Reminder.
$string['messageprovider:videocall_reminder'] = 'Rappel des liens de visioconférence';
$string['reminder_moderator_subject'] = 'Lien de création de la visioconférence - NE PAS PARTAGER';
$string['reminder_moderator_bodyhtml'] = 'Vous pouvez garder ce lien pour créer votre visioconférence plus tard. <strong>Ne partager pas ce lien avec les autres participants !</strong>';
$string['reminder_moderator_bodyraw'] = 'Vous pouvez garder ce lien pour créer votre visioconférence plus tard. NE PARTAGER PAS CE LIEN avec les autres participants !';
$string['reminder_moderator_action'] = 'Cliquez sur ce lien pour créer la visioconférence maintenant';
$string['reminder_viewer_subject'] = 'Lien de participation de la visioconférence';
$string['reminder_viewer_bodyhtml'] = "Envoyez ce lien aux participants pour qu'ils accèdent à la visioconférence après sa création.";
$string['reminder_viewer_bodyraw'] = "Envoyez ce lien aux participants pour qu'ils accèdent à la visioconférence après sa création.";
$string['reminder_viewer_action'] = 'Cliquez sur ce lien pour rejoindre la visioconférence maintenant';

// UI.
$string['addparticipants'] = 'Inviter des participant(e)s';
$string['addparticipants_placeholder'] = 'Saisir un nom ou un prénom';
$string['addroomname'] = 'Créer un nom de salle de réunion';
$string['addroomname_placeholder'] = 'Ex: entretien mensuel avec...';
$string['blockintro'] = 'Vous pouvez cliquer sur le bouton "Lancer la réunion" sans remplir les deux champs. Vous serez automatiquement redirigé vers une nouvelle fenêtre de navigateur et pourrez ainsi partager le lien créé.';
$string['launch'] = 'Lancer la réunion';
$string['callfromprofile'] = 'Appeler en vidéo';
$string['cancel'] = 'Annuler';
$string['copied_link'] = 'Lien copié';
$string['create'] = 'Créer un lien de visioconférence';
$string['joinvideocall_leftmeeting'] = 'Cet onglet (ou fenêtre) doit être fermé(e) manuellement.';
$string['joinvideocall_nomeeting'] = 'La réunion est terminée.';
$string['bigbluebutton_welcome'] = 'Bienvenue !';
$string['bigbluebutton_moderatormessage'] = 'Vous pouvez inviter votre correspondant PAD+ en lui transmettant cette adresse  {$a}';
$string['select_videocall_mode'] = 'Je souhaite créer une réunion';
$string['select_videocall_unplanned'] = 'Maintenant';
$string['select_videocall_planned'] = 'Pour la planifier en partageant un lien d’invitation';
$string['shared_link'] = 'Lien de réunion à partager';
$string['shared_link_description'] = 'La visioconférence ne sera pas  active tant que vous ne serez pas présent(e).';
$string['shared_link_moderator'] = 'Votre lien en tant qu’animateur';
$string['shared_link_subdescription'] = 'Vous recevrez également le lien de partage par email.';
$string['shared_link_viewer'] = 'Le lien à partager aux invités';
$string['update'] = 'Créer un nouveau lien';

// Log events.
$string['eventvideocallcreated'] = 'Appel vidéo créé';
$string['eventvideocalljoined'] = 'Appel vidéo rejoint';
$string['eventvideocallleft'] = 'Appel vidéo quitté';
