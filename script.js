// script.js
document.addEventListener("DOMContentLoaded", function () {
  const namaKopiSelect = document.getElementById("nama_kopi");
  const hargaKopiInput = document.getElementById("harga_kopi");
  const totalCupInput = document.getElementById("total_cup");
  const totalHargaInput = document.getElementById("total_harga");

  if (namaKopiSelect) {
    namaKopiSelect.addEventListener("change", function () {
      const selectedValue = this.value;
      let harga = 0;

      switch (selectedValue) {
        case "americano":
          harga = 8000;
          break;
        case "cappucino":
          harga = 10000;
          break;
        case "brown":
          harga = 12000;
          break;
        case "caramel":
          harga = 14000;
          break;
        default:
          harga = 0;
      }

      hargaKopiInput.value = harga;
      calculateTotalHarga();
    });
  }

  if (totalCupInput) {
    totalCupInput.addEventListener("input", calculateTotalHarga);
  }

  function calculateTotalHarga() {
    const harga = parseInt(hargaKopiInput.value) || 0;
    const totalCup = parseInt(totalCupInput.value) || 0;
    const totalHarga = harga * totalCup;

    totalHargaInput.value = totalHarga;
  }
});
