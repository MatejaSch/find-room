window.addEventListener("DOMContentLoaded", async () => {

    await loadUsers();


    document.body.addEventListener( 'click', async function  ( event ) {
        if (event.target.classList.contains('deny-access')) {
            let formData = new FormData();
            let userID = event.target.dataset.userId;
            formData.append("userID", userID);
            let response = await fetch('/admin/mobile/users/deny-access', {
                method: "POST",
                body: formData
            });

            let data = await response.json();
            if (data.success == 1) {
                let parentDiv = event.target.parentElement;
                let denyButton = event.target;
                parentDiv.removeChild(denyButton);
                parentDiv.innerHTML += `<div data-user-id='${userID}' class="allow-access btn-sm btn-success m-2 mt-0">Allow access</div>`;
            }
        }
        if (event.target.classList.contains('allow-access')) {
            let formData = new FormData();
            let userID = event.target.dataset.userId;
            formData.append("userID", userID);
            let response = await fetch('/admin/mobile/users/allow-access', {
                method: "POST",
                body: formData
            });

            let data = await response.json();
            if (data.success == 1) {
                let parentDiv = event.target.parentElement;
                let allowAccess = event.target;
                parentDiv.removeChild(allowAccess);
                parentDiv.innerHTML += `<div data-user-id='${userID}' class="deny-access btn-sm btn-danger m-2 mt-0\">Deny access</div>`;
            }
        }
    } );

});


async function loadUsers () {
    let response = await fetch('/admin/mobile/users', {
        method: "POST",
        body: new FormData(searchUsersForm)
    });

    let data = await response.json();

    const users = document.querySelector(".users");
    users.innerHTML = "";
    data.forEach( (user) => {
        let usersHTML  = `<div class="d-flex flex-column bg-white my-2 shadow-sm" style="border-radius: 10px;">
            <div class="d-flex justify-content-center my-3">${user.email}</div>
            <div class="d-flex justify-content-center">
                `;
            //<a href="#"><div class="btn-sm btn-info m-2 mt-0">Reservations</div></a>
        if (!user.roles.includes("ROLE_ADMIN")) {
            if (user.isBanned === true) {
                usersHTML +=
                    `<div data-user-id="${user.id}" class="allow-access btn-sm btn-success m-2 mt-0">Allow
                    access</div>`;
            }
            else{
                usersHTML += `<div data-user-id="${user.id}" class="deny-access btn-sm btn-danger m-2 mt-0">Deny access</div>`;
            }
        }
        usersHTML.innerHTML += `</div></div>`;
        users.innerHTML += usersHTML;
    });
}

const searchUsersForm = document.querySelector("#search_users");
searchUsersForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    await loadUsers();
});


