function updateHargaDanTotal() {
  const hargaPerKopi = {
    americano: 8000,
    cappucino: 10000,
    brown: 12000,
    caramel: 14000,
  };

  const namaKopi = document.getElementById("nama_kopi").value;
  const hargaInput = document.getElementById("harga_kopi");
  const totalCupInput = document.getElementById("total_cup");
  const totalHargaInput = document.getElementById("total_harga");

  const harga = hargaPerKopi[namaKopi] || 0;
  const totalCup = parseInt(totalCupInput.value) || 0;
  const totalHarga = harga * totalCup;

  hargaInput.value = harga;
  totalHargaInput.value = totalHarga;
}

document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("nama_kopi")
    .addEventListener("change", updateHargaDanTotal);
  document
    .getElementById("total_cup")
    .addEventListener("input", updateHargaDanTotal);
});
