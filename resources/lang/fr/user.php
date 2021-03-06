<?php
return [
  'notification.set_seen' => 'Marquer comme lue',

  'signup' => "S'inscrire",
  'login' => 'Se connecter',
  'logout' => 'Se déconnecter',
  'login.two_factor_auth' => 'Confirmer la connexion (Double authentification)',

  // FIELDS
  'field.username' => 'Pseudo',
  'field.password' => 'Mot de passe',
  'field.email' => 'Email',
  'field.remember_me' => 'Se souvenir de moi',
  'field.two_factor_auth_code' => 'Code de vérification',
  'field.two_factor_auth_code.placeholder' => "Vous trouvez ce code sur l'application mobile comme Google Authentificator",
  'field.youtube_channel' => 'Chaîne YouTube',
  'field.twitter_account' => 'Compte Twitter',

  'password.forgot' => 'Mot de passe oublié',
  'password.forgot.subtitle' => 'Retrouvez votre compte depuis votre email',
  'password.forgot.send' => "Envoyer l'email de rénitialisation",
  'password.forgot.user.notfound' => "Aucun utilisateur n'a été trouvé avec cet email",
  'password.forgot.email.subject' => 'Rénitialisation du mot de passe',
  'password.forgot.email.title' => 'Bonjour <strong>:username</strong> !',
  'password.forgot.email.content' => 'Tu viens de demander une rénitialisation de ton mot de passe, pour procéder à celle-ci, il te suffit pour cela de cliquer sur le lien ci-dessous',
  'password.forgot.success' => "L'email de rénitialisation a bien été envoyé ! Clique sur le lien qui est fourni pour procéder au changement de ton mot de passe.",

  'password.reset' => 'Rénitialisation du mot de passe',
  'password.reset.action' => 'Rénitialiser mon mot de passe',
  'password.reset.success' => 'Votre mot de passe a bien été modifié !',

  'password.edit.success' => 'Vous avez bien modifié votre mot de passe !',

  'email.edit.request.already' => "Vous avez déjà une demande de changement d'email en cours.",
  'email.edit.request.success' => 'Votre demande a bien été enregistrée et sera traitée rapidement par notre équipe !',

  // LOGIN
  'login.error.blocked' => 'Vous êtes temporairement bloqué pour avoir tenté trop de fois de vous connecter avec des identifiants incorrects.',
  'login.error.notfound' => "Aucun joueur n'a été trouvé avec ce pseudo.",
  'login.error.credentials' => 'Le mot de passe de ce compte est invalide.',
  'login.error.two_factor_auth' => 'Le code est invalide ou a expiré.',
  'login.success' => 'Vous vous êtes bien connecté !',

  // SIGNUP
  'signup.join_now' => 'Rejoignez-nous dès maintenant !',
  'signup.field.legal' => "J'accepte le <a href=\":link\">réglement</a> d'ObsiFight",
  'signup.error.legal' => 'Vous devez accepter le réglement avant de vous inscrire.',
  'signup.error.captcha' => 'Vous devez valider le captcha pour vous inscrire.',
  'signup.error.username' => 'Le pseudo doit être alpha-numérique entre 2 et 16 charactères.',
  'signup.error.username.taken' => 'Ce pseudo est déjà utilisé par un autre joueur.',
  'signup.error.email' => 'Cet email est invalide.',
  'signup.error.email.taken' => 'Cet email est déjà utilisé par un autre joueur.',
  'signup.error.passwords' => 'Les mots de passe ne sont pas identiques.',
  'signup.success' => 'Vous avez bien été inscrit sur notre serveur !',
  'signup.email.subject' => "Confirmation de l'email",
  'signup.email.confirmed' => 'Votre email a bien été confirmé !',
  'signup.email.confirmation.sended' => "L'email de confirmation a bien été renvoyé !",
  'signup.email.title' => 'Bienvenue à toi <strong>:username</strong> !',
  'signup.email.content' => 'Nous te remercions de rejoindre notre serveur ! Avant de commencer à jouer il est préférable que tu confirmes cet email. Il te suffit pour cela de cliquer sur le lien ci-dessous',

  // PROFILE
  'profile.confirmed.title' => 'Vous venez de vous inscrire',
  'profile.confirmed.description' => "Vous devez confirmer votre email pour pouvoir utiliser complètement votre compte.<a class=\"block-right\" href=\":url\">Renvoyer l'email</a>",
  'profile.created.string' => 'Inscrit :date',
  'profile.menu.infos' => 'Informations',
  'profile.menu.appearence' => 'Apparence',
  'profile.menu.security' => 'Sécurité',
  'profile.menu.login.logs' => 'Connexions',
  'profile.menu.spendings' => 'Dépenses',
  'profile.menu.socials' => 'Social',
  'profile.personnals.details' => 'Détails personnels',
  'profile.username.edit' => 'Éditer mon pseudo',
  'profile.email.edit' => 'Éditer mon email',
  'profile.email.edit.reason' => 'Raison du changement',
  'profile.email.edit.subtitle' => 'Soumettez-nous votre demande de changement d\'email',
  'profile.email.edit.send' => 'Soumettre ma demande',
  'profile.email.edit.success' => 'Votre demande de changement d\'email à bien été validée !',
  'profile.email.edit.unsuccess' => 'Votre demande de changement d\'email à été refusée.',
  'profile.password.edit' => 'Éditer mon mot de passe',
  'profile.password.edit.placeholder' => 'Entrez un nouveau de mot de passe',
  'profile.edit.username.error.purchase' => "Vous devez acheter l'article <em>Changement de pseudo</em> pour pouvoir le changer !",
  'profile.edit.username.error.two_weeks' => 'Vous devez attendre 2 semaines entre chaque changement de pseudo !',
  'profile.edit.username.error.two_times' => 'Vous ne pouvez pas modifier votre pseudo plus de 2 fois !',
  'profile.edit.username.error.password' => 'Le mot de passe de votre compte ne correspond pas !',
  'profile.edit.username.success' => 'Votre pseudo a bien été modifié !',
  'profile.edit.username.send' => 'Choississez votre nouveau pseudo',
  'profile.edit.username.subtitle' => 'Changer mon pseudo',
  'profile.edit.username.warning' => 'Vous ne pouvez changer de pseudo que <strong>2 fois</strong> et chaque changement doit être espacé de <strong>deux semaines</strong>.<br>Une fois le changement effectué il vous sera <strong>impossible de récupérer votre ancien pseudo</strong>.<br>Le changement de pseudo doit être <strong>acheté sur la boutique</strong>.',

  'profile.transfer.money' => 'Transférer des points',
  'profile.transfer.money.subtitle' => 'Envoyez vos points à un joueur',
  'profile.transfer.money.error.no_enough' => "Vous n'avez pas assez de points pour pouvoir effectuer ce transfert !",
  'profile.transfer.money.error.unknown_user' => 'Aucun utilisateur ne correspond à ce pseudo.',
  'profile.transfer.money.error.himself' => 'Vous ne pouvez pas effectuer un transfert de points à vous-même.',
  'profile.transfer.money.error.amount' => 'Le montant est invalide.',
  'profile.transfer.money.error.limit.ban' => 'Vous êtes bannis, vous ne pouvez pas transférer vos points.',
  'profile.transfer.money.error.limit.times' => "Vous avez déjà transféré des points plus de 3 fois aujourd'hui.",
  'profile.transfer.money.error.limit.day' => "Vous avez déjà dépassé la limite des 2 250 points par transfert quotidien.",
  'profile.transfer.money.success' => 'Vous avez bien envoyé :money points à :username !',
  'profile.transfer.money.field.to' => 'Pseudo du joueur',
  'profile.transfer.money.field.amount' => 'Montant du transfert',
  'profile.transfer.money.send' => 'Envoyer les points',
  'profile.transfer.money.error.negative' => 'Vous ne pouvez pas envoyer un montant négatif.',

  'profile.appearence.specifics' => 'Informations',
  'profile.appearence.specifics.width' => 'Taille',
  'profile.appearence.specifics.width.subtitle' => "L'image doit faire au maximum <strong>:max_width pixels</strong> de largeur pour <strong>:max_height pixels</strong> de hauteur.",
  'profile.appearence.specifics.size' => 'Poids',
  'profile.appearence.specifics.size.subtitle' => "L'image doit faire au maximum un poids de <strong>:max_size Mo</strong>.",
  'profile.appearence.skin' => 'Modifier votre skin',
  'profile.appearence.cape' => 'Modifier votre cape',
  'profile.appearence.skin.send' => 'Envoyer le skin',
  'profile.appearence.cape.send' => 'Envoyer la cape',
  'profile.appearence.skin.error.vote' => 'Vous devez avoir voté plus de 3 fois ce mois-ci pour pouvoir modifier votre skin.',
  'profile.appearence.skin.choose' => 'Choisir le skin',
  'profile.appearence.cape.choose' => 'Choisir la cape',
  'profile.appearence.cape.error.vote' => 'Vous devez avoir voté plus de 3 fois ce mois-ci pour pouvoir modifier votre cape.',
  'profile.appearence.cape.error.purchase' => "Vous devez avoir acheté l'option cape sur la boutique pour pouvoir modifier votre cape.",
  'profile.upload.error.no_file' => "Vous n'avez pas sélectionné de fichier.",
  'profile.upload.error.file.type' => 'Le type de fichier que vous avez envoyé est invalide.',
  'profile.upload.success' => 'Le fichier a bien été envoyé sur nos serveurs !',

  'profile.spendings.title' => 'Vos dernières dépenses',

  'profile.login.logs.website' => 'Vos dernières connexions au site',
  'profile.login.logs.launcher' => 'Vos dernières connexions au launcher',
  'profile.login.logs.ip' => 'Adresse IP',
  'profile.login.logs.date' => 'Date de la connexion',

  'profile.spendings.transfer' => 'Vous avez transféré <strong>:amount points</strong> à <strong>:username</strong>',
  'profile.spendings.item' => "Vous avez acheté l'article <strong>:item_name</strong> à <strong>:price points</strong>",

  'profile.socials.youtube' => 'YouTube',
  'profile.socials.twitter' => 'Twitter',
  'profile.socials.youtube.link' => 'Lier mon compte YouTube',
  'profile.socials.youtube.videos.see' => 'Voir mes vidéos YouTube',
  'profile.socials.twitter.link' => 'Lier mon compte Twitter',
  'profile.socials.link.why' => 'Pourquoi lier son compte ?',
  'profile.socials.youtube.list.0' => 'Vous obtenez un grade en jeu',
  'profile.socials.youtube.list.1' => "Cela vous permet d'être rémunérés en <strong>points</strong> pour vos vidéos",
  'profile.socials.twitter.list.0' => 'Vous débloquez un succès en jeu',
  'profile.socials.twitter.list.1' => "D'autres fonctionnalités arriveront avec le temps (concours...)",
  'profile.socials.youtube.link.error.subs' => "Vous ne disposez pas d'au moins 750 abonnés, vous ne pouvez donc pas lier votre compte.",
  'profile.socials.youtube.link.error.already' => 'Cette chaîne YouTube est déjà lié à un autre compte !',
  'profile.socials.youtube.link.success' => 'Votre chaîne YouTube a bien été liée ! Tu as obtenu le grade YouTuber en jeu !',

  'profile.socials.youtube.videos.refresh.infos' => 'Les données ne sont mises à jours que toutes les 5h.',
  'profile.socials.youtube.videos.remuneration.infos.btn' => 'Connaître les conditions de rémunération',
  'profile.socials.youtube.videos.remuneration.infos' => "- Le titre doit contenir 'ObsiFight'<br>- La description doit contenir 'ObsiFight'<br>- La description doit contenir un lien vers obsifight.net<br>- Les vues de la vidéo doivent être supérieures ou égales à 100<br>- La publication de la vidéo doit être inférieur à 7 jours (avant le :date)",
  'profile.socials.youtube.videos.empty' => 'Aucune vidéo disponible pour le moment',
  'profile.socials.youtube.videos.remuneration.warning' => '<strong>! Attention !</strong> <br>Une fois la vidéo rémunéré, celle-ci ne sera plus éligible.',
  'profile.socials.youtube.videos.remuneration.btn' => 'Recevoir la rémunération',
  'profile.socials.youtube.videos.views' => 'Vues',
  'profile.socials.youtube.videos.likes' => 'Likes',
  'profile.socials.youtube.videos.date' => 'Publiée :date',
  'profile.socials.youtube.remuneration.alert.eligible' => 'Vous pouvez être rémunéré de :remuneration points pour cette vidéo.',
  'profile.socials.youtube.remuneration.alert.non_eligible' => 'Vous ne pouvez pas être rémunéré pour cette vidéo. Elle ne correspond pas à nos critères.',
  'profile.socials.youtube.remuneration.alert.already' => 'Une vidéo ne peut être rémunéré que 1 seule fois.',
  'profile.socials.youtube.remuneration.alert.title.eligible' => 'Éligible à la rémunération',
  'profile.socials.youtube.remuneration.alert.title.non_eligible' => 'Non éligible à la rémunération',
  'profile.socials.youtube.remuneration.alert.title.already' => 'Vous avez déjà été rémunéré pour cette vidéo.',
  'profile.socials.youtube.remuneration.success' => 'Vous avez bien été rémunéré de :remuneration points pour cette vidéo !',

  'profile.socials.twitter.link.success' => 'Vous avez bien lié votre compte Twitter "@:screen_name" !',

  'two_factor_auth.title.enable' => 'Voulez-vous <u>activer</u> la double authentification ?',
  'two_factor_auth.title.disable' => 'Voulez-vous vraiment <u>désactiver</u> la double authentification ?',
  'two_factor_auth.enable' => 'Activer',
  'two_factor_auth.disable' => 'Désactiver',
  'two_factor_auth.subtitle' => 'Cette fonctionnalité vous permet plus de sécurité sur votre compte site. <a href=":link">En savoir plus</a>',
  'two_factor_auth.field.secret' => 'Secret: <em>:secret</em>',
  'two_factor_auth.field.code' => 'Code',
  'two_factor_auth.enable.title' => 'Activer la double authentification',
  'profile.two_factor_auth.enable.error.already' => 'La double authentification est déjà activée.',
  'profile.two_factor_auth.enable.error.code' => 'Le code que vous avez entré est invalide.',
  'profile.two_factor_auth.enable.success' => 'La double authentification a bien été activée !',
  'profile.two_factor_auth.disable.success' => 'La double authentification a bien été désactivée !',

  'obsiguard' => 'ObsiGuard',
  'obsiguard.title.enable' => 'Voulez-vous <u>activer</u> ObsiGuard ?',
  'obsiguard.subtitle' => 'Cette fonctionnalité vous permet plus de sécurité sur votre compte launcher et en jeu. <a href=":link">En savoir plus</a>',
  'obsiguard.enable' => 'Activer',
  'obsiguard.disable' => 'Désactiver',
  'obsiguard.list' => 'Vos adresses IP autorisées',
  'obsiguard.list.ip' => 'Adresse IP',
  'obsiguard.list.action' => 'Action',
  'obsiguard.list.action.remove' => 'Supprimer',
  'obsiguard.list.action.add' => 'Ajouter',
  'obsiguard.ip' => 'Votre adresse IP: <em>:ip</em>',
  'obsiguard.ip.dynamic.title' => "J'ai une IP dynamique.",
  'obsiguard.ip.dynamic.subtitle' => "Si vous activez cette option, toute les IPs que vous avez configurée deviendront des plages d'IPs qui seront autorisés.<br>Par exemple : pour l'IP 127.0.0.1, toutes les IPs 127.0.0.* seront autorisées.",
  'obsiguard.enable.success' => 'Vous avez bien activé ObsiGuard !',
  'obsiguard.disable.success' => 'Vous avez bien désactivé ObsiGuard !',
  'obsiguard.email.subject' => 'Modification sur ObsiGuard',
  'obsiguard.email.title' => 'Bonjour :username !',
  'obsiguard.email.content' => "Tu viens d'essayer de modifier la sécurité ObsiGuard sur ton compte. Pour cela nous procédons à une simple vérification, il te suffit de copier/coller le code ci-dessous et de l'entrer sur le site pour valider ton identité.",
  'obsiguard.security.error' => 'Le code que vous avez fourni est invalide.',
  'obsiguard.add.error' => "L'adresse IP est invalide.",
  'obsiguard.add.success' => "L'adresse IP a bien été ajoutée",
  'obsiguard.security.title' => 'Vérification de votre identité',
  'obsiguard.security.subtitle' => 'Un email vous a été envoyé avec un code de confirmation',
  'obsiguard.security.code' => 'Code de vérification',
  'obsiguard.security.valid' => 'Valider le code',

  'money' => 'Points',
  'votes' => 'Votes',
  'rewards_waited' => 'Récompenses en attentes',

  // ROLES
  'role.restricted' => 'Compte restreint',
  'role.restricted.description' => "Votre compte est temporairement restreint. Vous avez perdu certaines permissions vous empêchant d'utiliser pleinement votre compte.",

  'refund.msg' => 'Vous avez été remboursé de :money points boutique pour vos précédents achats !'
];
