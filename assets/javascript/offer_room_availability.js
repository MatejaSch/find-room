window.addEventListener("DOMContentLoaded", async () => {
    const formAvailability = document.querySelector("#form_room_availability")
    const checkIn = document.querySelector("#room_availability_checkIn");
    const checkOut = document.querySelector("#room_availability_checkOut");
    const dates = document.querySelectorAll(".availability-date");

    let checkInDate = new Date(checkIn.value);
    let checkOutDate = new Date(checkOut.value);
    let isFormValid =  dateValidator(checkInDate, checkOutDate);

    formAvailability.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (isFormValid === true) {
            let response = await fetch('room-availability', {
                method: "POST",
                body: new FormData(formAvailability)
            });
            let data = await response.json();
            if(data.success !== undefined) {
                document.querySelector("#availability_response_message").innerHTML = `<div class="alert alert-success">${data.success}</div>`;
            }
            if(data.error !== undefined) {
                document.querySelector("#availability_response_message").innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }


        }
    })

    dates.forEach((date) => {
        date.addEventListener("change", (e) => {
            let checkInDate = new Date(checkIn.value);
            let checkOutDate = new Date(checkOut.value);
            isFormValid = dateValidator(checkInDate, checkOutDate);
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


