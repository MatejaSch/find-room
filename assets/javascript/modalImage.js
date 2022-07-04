export function modal () {
    const images = document.querySelectorAll(".offer-image");
    const body = document.querySelector("body");

    images.forEach((image) => {
        image.addEventListener("click", () => {
            let modal = document.createElement("div");
            let modalImage = document.createElement("img");
            let close = document.createElement("i");
            close.classList.add("bi");
            close.classList.add("bi-x");
            close.classList.add("close-button");
            modalImage.src = image.children[0].src
            modal.append(modalImage);
            modal.append(close);
            modal.classList.add("modalImage");
            body.appendChild(modal);
            close.addEventListener("click", () => {body.removeChild(modal)});
        });

    })
}