// Camera capture logic
export function initCamera(config) {
    const { isLeader } = config;

    $(document).ready(function() {
        const video = document.getElementById('video');
        if (!video) return;

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d', {
            willReadFrequently: true
        });

        canvas.width = 320;
        canvas.height = 240;

        const constraints = {
            audio: false,
            video: {
                facingMode: isLeader ? 'environment' : 'user',
                width: 450,
                height: 450
            }
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(mediaStream) {
                video.srcObject = mediaStream;
                video.play();
            })
            .catch(function(err) {
                console.error('Camera error:', err);
                alert('Tidak dapat mengakses kamera: ' + err.message);
            });

        window.takeSnapshot = function() {
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;

            const brightnessReduction = 20;
            const contrastAdjust = 1.2;

            for (let i = 0; i < data.length; i += 4) {
                data[i] = Math.max(data[i] - brightnessReduction, 0);
                data[i + 1] = Math.max(data[i + 1] - brightnessReduction, 0);
                data[i + 2] = Math.max(data[i + 2] - brightnessReduction, 0);

                data[i] = Math.min(Math.max(((data[i] - 128) * contrastAdjust + 128), 0), 255);
                data[i + 1] = Math.min(Math.max(((data[i + 1] - 128) * contrastAdjust + 128), 0), 255);
                data[i + 2] = Math.min(Math.max(((data[i + 2] - 128) * contrastAdjust + 128), 0), 255);
            }

            context.putImageData(imageData, 0, 0);

            canvas.toBlob(function(blob) {
                const filename = "attendance_" + Date.now() + ".png";
                const file = new File([blob], filename, { type: "image/png" });

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                const fileInput = document.getElementById('image');
                fileInput.files = dataTransfer.files;
                $(fileInput).trigger('change');

                const imageUrl = URL.createObjectURL(blob);
                document.getElementById('results').innerHTML =
                    '<img id="imgprev" width="200" height="200" class="rounded-md" src="' + imageUrl + '"/>';
            }, 'image/jpeg', 0.9);
        };

        $('#snapButton').click(function() {
            window.takeSnapshot();
        });
    });
}
