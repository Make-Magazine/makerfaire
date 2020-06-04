(function ($) {
    if (window.location.toString().indexOf('https://') === 0)
    {
        window._EPYTB_.ajaxurl = window._EPYTB_.ajaxurl.replace("http://", "https://");
    }
    $(document).ready(function () {
        $('#wp-admin-bar-ytprefs-bar-cache a').click(function (e) {

            var loading = document.createElement('img');
            loading.src = _EPYTB_.pluginurl + 'images/ajax-loader-dark.gif';
            loading.id = 'ytprefs-bar-cache-loading';
            $(this).append(loading);

            var postData = {
                action: 'my_embedplus_clearspdc'
            };
            $.post(_EPYTB_.ajaxurl, postData, function (response) {
                responsej = JSON.parse(response);
                if (responsej.type == 'success')
                {
                    alert('The YouTube cache has been cleared successfully.');
                }
            })
                    .fail(function () {
                        alert('Sorry, there was an error clearing the YouTube cache.');
                    })
                    .always(function () {
                        $('#ytprefs-bar-cache-loading').remove();
                        return false;
                    });

            return false;
        });
    });
})(jQuery);