<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>vonic user</title>
    <link rel="stylesheet" href="css/vonic.css">
</head>

<body>
    <nav>
        <div class="navdiv"></div> 
            <div class="logo">
                <img src="img/LogoVonic.png">
            </div>

            <div class="nav-menu">
                <button>
                    <a href="/">Beranda</a>
                </button>
                <button>
                    <a href="/riwayat">Riwayat Pemilihan</a>
                </button>
            </div>
    </nav>
    
    <h1>Polling Aktif</h1>
    

    <div class="kontainer">
        
<div class="polling-container">
    <div class="polling-header">
        <div>
            <h2>Piknik DTEDI Periode 2026</h2>
            <p>Polling aktif • Segera ikuti voting</p>
        </div>

        <div class="polling-timer">
            <span>◷</span> 48:00:20
        </div>
    </div>

    <div class="polling-alert">
        Anda belum mengikuti polling ini. Segera tekan tombol
        "Ikuti Voting" untuk memberikan suara Anda.
    </div>

    <div class="polling-info">
        <div class="info-item">
            <div class="info-label">▣ <span>Periode</span></div>
            <strong>1 Sep - 12 Sep 2026</strong>
        </div>

        <div class="info-item">
            <div class="info-label">♧ <span>Partisipan</span></div>
            <strong>120</strong>
        </div>

        <div class="info-item">
            <div class="info-label">◌ <span>Status</span></div>
            <strong>Belum mengikuti</strong>
        </div>
    </div>

    <div class="polling-action">
        <button type="button" class="polling-button">
            Ikuti Voting <span>→</span>
        </button>
    </div>
</div>


<div class="faq-container">

    <details class="faq-item">
        <summary>
            <span class="faq-icon">?</span>
            <span class="faq-question">
                Bagaimana cara memberikan suara?
            </span>
            <span class="faq-arrow"></span>
        </summary>

        <div class="faq-answer">
            Untuk memberikan suara, silakan klik tombol
            "Ikuti Voting", kemudian pilih salah satu pilihan
            yang tersedia dan tekan tombol Kirim Suara.
        </div>
    </details>

    <details class="faq-item">
        <summary>
            <span class="faq-icon">?</span>
            <span class="faq-question">
                Apakah pilihan bisa diubah setelah dikirim?
            </span>
            <span class="faq-arrow"></span>
        </summary>

        <div class="faq-answer">
            Tidak. Setelah suara berhasil dikirim, pilihan
            tidak dapat diubah kembali. Pastikan pilihan Anda
            sudah benar sebelum mengirimkan suara.
        </div>
    </details>

    <details class="faq-item">
        <summary>
            <span class="faq-icon">?</span>
            <span class="faq-question">
                Kapan hasil voting diumumkan?
            </span>
            <span class="faq-arrow"></span>
        </summary>

        <div class="faq-answer">
            Hasil voting akan diumumkan setelah periode
            pemungutan suara berakhir dan proses perhitungan
            suara selesai dilakukan.
        </div>
    </details>

    <details class="faq-item">
        <summary>
            <span class="faq-icon">?</span>
            <span class="faq-question">
                Apa yang terjadi jika belum sempat memilih?
            </span>
            <span class="faq-arrow"></span>
        </summary>

        <div class="faq-answer">
            Jika belum memberikan suara hingga periode voting
            berakhir, Anda tidak akan tercatat sebagai
            peserta yang telah memberikan suara.
        </div>
    </details>

</div>

        

    </div>

    <h1>Riwayat Pemilihan Destinasi Wisata Piknik DTEDI</h1>
    <div class="card-container">
        <div class="riwayat">
            <h2>Pantai Lorem Ipsum</h2>
            <p>Deskripsi pantai Lorem Ipsum</p>

            <div class="info">
                <img src="img/calendar_icon.png">

                <div class="ingfo">
                    <span>Periode 2025</span>
                    <span>dd/mm/yy</span>
                </div>
            </div>

            <div class="info">
                <img src="img/participants_icon.png">

                <div class="ingfo">
                    <span>Partisipan</span>
                    <span>100 orang</span>
                </div>
        </div>
        <button onclick="popup1.showModal()">Lihat hasil</button>
        <dialog id="popup1">
            <strong>Hasil Voting</strong>
            <h2>Piknik DTEDI Periode 2025</h2>
            <img src="https://images.pexels.com/photos/25405465/pexels-photo-25405465.jpeg" height="300px" width="300px">
            <p>Deskripsi pantai Lorem Ipsum.</p>
            <button onclick="popup1.close()">Tutup</button>
        </dialog>
    </div>

         <div class="riwayat">
            <h2>Pantai Lorem Ipsum</h2>
            <p>Deskripsi pantai Lorem Ipsum</p>

            <div class="info">
            <img src="img/calendar_icon.png">

            <div class="ingfo">
                <span>Periode 2025</span>
                <span>dd/mm/yy</span>
            </div>
        </div>

        <div class="info">
            <img src="img/participants_icon.png">

            <div class="ingfo">
                <span>Partisipan</span>
                <span>100 orang</span>
            </div>
    </div>
    <button>Lihat hasil</button>
</div>

        <div class="riwayat">
            <h2>Pantai Lorem Ipsum</h2>
            <p>Deskripsi pantai Lorem Ipsum</p>

            <div class="info">
            <img src="img/calendar_icon.png">

            <div class="ingfo">
                <span>Periode 2025</span>
                <span>dd/mm/yy</span>
            </div>
        </div>

        <div class="info">
            <img src="img/participants_icon.png">

            <div class="ingfo">
                <span>Partisipan</span>
                <span>100 orang</span>
            </div>
    </div>
    <button>Lihat hasil</button>
</div>

    </div>

<footer class="footer-container">
    <div class="footer-logo">
        <img src="img/LogoVonic.png">
    </div>

    <div class="footer-copyright">
        © 2026 DTEDI SV UGM
    </div>
</footer>

</body>
</html>