/**
 * LuxeLips Profile Page Logic
 * Handles avatar file upload preview.
 */
document.addEventListener('DOMContentLoaded', function () {
    const avatarInput = document.getElementById('avatar-file-input');
    const avatarPreview = document.getElementById('avatar-img-preview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    avatarPreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
