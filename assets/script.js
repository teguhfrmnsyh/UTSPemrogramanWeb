/**
 * Bersuara - Interactive JavaScript
 * FR-05: Pencarian artis pencocokan sebagian + rekomendasi mirip Spotify
 * FR-09: Perhitungan dinamis total bayar
 * FR-17: Konfirmasi hapus konser
 */

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // FR-05: Rekomendasi Artis Seperti Spotify (Autocomplete)
    // -------------------------------------------------------------
    const artistInput = document.getElementById('artist-search');
    const suggestionsBox = document.getElementById('artist-suggestions');

    if (artistInput && suggestionsBox) {
        let popularArtists = [];
        try {
            popularArtists = JSON.parse(artistInput.dataset.artists || '[]');
        } catch (err) {
            popularArtists = [];
        }

        artistInput.addEventListener('input', (e) => {
            const query = e.target.value.trim().toLowerCase();
            if (!query) {
                suggestionsBox.style.display = 'none';
                suggestionsBox.innerHTML = '';
                return;
            }

            const matches = popularArtists.filter(artist => 
                artist.toLowerCase().includes(query)
            );

            if (matches.length > 0) {
                suggestionsBox.innerHTML = '';
                const matchedSlice = matches.slice(0, 8);
                matchedSlice.forEach(artist => {
                    const item = document.createElement('div');
                    item.className = 'suggestion-item';
                    item.dataset.value = artist;

                    const artistSpan = document.createElement('span');
                    artistSpan.className = 'suggestion-artist';
                    artistSpan.textContent = artist;

                    const badgeSpan = document.createElement('span');
                    badgeSpan.className = 'suggestion-badge';
                    badgeSpan.textContent = 'Artis';

                    item.appendChild(artistSpan);
                    item.appendChild(badgeSpan);

                    item.addEventListener('click', () => {
                        artistInput.value = item.dataset.value;
                        suggestionsBox.style.display = 'none';
                        const form = artistInput.closest('form');
                        if (form) form.submit();
                    });

                    suggestionsBox.appendChild(item);
                });
                suggestionsBox.style.display = 'block';
            } else {
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
            }
        });

        // Sembunyikan saat klik di luar
        document.addEventListener('click', (e) => {
            if (!artistInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });
    }

    // -------------------------------------------------------------
    // FR-09: Update Dinamis Total Harga di Form Pembayaran
    // -------------------------------------------------------------
    const qtyInput = document.getElementById('jumlah-tiket');
    const unitPriceEl = document.getElementById('harga-satuan');
    const totalPriceEl = document.getElementById('total-harga-display');
    const totalInput = document.getElementById('total-bayar-input');

    if (qtyInput && unitPriceEl && totalPriceEl) {
        const unitPrice = parseInt(unitPriceEl.dataset.price || '0', 10);
        const formatRupiah = (num) => 'Rp ' + num.toLocaleString('id-ID');

        qtyInput.addEventListener('input', () => {
            let qty = parseInt(qtyInput.value || '1', 10);
            const max = parseInt(qtyInput.getAttribute('max') || '4', 10);
            const min = parseInt(qtyInput.getAttribute('min') || '1', 10);

            if (qty < min) qty = min;
            if (qty > max) qty = max;

            const total = qty * unitPrice;
            totalPriceEl.textContent = formatRupiah(total);
            if (totalInput) totalInput.value = total;
        });
    }

    // -------------------------------------------------------------
    // FR-17: Konfirmasi Hapus Konser Admin
    // -------------------------------------------------------------
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const concertName = btn.dataset.concertName || 'konser ini';
            const confirmed = confirm(`Apakah Anda yakin ingin menghapus konser "${concertName}"? Tindakan ini tidak dapat dibatalkan.`);
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
});
