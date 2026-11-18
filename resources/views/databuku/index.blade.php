@extends('layouts.perpustakaan')

@section('title', 'Daftar Buku')

@section('content')

    <div class="page-header">
        <h2>Daftar Buku</h2>
        <p>Catatan koleksi perpustakaan.</p>
    </div>

    <div id="alert-area" role="status" aria-live="polite"></div>

    {{-- Saat form disembunyikan (siswa), daftar buku dibuat selebar halaman --}}
    <style>
        .katalog--penuh {
            display: block !important;
            grid-template-columns: 1fr !important;
        }
        .katalog--penuh .rak {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }
        .katalog--penuh .rak-tabel {
            width: 100%;
            table-layout: auto;
        }
        .katalog--penuh .rak-tabel th,
        .katalog--penuh .rak-tabel td {
            padding: 14px 16px;
            vertical-align: middle;
        }
        .katalog--penuh .rak-tabel td.judul {
            min-width: 260px;
        }
        .katalog--penuh .rak-tabel td.aksi {
            text-align: right;
            white-space: nowrap;
        }
        .katalog--penuh .rak-tabel td.aksi .aksi-link {
            display: inline-block;
            padding: 6px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            text-decoration: none;
            font-size: 13px;
        }
        .katalog--penuh .rak-tabel td.aksi .aksi-link:hover {
            background: #0f172a;
            border-color: #0f172a;
            color: #fff;
        }
    </style>

    <div class="katalog @cannot('kelola data buku') katalog--penuh @endcannot">

        {{-- Kiri: formulir tambah / ubah (hanya admin dan petugas) --}}
        @can('kelola-buku')
        <aside class="catat" id="kartu-form">
            <h3 id="judul-form">Tambah buku baru</h3>
            <p class="catat-bantuan" id="bantuan-form">Isi keempat kolom, lalu simpan.</p>

            <form id="form-buku" novalidate>
                <input type="hidden" id="buku-id">

                <div class="isian">
                    <label for="buku-kategori_id">Kategori</label>
                    <select id="buku-kategori_id">
                        <option value="">Pilih kategori</option>
                        @foreach ($kategoris as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                    <small class="galat" id="err-kategori_id"></small>
                </div>

                <div class="isian">
                    <label for="buku-judul">Judul</label>
                    <input type="text" id="buku-judul" autocomplete="off">
                    <small class="galat" id="err-judul"></small>
                </div>

                <div class="isian">
                    <label for="buku-penulis">Penulis</label>
                    <input type="text" id="buku-penulis" autocomplete="off">
                    <small class="galat" id="err-penulis"></small>
                </div>

                <div class="isian">
                    <label for="buku-tahun_terbit">Tahun terbit</label>
                    <input type="number" id="buku-tahun_terbit" placeholder="Contoh: 2020">
                    <small class="galat" id="err-tahun_terbit"></small>
                </div>

                <div class="catat-aksi">
                    <button type="submit" id="btn-simpan" class="btn">Simpan buku</button>
                    <button type="button" id="btn-batal" class="btn-teks" hidden>Batal ubah</button>
                </div>
            </form>
        </aside>
        @endcan

        {{-- Kanan: daftar buku --}}
        <section class="rak" aria-labelledby="judul-rak">
            <div class="rak-kepala">
                <h3 id="judul-rak">Koleksi</h3>
                <span id="info-total"></span>
            </div>

            <div class="rak-gulir">
                <table class="rak-tabel">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Tahun terbit</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-buku">
                        <tr><td colspan="6" class="rak-kosong">Mengambil data buku...</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="rak-kaki">
                <span id="info-halaman"></span>
                <div class="pager">
                    <button type="button" id="btn-prev" class="btn-garis">&lsaquo; Sebelumnya</button>
                    <button type="button" id="btn-next" class="btn-garis">Berikutnya &rsaquo;</button>
                </div>
            </div>
        </section>

    </div>

    <script>
        const API = '/api/buku';
        const FIELD = ['kategori_id', 'judul', 'penulis', 'tahun_terbit'];
        const $ = (id) => document.getElementById(id);
        let halaman = 1;

        // Hanya admin dan petugas yang boleh mengelola buku
        const BISA_KELOLA = @json(auth()->user()?->can('kelola-buku') ?? false);

        // Token CSRF dari cookie XSRF-TOKEN (dibutuhkan untuk POST/PUT/DELETE)
        const csrf = () => decodeURIComponent(
            (document.cookie.split('; ').find((c) => c.startsWith('XSRF-TOKEN=')) || '').split('=')[1] || ''
        );

        // Ubah teks berbahaya menjadi aman sebelum masuk ke HTML (cegah XSS)
        const esc = (t) => String(t ?? '').replace(/[&<>"']/g, (c) =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        // Error khusus: status 0 = jaringan gagal, 422 = validasi gagal, dst.
        class ApiError extends Error {
            constructor(status, message, errors = {}) {
                super(message);
                this.status = status;
                this.errors = errors;
            }
        }

        function pesan(teks, jenis = 'success') {
            $('alert-area').innerHTML = `<div class="pesan pesan-${jenis}">${esc(teks)}</div>`;
        }

        function bersihkanError() {
            if (!BISA_KELOLA) return;
            FIELD.forEach((f) => {
                $('err-' + f).textContent = '';
                $('buku-' + f).classList.remove('tidak-valid');
            });
        }

        // Satu pintu untuk semua request ke API
        async function apiRequest(path = '', { method = 'GET', body = null } = {}) {
            const headers = {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': csrf(),
            };
            if (body) headers['Content-Type'] = 'application/json';

            let res;
            try {
                res = await fetch(API + path, {
                    method,
                    credentials: 'same-origin',
                    headers,
                    body: body ? JSON.stringify(body) : undefined,
                });
            } catch {
                // fetch hanya gagal (reject) kalau jaringan putus atau server mati
                throw new ApiError(0, 'Tidak dapat terhubung ke server.');
            }

            const data = await res.json().catch(() => null);

            // 404, 422, 500 tetap "berhasil" bagi fetch, jadi res.ok wajib dicek
            if (!res.ok) {
                throw new ApiError(res.status, data?.message ?? `HTTP ${res.status}`, data?.errors ?? {});
            }
            return data;
        }

        function tanganiError(err) {
            bersihkanError();
            if (err.status === 422) {
                Object.entries(err.errors).forEach(([f, m]) => {
                    if ($('err-' + f)) {
                        $('err-' + f).textContent = m.join(' ');
                        $('buku-' + f).classList.add('tidak-valid');
                    }
                });
                pesan('Data belum valid. Periksa kolom yang ditandai merah.', 'warning');
            } else if (err.status === 404) {
                pesan('Data tidak ditemukan. Mungkin sudah dihapus, jadi tabel dimuat ulang.', 'warning');
                muatBuku(halaman);
            } else if (err.status === 401 || err.status === 403) {
                pesan('Anda tidak memiliki izin untuk melakukan aksi ini.', 'danger');
            } else if (err.status === 419) {
                pesan('Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.', 'warning');
            } else if (err.status === 0) {
                pesan('Tidak dapat terhubung ke server. Periksa koneksi atau pastikan server berjalan.', 'danger');
            } else {
                pesan('Terjadi kesalahan pada server (kode ' + err.status + '). ' + err.message, 'danger');
            }
        }

        // Sorot baris yang sedang diubah
        function tandaiBaris() {
            if (!BISA_KELOLA) return;
            const id = $('buku-id').value;
            document.querySelectorAll('#tbody-buku tr[data-id]').forEach((tr) => {
                tr.classList.toggle('sedang-diubah', id !== '' && tr.dataset.id === id);
            });
        }

        // ---- Membaca data ----
        async function muatBuku(page = 1) {
            try {
                const json = await apiRequest('?page=' + page);

                // Kalau halaman kosong setelah menghapus, mundur satu halaman
                if (json.data.length === 0 && page > 1) return muatBuku(page - 1);

                halaman = json.meta.current_page;
                const mulai = json.meta.from ?? 1;

                $('tbody-buku').innerHTML = json.data.map((b, i) => `
                    <tr data-id="${b.id}" class="spine-${(((Number(b.kategori_id) || 1) - 1) % 6) + 1}">
                        <td class="no">${mulai + i}</td>
                        <td class="kategori">${esc(b.kategori)}</td>
                        <td class="judul">${esc(b.judul)}</td>
                        <td>${esc(b.penulis)}</td>
                        <td class="tahun">${esc(b.tahun)}</td>
                        <td class="aksi">
                            <a class="aksi-link" href="/buku/${b.id}">Detail</a>
                            ${BISA_KELOLA ? `
                                <button type="button" class="aksi-link" data-aksi="ubah" data-id="${b.id}">Ubah</button>
                                <button type="button" class="aksi-link hapus" data-aksi="hapus" data-id="${b.id}">Hapus</button>
                            ` : ''}
                        </td>
                    </tr>`).join('')
                    || '<tr><td colspan="6" class="rak-kosong">Rak masih kosong.</td></tr>';

                $('info-total').textContent = json.meta.total + ' buku tercatat';
                $('info-halaman').textContent = `Halaman ${json.meta.current_page} dari ${json.meta.last_page}`;
                $('btn-prev').disabled = json.links.prev === null;
                $('btn-next').disabled = json.links.next === null;
                tandaiBaris();
            } catch (err) {
                const teks = err.status === 0
                    ? 'Tidak dapat terhubung ke server.'
                    : 'Data buku gagal dimuat (kode ' + err.status + ').';
                $('tbody-buku').innerHTML = `<tr><td colspan="6" class="rak-kosong error">${esc(teks)}</td></tr>`;
                pesan(teks + ' Coba muat ulang halaman.', 'danger');
            }
        }

        // ---- Form ----
        function resetForm() {
            $('form-buku').reset();
            $('buku-id').value = '';
            $('kartu-form').classList.remove('catat--ubah');
            $('judul-form').textContent = 'Catat buku baru';
            $('bantuan-form').textContent = 'Isi keempat kolom, lalu simpan.';
            $('btn-simpan').textContent = 'Simpan buku';
            $('btn-batal').hidden = true;
            bersihkanError();
            tandaiBaris();
        }

        // ---- Tambah dan Ubah ----
        async function simpan(e) {
            e.preventDefault();
            const id = $('buku-id').value;
            const tombol = $('btn-simpan');

            const body = {
                kategori_id: $('buku-kategori_id').value || null,
                judul: $('buku-judul').value.trim(),
                penulis: $('buku-penulis').value.trim(),
                tahun_terbit: $('buku-tahun_terbit').value === '' ? null : Number($('buku-tahun_terbit').value),
            };

            tombol.disabled = true;
            tombol.textContent = 'Menyimpan...';

            try {
                await apiRequest(id ? '/' + id : '', { method: id ? 'PUT' : 'POST', body });

                // Baris ini hanya tercapai kalau request benar-benar berhasil
                pesan(id ? 'Perubahan buku berhasil disimpan.' : 'Buku berhasil ditambahkan.');
                resetForm();
                await muatBuku(id ? halaman : 1);
            } catch (err) {
                tanganiError(err);
            } finally {
                tombol.disabled = false;
                tombol.textContent = $('buku-id').value ? 'Simpan perubahan' : 'Simpan buku';
            }
        }

        async function bukaFormUbah(id) {
            try {
                const { data } = await apiRequest('/' + id);
                bersihkanError();
                $('buku-id').value = data.id;
                $('buku-kategori_id').value = data.kategori_id;
                $('buku-judul').value = data.judul;
                $('buku-penulis').value = data.penulis;
                $('buku-tahun_terbit').value = data.tahun; // di respons namanya "tahun"

                $('kartu-form').classList.add('catat--ubah');
                $('judul-form').textContent = 'Ubah buku';
                $('bantuan-form').textContent = 'Ganti isian yang perlu, lalu simpan. Buku lain tidak ikut berubah.';
                $('btn-simpan').textContent = 'Simpan perubahan';
                $('btn-batal').hidden = false;
                tandaiBaris();

                const gerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';
                $('kartu-form').scrollIntoView({ behavior: gerak, block: 'nearest' });
                $('buku-judul').focus({ preventScroll: true });
            } catch (err) {
                tanganiError(err);
            }
        }

        // ---- Hapus ----
        async function hapus(id) {
            if (!confirm('Yakin ingin menghapus buku ini?')) return; // batal = tidak ada request

            try {
                await apiRequest('/' + id, { method: 'DELETE' });
                if ($('buku-id').value === String(id)) resetForm(); // buku yang sedang diubah ikut terhapus
                pesan('Buku berhasil dihapus.');
                await muatBuku(halaman);
            } catch (err) {
                tanganiError(err);
            }
        }

        // ---- Inisialisasi ----
        if (BISA_KELOLA) {
            $('form-buku').onsubmit = simpan;
            $('btn-batal').onclick = resetForm;
        }
        $('btn-prev').onclick = () => muatBuku(halaman - 1);
        $('btn-next').onclick = () => muatBuku(halaman + 1);

        // Satu listener untuk semua tombol Ubah/Hapus (tombolnya dibuat dinamis)
        $('tbody-buku').onclick = (e) => {
            const b = e.target.closest('button[data-aksi]');
            if (!b) return;
            if (b.dataset.aksi === 'hapus') hapus(b.dataset.id);
            else bukaFormUbah(b.dataset.id);
        };

        muatBuku(1);
    </script>

@endsection