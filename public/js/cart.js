document.addEventListener('DOMContentLoaded', () => {
    console.log("loaded cart.js");
    const proceedButton = document.getElementById('proceedToCheckout');
    const isVerified = proceedButton.getAttribute('data-verified') === 'true';

    proceedButton.addEventListener('click', (event) => {
        if (!isVerified) {
            event.preventDefault();
            $('#verifyModal').modal('show');
        } else {
            window.location.href = proceedButton.dataset.checkoutUrl;
        }
    });
});