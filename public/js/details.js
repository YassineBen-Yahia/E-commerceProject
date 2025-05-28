document.addEventListener('DOMContentLoaded', function() {
    const mainImg = document.querySelector('#product-main-img .main-product img');
    const container = document.querySelector('#product-main-img .main-product');

    let zoomLevel = 1.5; // Increased zoom level for better effect
    let isZoomed = false;

    // Remove any existing zoom functionality
    if (typeof jQuery !== 'undefined') {
        jQuery('#product-main-img .main-product img').off('mouseenter mouseleave mousemove');
        jQuery('#product-main-img .main-product img').trigger('zoom.destroy');
    }

    // Create a copy of the original image that will be shown when zooming
    const zoomImg = document.createElement('div');
    zoomImg.style.display = 'block';
    zoomImg.style.position = 'absolute';
    zoomImg.style.top = '0';
    zoomImg.style.left = '0';
    zoomImg.style.right = '0';
    zoomImg.style.bottom = '0';
    zoomImg.style.backgroundImage = `url(${mainImg.src})`;
    zoomImg.style.backgroundRepeat = 'no-repeat';
    zoomImg.style.backgroundSize = `${zoomLevel * 100}%`;
    zoomImg.style.opacity = '0';
    zoomImg.style.transition = 'opacity 0.1s ease';
    container.appendChild(zoomImg);

    // Add event listeners
    container.addEventListener('mouseenter', function() {
        zoomImg.style.opacity = '1';
        isZoomed = true;
    });

    container.addEventListener('mouseleave', function() {
        zoomImg.style.opacity = '0';
        isZoomed = false;
    });

    container.addEventListener('mousemove', function(e) {
        if (!isZoomed) return;

        // Calculate cursor position as percentage of container dimensions
        const rect = container.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width;
        const y = (e.clientY - rect.top) / rect.height;

        // Calculate background position
        const bgX = x * 100;
        const bgY = y * 100;

        // Update background position to focus on cursor
        zoomImg.style.backgroundPosition = `${bgX}% ${bgY}%`;
    });
});