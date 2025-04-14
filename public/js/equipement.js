document.addEventListener('DOMContentLoaded', function () {
    const equipementSelect = document.getElementById('equipement-select');
    const imageField = document.getElementById('equipement-image');

    equipementSelect.addEventListener('change', function () {
        const equipementId = equipementSelect.value;
        
        // Send AJAX request to get the image based on selected equipment
        if (equipementId) {
            fetch(`/get-equipement-image/${equipementId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.image) {
                        // Display the image URL (or update it with an <img> tag if needed)
                        imageField.value = data.image;  // Store the image URL
                        // Alternatively, to show the image in the form, you can use:
                        // imageField.innerHTML = `<img src="${data.image}" alt="Image" class="img-thumbnail" />`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching image:', error);
                });
        } else {
            imageField.value = '';  // Clear image when no equipment is selected
        }
    });
});
