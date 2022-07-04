// import { Calendar } from '@fullcalendar/core';
// import dayGridPlugin from '@fullcalendar/daygrid';
//
//
// const calendarEl = document.querySelector("#calendar");
//
// let calendar = new Calendar(calendarEl, {
//     plugins: [ dayGridPlugin],
//     initialView: 'dayGridMonth',
//     headerToolbar: {
//         left: 'prev',
//         center: 'title',
//         right: 'next'
//     },
//     contentHeight: "auto",
// });
//
// calendar.render();
//
// window.addEventListener("DOMContentLoaded", () => {
//     const offerID = window.location.toString().split('/').pop();
//
//     let prevMonth = document.querySelector("#calendar > * .fc-prev-button ");
//     let nextMonth = document.querySelector("#calendar > * .fc-next-button ");
//
//     const date = new Date();
//     let activeMonth = date.getMonth() + 1;
//
//     [prevMonth, nextMonth].forEach((button) => {
//         button.addEventListener("click", async (e) => {
//             if (button.classList.contains("fc-prev-button")) {
//                 let response = await fetch(`${offerID}/month-availability/${activeMonth}`, {
//                     method: "POST"
//                 });
//                 let data = await response.json();
//             }
//             else{
//                 console.log("prev");
//             }
//         })
//     });
//
// /*
//     let date = new Date(Date.now());
//     let formatedDate = `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;
//
//     const mark = document.querySelector('[data-date=toDateString] > * .fc-daygrid-day-events').style = "background: red;";
//     console.log(mark);*/
//
// })
//
// function loadNotAvailableDays (month) {
//
// }