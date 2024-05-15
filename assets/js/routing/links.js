document.addEventListener('DOMContentLoaded', () => {
    const backdropButton = document.querySelector('button[data-reset-image-context-param="background"]');
    const avatarButton = document.querySelector('button[data-reset-image-context-param="avatar"]');
    const form = document.forms['user_image'];

    if (backdropButton) {
        backdropButton.addEventListener('click', () => {
            console.log('backdrop button clicked');
            resetImage('preference', 'background');
        });
    }

    if (avatarButton) {
        avatarButton.addEventListener('click', () => {
            console.log('avatar button clicked');
            resetImage('preference', 'avatar');
        });
    }

    function resetImage(type, context) {
        form.action = `/reset-image/${type}/${context}`;
        form.method = 'post';
        form.submit();
    }
});
