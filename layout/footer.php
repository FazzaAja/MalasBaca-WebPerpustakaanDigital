</div> <script>
        const globalPath = "<?php echo isset($path) ? $path : ''; ?>";

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function showBookDetail(id, title, author, desc, cover, pdf, role) {
            
            // 1. Munculkan Container Cover yang tadi disembunyikan
            const coverContainer = document.getElementById('coverContainer');
            coverContainer.style.display = 'flex'; 
            
            // Kembalikan styling shadow/background agar terlihat bagus saat muncul
            coverContainer.style.background = '#fdfbfb';
            coverContainer.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.5)';
            coverContainer.style.height = '240px'; // Sesuaikan tinggi cover
            coverContainer.style.width = '160px';  // Sesuaikan lebar cover
            coverContainer.style.marginBottom = '20px';

            // 2. Munculkan elemen lain
            document.getElementById('detailStats').style.display = 'flex';
            document.getElementById('actionButtons').style.display = 'block';
            document.getElementById('detailAuthor').style.display = 'block';

            // 3. Isi Data Text
            document.getElementById('detailTitle').innerText = title;
            document.getElementById('detailAuthor').innerText = author;
            document.getElementById('detailDescription').innerText = desc;
            document.getElementById('detailDescription').style.marginTop = '0px'; // Reset margin
            document.getElementById('detailDescription').style.textAlign = 'left'; // Reset align
            document.getElementById('detailId').innerText = id;
            // Set hidden favorite form input (if present)
            var favInput = document.getElementById('favBookId');
            if (favInput) favInput.value = id;
            
            // 4. Update Gambar Cover
            const img = document.getElementById('detailImage');
            // If cover is absolute (http(s) or protocol-relative), use it directly (CDN). Otherwise prefix with globalPath/uploads/covers/
            if (/^(https?:)?\/\//i.test(cover)) {
                img.src = cover;
            } else if (!cover) {
                img.src = '';
            } else {
                img.src = globalPath + 'uploads/covers/' + cover;
            }
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.borderRadius = '5px';
            img.style.objectFit = 'cover';

            // 5. Link Baca PDF
            const btn = document.getElementById('readBtn');
            btn.href = globalPath + "uploads/pdfs/" + pdf;

            // 6. Link PDF
            const scroll = document.getElementById('readBtn');
            btn.href = globalPath + "uploads/pdfs/" + pdf;

            // --- TAMBAHAN BARU: AUTO SCROLL UNTUK MOBILE ---
            // Cek jika lebar layar kurang dari 1024px (Tablet & HP)
            if (window.innerWidth <= 1024) {
                const panel = document.getElementById('rightPanel');
                
                // 1. Pastikan panel terlihat (Override CSS jika hidden di mobile)
                panel.style.display = 'flex'; 
                
                // 2. Lakukan Scroll Halus ke Panel Kanan
                panel.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' // Bagian atas panel akan sejajar dengan atas layar
                });
            }
        }
    </script>
</body>
</html>