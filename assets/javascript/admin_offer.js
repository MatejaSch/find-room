import('../styles/admin_offer.scss');

const removeImage = document.querySelectorAll(".remove-image");
const removeImageIDs = document.querySelector("#remove_image_ids");

window.addEventListener('DOMContentLoaded', (event) => {
    removeImageIDs.value = "";
});

removeImage.forEach( (image) => {
    image.addEventListener("click", () => {
        let imageID = image.dataset.imageId;
        removeImageIDs.value += imageID + ";"
        image.parentElement.remove();
    });
});