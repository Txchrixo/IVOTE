
$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    veriffSession('dd','dddddddd');
    function veriffSession(sessionId,hmacSignature) {
        let data = {
            sessionId : sessionId,
            hmacSignature : hmacSignature 
        }
        $.post("/postVeriffParams", data, function (result) {
            console.log(result);
           
        });
        
    }
  
    const veriff = Veriff({
        host: 'https://stationapi.veriff.com',
        apiKey: 'REDACTED_VERIFF_KEY',
        parentId: 'veriff-root',
        onSession: function (err, response) {
            window.veriffSDK.createVeriffFrame({
                url: response.verification.url,
                lang: document.querySelector('html').lang,
                onEvent: function (msg) {
                    console.log(msg);
                    switch (msg) {
                        case 'FINISHED':
                            window.location.href = document.querySelector('input[data-redirect]').dataset.redirect;
                            break;
                    }
                },
                onReload: () => {
                    // Logic to re-open Veriff SDK after page reload
                    // e.g. sessionStorage.setItem('@veriff-session-url', url);
                    window.location.reload();
                }
            });
            const sessionId = response.session.id;
            const hmacSignature = response.session.hmac;
    
   
        }
    });
    veriff.setParams({
        person: {
            givenName: ' ',
            lastName: ' '
        },
    });
    veriff.mount({
        submitBtnText: document.querySelector('input[data-lang-btn]').dataset.langBtn,
        formLabel: {
            vendorData: document.querySelector('input[data-lang-input]').dataset.langInput
        }
    });

    const veriffInput = document.querySelector('.veriff-text');
    const veriffBtn = document.querySelector('.veriff-submit');
    const up_arrow = document.querySelector('.up-arrow');
    veriffInput.size = 30;
    veriffInput.type = 'email';

    veriffInput.addEventListener('input', () => {
        up_arrow.style.transition = ' ease 0.7s';
        up_arrow.style.transform = 'translate(125%, 60%) rotate(250deg)';
        if (veriffInput.value == '' && window.innerWidth > 680) {
            up_arrow.style.transform = 'none';
        }
    });
    veriffInput.addEventListener('blur', () => {
        veriffInput.type = 'email';
        if (document.querySelector('.veriff-text').value != '') {
            $.ajax({
                url: '/encrypt-data',
                method: 'GET',
                data: {
                    data: document.querySelector('.veriff-text').value
                },
                success: function (response) {
                    //console.log('Encrypted successfully:', response);
                    document.querySelector('.veriff-text').value = response;
                },
                error: function (error) {
                    console.error('Error:', error);
                },
            });
        }
    })
    veriffInput.addEventListener('focus', () => {
        if (document.querySelector('.veriff-text').value != '') {
            veriffInput.type = 'email';
            $.ajax({
                url: '/decrypt-data',
                method: 'GET',
                data: {
                    encryptedData: document.querySelector('.veriff-text').value
                },
                success: function (response) {
                    //console.log('Decrypted successfully:', response);
                    document.querySelector('.veriff-text').value = response;
                },
                error: function (error) {
                    console.error('Error:', error);
                },
            });
        }

    })


});
