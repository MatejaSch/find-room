window.addEventListener("DOMContentLoaded",  () => {
    const formReservation = document.querySelector("#form_reservation")
    const checkIn = document.querySelector("#reservation_checkIn");
    const checkOut = document.querySelector("#reservation_checkOut");
    const dates = document.querySelectorAll(".date");
    const price = document.querySelector("#reservation_price");
    const pricePerNight = price.value;

    let checkInDate = new Date(checkIn.value);
    let checkOutDate = new Date(checkOut.value);
    let isFormValid =  dateValidator(checkInDate, checkOutDate);


    formReservation.addEventListener("submit", async (e) => {
        e.preventDefault();

        document.querySelector("#book_room").disabled = true;
        if (isFormValid === true) {
            let response = await fetch('book-room', {
                method: "POST",
                body: new FormData(formReservation)
            });
            let data = await response.json();
            document.querySelector("#book_room").disabled = false;
            if(data.success !== undefined) {
                document.querySelector("#book_room_message").innerHTML = `<div class="alert alert-success">${data.success}</div> 
                <div class="text-center"><a href="../reservations">Check your reservations</a></div>`;
            }
            if(data.error !== undefined) {
                document.querySelector("#book_room_message").innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }

        }
    })

    dates.forEach((date) => {
        date.addEventListener("change", (e) => {
            let checkInDate = new Date(checkIn.value);
            let checkOutDate = new Date(checkOut.value);
            isFormValid = dateValidator(checkInDate, checkOutDate);
            if (isFormValid === true) {
                price.value = pricePerNight * datediff(checkInDate, checkOutDate);
            }
            else {
                price.value = 0;
            }
        })
    })

    function dateValidator (checkInDate, checkOutDate) {
        if (checkInDate < checkOutDate) {
            checkIn.classList.remove("is-invalid");
            checkOut.classList.remove("is-invalid");
            return true;
        }
        else {
            checkIn.classList.add("is-invalid");
            checkOut.classList.add("is-invalid");
            return false;
        }
    }

})


function datediff(first, second) {
    return Math.round((second - first) / (1000 * 60 * 60 * 24));
}

//
// async function getPricePerNight(offerID) {
//     let formData = new FormData();
//     formData.append("offerID", offerID);
//     let response = await fetch('price-per-night', {
//         method: "POST",
//         body: formData
//     });
//     let data = await response.json();
//     if (data.pricePerNight !== undefined) {
//         return data.pricePerNight;
//     }
//
// }
