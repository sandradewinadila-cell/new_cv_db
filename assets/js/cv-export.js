/**
 * assets/js/cv-export.js
 * Logika ekspor CV ke format PDF menggunakan html2pdf.js
 */

document.addEventListener("DOMContentLoaded", function () {
  const downloadBtn = document.getElementById("btn-download-pdf");

  if (!downloadBtn) return;

  downloadBtn.addEventListener("click", function () {
    const element = document.getElementById("cv-content");

    if (!element) {
      alert("Konten CV tidak ditemukan!");
      return;
    }

    // Ubah status dan teks tombol saat memproses
    const originalText = downloadBtn.innerHTML;
    downloadBtn.disabled = true;
    downloadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses PDF...';

    // Opsi konfigurasi html2pdf
    const opt = {
      margin: [8, 8, 8, 8], // Margin dalam satuan mm
      filename: "CV_Nadila_Sandra_Dewi.pdf",
      image: { type: "jpeg", quality: 0.98 },
      html2canvas: {
        scale: 2,
        useCORS: true,
        logging: false,
        scrollX: 0,
        scrollY: 0,
      },
      jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
      pagebreak: { mode: ["avoid-all", "css", "legacy"] },
    };

    // Eksekusi generate PDF
    html2pdf()
      .set(opt)
      .from(element)
      .save()
      .then(function () {
        // Kembalikan tombol ke keadaan semula setelah selesai
        downloadBtn.disabled = false;
        downloadBtn.innerHTML = originalText;
      })
      .catch(function (error) {
        console.error("Gagal mengekspor PDF:", error);
        alert("Terjadi kesalahan saat membuat file PDF.");
        downloadBtn.disabled = false;
        downloadBtn.innerHTML = originalText;
      });
  });
});
