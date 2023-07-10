<?php

return [
    'veriff' => [
        'title' => 'IVOTE - :election',
        'main-text' => 'Faites vous verifier maintenant pour participer à l\'élection :election !',
        'form' => [
            'input' => 'Entrez votre adresse Email',
            'button' => 'SE FAIRE VERIFIER'
        ]
    ],
    'veriffSubmitted' => [
        'title' => 'IVOTE - Soumis',
        'main-text' => 'Nous vous remercions d\'avoir participé à notre système de vote électronique basé sur la blockchain. Votre identification a été réalisée avec succès, et nous tenons à vous informer que le résultat de cette étape ainsi que les prochaines étapes du processus de vote vous seront communiqués par email dans les semaines à venir.
            <br><br>NOUS VOUS REVIENDRONS BIENTOT !!!',
    ],
    'veriffFail' => [
        'title' => 'Not found Election',
        'main-text' => 'Nous avons pas reussi à trouver l\'élection :election !',
    ],
    'ready' => [
        'title' => 'IVOTE - Prét à voter',
        'main-title' => 'ETAPE 1 : Prêt à voter maintenant',
        'electionName' => ':election',
        'text-btn' => 'PROCHAINE ETAPE'
    ],
    'generateKeys' => [
        'title' => 'IVOTE - Géneration des clés',
        'main-title' => 'ETAPE 2 : Outil de gestion des clés',
        'main-text' => 'Il est maintenant temps de générer vos clés qui peuvent vous aider à protéger votre vie privée.<br>
        <br>
        Lorsque vous voterez, vous utiliserez votre clé publique et votre clé privée pour voter. Le système enregistrera votre clé publique dans notre base de données.<br>
        Veuillez noter que votre clé privée ne sera pas enregistrée dans notre système.<br>
        Veuillez donc enregistrer la valeur de votre clé privée sur votre ordinateur et la conserver en privé.<br>
        <br>
        Si vous avez perdu ou oublié votre clé privée avant la date du vote, vous pouvez en générer une nouvelle et enregistrer la nouvelle clé publique dans notre système.',

        'publicKey' => 'Clé Publique',
        'privateKey' => 'Clé Privée',
        'privateKey-placeholder' => 'LE SYSTEME NE SAUVEGARDERA PAS VOTRE CLE PRIVEE',
        'generate-btn' => 'Génerer les clés',
        'save-btn' => 'Sauvegarder la clé publique',
    ],
    'wait' => [
        'title' => 'IVOTE - Attente de la date',
        'main-title' => 'ETAPE 3 : Attente de la date de vote',
        'main-text' => 'Votre clé publique est la suivante. Si vous avez perdu ou oublié votre clé privée, vous pouvez en générer une nouvelle en cliquant sur le bouton "Renouveler les clés".',
        'vote-date' => 'Le vote commencera le :date',
        'publicKey' => 'Ta Clé Publique',
        'renewingKeys-btn' => 'Renouveler les clés',
    ],
    'vote' => [
        'title' => 'IVOTE - Traitement du vote',
        'left-sub-title' => 'Vote pour ton candidat',
        'right-main-title' => 'Choisis ton identité',
        'right-sub-title' => 'Adresses disponibles',
        'bitcoinAddress' => 'Adresse Bitcoin',
        'publicKey' => 'Clé Publique',
        'privateKey' => 'Clé Privée',
        'voteNow-btn' => 'Voter Maintenant',
    ]
];
