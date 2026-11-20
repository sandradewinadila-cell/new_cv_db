document.addEventListener("DOMContentLoaded", function () {
  const btnDownload = document.getElementById("btn-download-pdf");

  if (btnDownload) {
    btnDownload.addEventListener("click", function () {
      // 1. Ubah teks tombol saat proses berjalan
      const originalText = btnDownload.innerText;
      btnDownload.innerText = "Memproses PDF...";
      btnDownload.disabled = true;

      // 2. Target elemen yang akan di-convert (seluruh konten CV)
      const element = document.getElementById("cv-content");

      // 3. Konfigurasi html2pdf yang aman dan stabil
      const opt = {
        margin: 10, // Margin 10mm
        filename: "CV_Nadila_Sandra_Dewi.pdf",
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, letterRendering: true },
        jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
      };

      // 4. Generate dan simpan PDF
      html2pdf()
        .set(opt)
        .from(element)
        .save()
        .then(function () {
          // 5. Kembalikan tombol ke kondisi semula setelah berhasil
          btnDownload.innerText = originalText;
          btnDownload.disabled = false;
        })
        .catch(function (err) {
          // Handle error jika gagal
          console.error("Error generating PDF:", err);
          btnDownload.innerText = originalText;
          btnDownload.disabled = false;
          alert("Gagal membuat PDF. Pastikan semua gambar memiliki URL yang valid (CORS).");
        });
    });
  }
});
