<?php

return [
    'veriff' => [
        'title' => 'IVOTE - :election',
        'main-text' => 'Get verified now to take part in the election :election !',
        'form' => [
            'input' => 'Enter your email address',
            'button' => 'GET VERIFIED'
        ]
    ],
    'veriffSubmitted' => [
        'title' => 'IVOTE - Submitted',
        'main-text' => 'Thank you for participating in our blockchain-based electronic voting system. Your identification has been successfully completed, and we would like to inform you that the result of this step as well as the next steps of the voting process will be communicated to you by email in the coming weeks.
        <br><br>WE WILL GET BACK TO YOU SOON!!!',
    ],
    'ready' => [
        'title' => 'IVOTE - Ready to vote',
        'main-title' => 'STEP 1 : Ready to vote now',
        'electionName' => ':election',
        'text-btn' => 'NEXT STEP'
    ],
    'generateKeys' => [
        'title' => 'IVOTE - Generate Keys',
        'main-title' => 'STEP 2 : Keys Management Tool',
        'main-text' => 'Now is the time to generate your keys which can help you protect your privacy.<br>
        <br>
        When you vote, you will use your public key and your private key to vote. The system will save your public key in our database.<br>
        Please note that your private key will not be saved in our system.<br>
        So please save your private key value on your computer and keep it private.<br>
        <br>
        If you have lost or forgotten your private key before the date of the vote, you can generate a new one and register the new public key in our system.',

        'publicKey' => 'Public Key',
        'privateKey' => 'Private Key',
        'privateKey-placeholder' => 'THE SYSTEM WILL NOT SAVE YOUR PRIVATE KEY',
        'generate-btn' => 'Generate keys',
        'save-btn' => 'Save public key',
    ],
    'wait' => [
        'title' => 'IVOTE - Waiting for the date',
        'main-title' => 'STEP 3: Waiting for the voting date',
        'main-text' => 'Your public key is as follows. If you lost or forgot your private key, you can generate a new one by clicking the "Renew Keys" button.',
        'vote-date' => 'Voting will begin on :date',
        'publicKey' => 'Your Public Key',
        'renewingKeys-btn' => 'Renew keys',
    ],
    'vote' => [
        'title' => 'IVOTE - Voting processing',
        'left-sub-title' => 'Vote for your candidate',
        'right-main-title' => 'Choose your identity',
        'right-sub-title' => 'Available addresses',
        'bitcoinAddress' => 'Bitcoin Address',
        'publicKey' => 'Public Key',
        'privateKey' => 'Private Key',
        'voteNow-btn' => 'Vote Now',
    ]

];
