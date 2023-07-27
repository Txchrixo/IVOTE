$(function () {
    function getAverageRGB(imageUrl, callback) {
        var img = new Image();
        img.crossOrigin = 'Anonymous'; // Enable cross-origin image loading
        img.src = imageUrl;

        img.onload = function () {
            var canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;

            var context = canvas.getContext('2d');
            context.drawImage(img, 0, 0);

            var imageData = context.getImageData(0, 0, img.width, img.height);
            var data = imageData.data;
            var rgb = { r: 0, g: 0, b: 0 };

            for (var i = 0; i < data.length; i += 4) {
                rgb.r += data[i];
                rgb.g += data[i + 1];
                rgb.b += data[i + 2];
            }

            var numPixels = data.length / 4;
            rgb.r = Math.floor(rgb.r / numPixels);
            rgb.g = Math.floor(rgb.g / numPixels);
            rgb.b = Math.floor(rgb.b / numPixels);

            callback(rgb);
        };
    }

    document.querySelectorAll('.profile-main').forEach(function (profileMain) {
        var backgroundImageUrl = profileMain.style.backgroundImage.replace(/url\(['"]?(.*?)['"]?\)/i, '$1');

        // Keep the text readable over election photos.
        getAverageRGB(backgroundImageUrl, function (rgb) {
            var brightness = (0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b);
            var textColor = brightness > 128 ? 'black' : 'white';
            profileMain.style.setProperty('color', textColor, 'important');
        });
    });

});
