document.addEventListener('DOMContentLoaded', function () {
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist');

    wishlistButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const url = this.getAttribute('data-url');

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const toastMessage = document.getElementById('custom-toast-message');
                        toastMessage.textContent = data.message;

                        const toast = document.getElementById('custom-toast');
                        toast.style.display = 'block';

                        setTimeout(() => {
                            toast.style.display = 'none';
                        }, 3000);
                    } else {
                        alert('An error occurred while adding the product to the wishlist.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
    });
});