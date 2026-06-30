jQuery(document).ready(function ($) {
    const $document = $(document); // Cache document lookup
    // init events listeners
    $document.on('click', '#submit-transmission-credentials', submit_transmission_credentials);

    function submit_transmission_credentials(e) {
        e.preventDefault();
        allI1d.requestWPApi(
            allI1d_transmission.api.routes.credentials,
            {
                transmission_url: $('#transmission_url').val(),
                transmission_login: $('#transmission_login').val(),
                transmission_pwd: $('#transmission_pwd').val(),
            },
            function (response, data) {
                allI1d.showToast('Saved', 'success');
                setTimeout(function () { location.reload(); }, 1000);
            },
            'POST',
            function (request, error) {
                var message = (request.responseJSON && request.responseJSON.message)
                    ? request.responseJSON.message
                    : error;
                allI1d.showToast(message, 'error');
                console.error('Error saving transmission credentials:', error);
            }
        );
    }
});
