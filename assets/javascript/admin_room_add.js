const beds = document.querySelectorAll(".bed-quantity");
const capacityField = document.querySelector("#capacity");

beds.forEach(bed => {
   bed.addEventListener("change", () => {
      let singleBed = parseInt(beds[0].value) || 0;
      let doubleBed = parseInt(beds[1].value) || 0;
      let capacity = singleBed + doubleBed*2;
      capacityField.innerText = capacity;
   })
});








