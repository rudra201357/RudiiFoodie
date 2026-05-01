var swiper = new Swiper(".mySwiper", {
  pagination: {
    el: ".swiper-pagination",
    dynamicBullets: true,
  },
});
const sortDropdown = document.getElementById('sort-options');

sortDropdown.addEventListener('change', function () {
  const selectedValue = this.value;
  console.log("Selected Sort Option:", selectedValue);

});




 