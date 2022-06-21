const beds = document.querySelectorAll(".bed-quantity");
const capacityField = document.querySelector("#capacity");

beds.forEach(bed => {
   bed.addEventListener("change", () => {
      let capacity = parseInt(beds[0].value) + parseInt(beds[1].value)*2;
      capacityField.innerText = capacity;
   })
});








