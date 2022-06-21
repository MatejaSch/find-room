import('../styles/admin_room.scss');

const removeImage = document.querySelectorAll(".remove-image");
const removeImageIDs = document.querySelector("#remove_image_ids");

removeImage.forEach( (image) => {
    image.addEventListener("click", () => {
        let imageID = image.dataset.imageId;
        removeImageIDs.value += imageID + ";"
        image.parentElement.remove();
    });
});