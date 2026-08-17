import os
import sys
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super(NumberedCanvas, self).__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super(NumberedCanvas, self).showPage()
        super(NumberedCanvas, self).save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        
        # Don't draw header/footer on cover page (page 1)
        if self._pageNumber > 1:
            # Header
            self.setFont("Helvetica-Bold", 8)
            self.setFillColor(colors.HexColor("#2B5C92"))
            self.drawString(36, 815, "PANDUAN PENGGUNAAN SISTEM BEASISWA BLITAR MENGABDI")
            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#64748B"))
            self.drawRightString(559, 815, "DISPORA KABUPATEN BLITAR")
            
            self.setStrokeColor(colors.HexColor("#E2E8F0"))
            self.setLineWidth(0.75)
            self.line(36, 808, 559, 808)

            # Footer
            self.setStrokeColor(colors.HexColor("#E2E8F0"))
            self.setLineWidth(0.75)
            self.line(36, 45, 559, 45)

            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#64748B"))
            self.drawString(36, 32, "Hak Cipta © 2026 Dispora Kabupaten Blitar — Manual Book Pemohon V2.2")
            
            page_text = f"Halaman {self._pageNumber} dari {page_count}"
            self.drawRightString(559, 32, page_text)

        self.restoreState()

def build_pdf(filename):
    doc = SimpleDocTemplate(
        filename,
        pagesize=A4,
        leftMargin=36,
        rightMargin=36,
        topMargin=54,
        bottomMargin=54
    )

    styles = getSampleStyleSheet()
    
    # Custom styles
    primary_color = colors.HexColor("#2B5C92")
    dark_color = colors.HexColor("#0C1446")
    accent_color = colors.HexColor("#FFD800")
    text_color = colors.HexColor("#1E293B")

    title_style = ParagraphStyle(
        'CoverTitle',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=24,
        leading=30,
        textColor=dark_color,
        alignment=1, # Center
        spaceAfter=12
    )

    subtitle_style = ParagraphStyle(
        'CoverSubTitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=12,
        leading=16,
        textColor=colors.HexColor("#475569"),
        alignment=1,
        spaceAfter=24
    )

    h1_style = ParagraphStyle(
        'CustomH1',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=15,
        leading=19,
        textColor=primary_color,
        spaceBefore=14,
        spaceAfter=8,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'CustomH2',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=15,
        textColor=dark_color,
        spaceBefore=10,
        spaceAfter=6,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'CustomBody',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=14,
        textColor=text_color,
        spaceAfter=6
    )

    bullet_style = ParagraphStyle(
        'CustomBullet',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=14,
        textColor=text_color,
        leftIndent=15,
        spaceAfter=4
    )

    note_style = ParagraphStyle(
        'NoteText',
        parent=styles['Normal'],
        fontName='Helvetica-Oblique',
        fontSize=9,
        leading=13,
        textColor=colors.HexColor("#1E3A8A")
    )

    story = []

    # COVER SECTION
    story.append(Spacer(1, 40))
    
    # Decorative Top Badge
    badge_data = [[Paragraph("<font color='#0C1446'><b>PANDUAN RESMI PEMOHON (PENGUJI)</b></font>", ParagraphStyle('Badge', fontName='Helvetica-Bold', fontSize=10, alignment=1))]]
    badge_table = Table(badge_data, colWidths=[250])
    badge_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), accent_color),
        ('ALIGN', (0,0), (-1,-1), 'CENTER'),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('BOTTOMPADDING', (0,0), (-1,-1), 6),
        ('TOPPADDING', (0,0), (-1,-1), 6),
        ('CORNERPAD', (0,0), (-1,-1), 8),
    ]))
    story.append(badge_table)
    story.append(Spacer(1, 25))

    story.append(Paragraph("MANUAL BOOK PEMOHON", title_style))
    story.append(Paragraph("Sistem Informasi Manajemen Beasiswa Terpadu<br/><b>BEASISWA BLITAR MENGABDI (V2.2)</b>", ParagraphStyle('SubHeader', fontName='Helvetica-Bold', fontSize=14, leading=18, textColor=primary_color, alignment=1)))
    story.append(Spacer(1, 10))
    story.append(Paragraph("Dinas Kepemudaan dan Olahraga (Dispora) Kabupaten Blitar", subtitle_style))
    story.append(Spacer(1, 15))

    # Box Info Panduan
    info_box_content = [
        [Paragraph("<b>Ringkasan Panduan Penggunaan</b>", ParagraphStyle('BoxH', fontName='Helvetica-Bold', fontSize=11, textColor=primary_color))],
        [Paragraph("Dokumen ini berisi panduan alur pendaftaran mandiri (tanpa akun), tata cara pengisian formulir 5-step, pengunggahan berkas persyaratan, pemeriksaan status kelulusan, serta pencetakan bukti pendaftaran resmi pada Portal Beasiswa Blitar Mengabdi.", body_style)]
    ]
    info_table = Table(info_box_content, colWidths=[500])
    info_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F8FAFC")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#CBD5E1")),
        ('PADDING', (0,0), (-1,-1), 12),
        ('LEFTPADDING', (0,0), (-1,-1), 14),
    ]))
    story.append(info_table)
    story.append(Spacer(1, 30))

    # Identitas Penerbit Table
    meta_data = [
        [Paragraph("<b>Instansi Pengelola</b>", body_style), Paragraph(": Dinas Kepemudaan dan Olahraga (Dispora) Kab. Blitar", body_style)],
        [Paragraph("<b>Alamat Kantor</b>", body_style), Paragraph(": Jl. Raya Sawahan Pojok, Kec. Garum, Kabupaten Blitar", body_style)],
        [Paragraph("<b>Layanan Kontak WA</b>", body_style), Paragraph(": Bapak Akhyat (0813-3400-1600)", body_style)],
        [Paragraph("<b>Jam Operasional</b>", body_style), Paragraph(": Senin — Jumat, 08:00 — 16:00 WIB", body_style)],
        [Paragraph("<b>Situs Web Resmi</b>", body_style), Paragraph(": http://localhost:8000 (Portal Publik)", body_style)],
    ]
    meta_table = Table(meta_data, colWidths=[140, 360])
    meta_table.setStyle(TableStyle([
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('TOPPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(meta_table)

    story.append(PageBreak())

    # BAB 1: PENGENALAN SISTEM
    story.append(Paragraph("BAB 1: PENGENALAN & KETENTUAN UMUM", h1_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=2, spaceAfter=10))

    story.append(Paragraph("1.1 Latar Belakang & Tiga Program Utama", h2_style))
    story.append(Paragraph("Sistem Informasi Manajemen Beasiswa Terpadu Kabupaten Blitar dikembangkan untuk memberikan kemudahan pendaftaran beasiswa daerah bagi mahasiswa dan calon mahasiswa berprestasi atau kurang mampu secara transparan, akuntabel, dan objektif.", body_style))
    story.append(Paragraph("Terdapat 3 (tiga) program beasiswa utama yang dibuka:", body_style))

    prog_table_data = [
        [Paragraph("<b>Kode</b>", ParagraphStyle('TH', fontName='Helvetica-Bold', fontSize=9, textColor=colors.white)), 
         Paragraph("<b>Nama Program Beasiswa</b>", ParagraphStyle('TH', fontName='Helvetica-Bold', fontSize=9, textColor=colors.white)), 
         Paragraph("<b>Deskripsi Singkat & Alur Seleksi</b>", ParagraphStyle('TH', fontName='Helvetica-Bold', fontSize=9, textColor=colors.white))],
        
        [Paragraph("<b>SDSS</b>", body_style), 
         Paragraph("<b>Satu Desa Satu Sarjana</b>", body_style), 
         Paragraph("Beasiswa penuh bagi mahasiswa perwakilan desa/kelurahan di Kab. Blitar. Melibatkan verifikasi faktual Desa & peninjauan Kecamatan.", body_style)],
        
        [Paragraph("<b>BERDAYA</b>", body_style), 
         Paragraph("<b>Berdaya Berjaya</b>", body_style), 
         Paragraph("Beasiswa prestasi mahasiswa Baru & Lama dengan tahapan Tes Wawancara serta perhitungan SPK kumulatif.", body_style)],
        
        [Paragraph("<b>BBP</b>", body_style), 
         Paragraph("<b>Bantuan Biaya Pendidikan</b>", body_style), 
         Paragraph("Bantuan biaya pendidikan untuk mahasiswa keluarga kurang mampu atau berprestasi akademik/non-akademik.", body_style)],
    ]
    prog_table = Table(prog_table_data, colWidths=[65, 140, 295])
    prog_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('PADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(prog_table)
    story.append(Spacer(1, 12))

    story.append(Paragraph("1.2 Ketentuan Pendaftaran Tanpa Akun (Guest)", h2_style))
    story.append(Paragraph("• <b>Tanpa Perlu Login/Register Akun:</b> Pemohon dapat langsung mengisi formulir pendaftaran secara terbuka tanpa perlu membuat akun pengguna.", bullet_style))
    story.append(Paragraph("• <b>Identifikasi NIK (16 Digit):</b> Seluruh pendaftaran diidentifikasi secara unik menggunakan Nomor Induk Kependudukan (NIK) resmi Kabupaten Blitar.", bullet_style))
    story.append(Paragraph("• <b>Aturan Penguncian NIK (Locking Rules):</b>", bullet_style))
    story.append(Paragraph("&nbsp;&nbsp;&nbsp;&nbsp;- Untuk Program SDSS: <b>1 NIK hanya dapat didaftarkan 1 kali seumur hidup</b>.", bullet_style))
    story.append(Paragraph("&nbsp;&nbsp;&nbsp;&nbsp;- Untuk Program Lain: 1 NIK dapat mendaftar kembali pada periode berikutnya jika pengajuan sebelumnya ditolak/gugur.", bullet_style))
    story.append(Spacer(1, 10))

    story.append(Paragraph("1.3 Berkas Persyaratan Umum yang Wajib Disiapkan", h2_style))
    story.append(Paragraph("Sebelum membuka formulir pendaftaran online, siapkan berkas digital berikut (Format PDF / DOC / DOCX, Maksimal 10 MB per berkas):", body_style))
    story.append(Paragraph("1. Scan KTP Asli / Surat Keterangan Domisili Kabupaten Blitar.", bullet_style))
    story.append(Paragraph("2. Scan Kartu Keluarga (KK) Kabupaten Blitar.", bullet_style))
    story.append(Paragraph("3. Surat Permohonan Beasiswa resmi (sesuai template program).", bullet_style))
    story.append(Paragraph("4. Surat Pernyataan Keabsahan Data (bermaterai 10.000).", bullet_style))
    story.append(Paragraph("5. Surat Pertanggungjawaban Mutlak (SPTJM) Kebenaran Dokumen.", bullet_style))
    story.append(Paragraph("6. Transkrip Nilai / Rapor Terakhir / Kartu Hasil Studi (KHS).", bullet_style))
    story.append(Paragraph("7. Berkas Pendukung (KIP/PKH/Surat Keterangan Tidak Mampu / Sertifikat Prestasi).", bullet_style))

    story.append(PageBreak())

    # BAB 2: ALUR PENDAFTARAN WIZARD 5 STEP
    story.append(Paragraph("BAB 2: TATA CARA PENDAFTARAN ONLINE (WIZARD 5-STEP)", h1_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=2, spaceAfter=10))

    story.append(Paragraph("Proses pendaftaran dilakukan secara mandiri melalui 5 (lima) tahapan pengisian formulir terstruktur:", body_style))
    story.append(Spacer(1, 6))

    steps_overview = [
        [Paragraph("<b>Step 1</b>", ParagraphStyle('TH1', fontName='Helvetica-Bold', fontSize=8.5, textColor=colors.HexColor("#0C1446"))), Paragraph("<b>Identitas Pemohon</b> (NIK, Nama, Alamat, No. HP, Kampus, Prodi, Semester)", body_style)],
        [Paragraph("<b>Step 2</b>", ParagraphStyle('TH1', fontName='Helvetica-Bold', fontSize=8.5, textColor=colors.HexColor("#0C1446"))), Paragraph("<b>Data Orang Tua / Wali</b> (Nama Ayah & Ibu, NIK Orang Tua, Pekerjaan, Penghasilan)", body_style)],
        [Paragraph("<b>Step 3</b>", ParagraphStyle('TH1', fontName='Helvetica-Bold', fontSize=8.5, textColor=colors.HexColor("#0C1446"))), Paragraph("<b>Status Ekonomi & Sosial</b> (Penghasilan Gabungan, Kepemilikan Rumah/Lahan, KIP/PKH)", body_style)],
        [Paragraph("<b>Step 4</b>", ParagraphStyle('TH1', fontName='Helvetica-Bold', fontSize=8.5, textColor=colors.HexColor("#0C1446"))), Paragraph("<b>Capaian Akademik & Prestasi</b> (IPK / Nilai Rapor, Prestasi Juara, Organisasi)", body_style)],
        [Paragraph("<b>Step 5</b>", ParagraphStyle('TH1', fontName='Helvetica-Bold', fontSize=8.5, textColor=colors.HexColor("#0C1446"))), Paragraph("<b>Unggah Dokumen Persyaratan</b> (Upload File PDF/DOC/DOCX maks 10MB)", body_style)],
    ]
    steps_table = Table(steps_overview, colWidths=[60, 440])
    steps_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F1F5F9")),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('PADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(steps_table)
    story.append(Spacer(1, 14))

    story.append(Paragraph("2.1 Langkah-Langkah Pengisian Formulir", h2_style))
    
    story.append(Paragraph("<b>1. Buka Halaman Pendaftaran:</b> Akses menu <b>Pendaftaran → Formulir Pendaftaran</b> pada navigasi atas atau klik tombol kuning <b>'Daftar Sekarang'</b> di beranda.", bullet_style))
    story.append(Paragraph("<b>2. Pilih Program & Jalur Beasiswa:</b> Pilih program beasiswa (misal: <i>Satu Desa Satu Sarjana</i>) dan jalur pendaftaran yang sedang berstatus <b>'Sedang Dibuka'</b>.", bullet_style))
    story.append(Paragraph("<b>3. Pengisian Step 1 (Identitas):</b> Masukkan 16 digit NIK secara teliti. Pastikan Nama Lengkap, Nomor HP/WhatsApp aktif, Perguruan Tinggi, dan Program Studi terisi benar.", bullet_style))
    story.append(Paragraph("<b>4. Pengisian Step 2 & 3 (Ekonomi):</b> Isi data orang tua serta kondisi perekonomian keluarga secara jujur dan transparan.", bullet_style))
    story.append(Paragraph("<b>5. Pengisian Step 4 (Akademik):</b> Masukkan nilai IPK atau rata-rata nilai rapor sesuai bukti dokumen resmi.", bullet_style))
    story.append(Paragraph("<b>6. Pengisian Step 5 (Unggah Berkas):</b> Klik tombol unggah pada masing-masing jenis dokumen wajib. Pastikan berkas terbaca jelas dan tidak rusak.", bullet_style))

    story.append(Spacer(1, 10))
    story.append(Paragraph("2.2 Langkah Final: Review & Submit Data", h2_style))
    story.append(Paragraph("Setelah menyelesaikan Step 5, sistem akan menampilkan halaman <b>Review Pendaftaran</b>:", body_style))
    story.append(Paragraph("• Periksa kembali seluruh ringkasan data identitas, akademik, dan berkas yang telah diunggah.", bullet_style))
    story.append(Paragraph("• Centang kotak persetujuan <b>Pernyataan Keabsahan Data & Kebijakan Privasi</b>.", bullet_style))
    story.append(Paragraph("• Klik tombol <b>'Kirim Pendaftaran'</b>.", bullet_style))

    # Caution Box
    warn_data = [[
        Paragraph("<b>PERHATIAN PENTING:</b><br/>Setelah tombol 'Kirim Pendaftaran' diklik, seluruh data dan berkas akan <b>TERKUNCI PERMANEN</b> dan tidak dapat diubah kembali. Pastikan seluruh data terisi dengan teliti sebelum mengirimkan.", note_style)
    ]]
    warn_table = Table(warn_data, colWidths=[500])
    warn_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#FEF3C7")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#F59E0B")),
        ('PADDING', (0,0), (-1,-1), 10),
    ]))
    story.append(Spacer(1, 8))
    story.append(warn_table)

    story.append(PageBreak())

    # BAB 3: CEK STATUS & CETAK BUKTI
    story.append(Paragraph("BAB 3: PANDUAN CEK STATUS & CETAK BUKTI PENDAFTARAN", h1_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=2, spaceAfter=10))

    story.append(Paragraph("3.1 Cara Melakukan Cek Status Pendaftaran", h2_style))
    story.append(Paragraph("Pemohon dapat memantau perkembangan status verifikasi dan seleksi secara real-time kapan saja:", body_style))
    story.append(Paragraph("1. Akses menu <b>Pendaftaran → Cek Status & Cetak Bukti</b>.", bullet_style))
    story.append(Paragraph("2. Masukkan <b>16 Digit NIK Pemohon</b>.", bullet_style))
    story.append(Paragraph("3. Pilih <b>Tahun Pendaftaran</b> (misal: 2026).", bullet_style))
    story.append(Paragraph("4. Klik tombol <b>'Cek Status'</b>.", bullet_style))

    story.append(Spacer(1, 10))
    story.append(Paragraph("3.2 Memahami Visual Stepper 5 Tahap Seleksi", h2_style))
    story.append(Paragraph("Halaman hasil pencarian status akan menampilkan grafik <b>5-Stage Alur Kemajuan Pendaftaran</b>:", body_style))

    stet_info = [
        [Paragraph("<b>Tahap 1: Pengajuan</b>", ParagraphStyle('TB1', fontName='Helvetica-Bold', fontSize=8.5, textColor=primary_color)), Paragraph("Pendaftaran berhasil diterima oleh sistem dan menunggu pemeriksaan berkas.", body_style)],
        [Paragraph("<b>Tahap 2: Verifikasi OPD</b>", ParagraphStyle('TB1', fontName='Helvetica-Bold', fontSize=8.5, textColor=primary_color)), Paragraph("Pemeriksaan keabsahan dokumen persyaratan oleh Tim Verifikator OPD.", body_style)],
        [Paragraph("<b>Tahap 3: Desa & Kecamatan</b>", ParagraphStyle('TB1', fontName='Helvetica-Bold', fontSize=8.5, textColor=primary_color)), Paragraph("Verifikasi faktual domisili oleh Desa & peninjauan rekomendasi Kecamatan (SDSS).", body_style)],
        [Paragraph("<b>Tahap 4: Penilaian SPK</b>", ParagraphStyle('TB1', fontName='Helvetica-Bold', fontSize=8.5, textColor=primary_color)), Paragraph("Perhitungan pembobotan nilai otomatis menggunakan Model Penilaian Berbobot.", body_style)],
        [Paragraph("<b>Tahap 5: Penetapan SK</b>", ParagraphStyle('TB1', fontName='Helvetica-Bold', fontSize=8.5, textColor=primary_color)), Paragraph("Penetapan penerima beasiswa resmi melalui Surat Keputusan Bupati Blitar.", body_style)],
    ]
    stet_table = Table(stet_info, colWidths=[130, 370])
    stet_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F8FAFC")),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#E2E8F0")),
        ('PADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(stet_table)
    story.append(Spacer(1, 12))

    story.append(Paragraph("Arti Warna Indikator Status:", h2_style))
    story.append(Paragraph("• <b>Hijau Solid:</b> Tahapan telah SELESAI & Lolos verifikasi.", bullet_style))
    story.append(Paragraph("• <b>Biru Pulsasi (Aktif):</b> Tahapan SEDANG BERJALAN saat ini.", bullet_style))
    story.append(Paragraph("• <b>Merah:</b> Berkas TIDAK LOLOS / Pengajuan Ditolak (Alasan penolakan ditampilkan pada layar).", bullet_style))
    story.append(Paragraph("• <b>Abu-abu:</b> Tahapan MENUNGGU proses sebelumnya.", bullet_style))

    story.append(Spacer(1, 10))
    story.append(Paragraph("3.3 Cara Mengunduh & Mencetak Tanda Bukti Pendaftaran", h2_style))
    story.append(Paragraph("Apabila status pendaftaran berhasil ditemukan, tombol <b>'Cetak Tanda Bukti Pendaftaran (PDF)'</b> akan muncul di bagian atas hasil pencarian. Klik tombol tersebut untuk mengunduh dokumen resmi bukti pendaftaran berstempel digital yang mencantumkan Nomor Pendaftaran unik (Contoh: <b>BM-2026-000123</b>).", body_style))

    story.append(PageBreak())

    # BAB 4: FAQ & CONTACT PERSON
    story.append(Paragraph("BAB 4: TANYA JAWAB (FAQ) & PUSAT BANTUAN", h1_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=2, spaceAfter=10))

    story.append(Paragraph("4.1 Pertanyaan Sering Diajukan (FAQ)", h2_style))

    faq_data = [
        [Paragraph("<b>Q: Apakah saya harus membuat akun untuk mendaftar?</b><br/>A: Tidak. Pendaftaran bersifat terbuka (guest) tanpa perlu membuat akun. Cukup isi formulir dan simpan NIK Anda untuk melakukan cek status.", body_style)],
        [Paragraph("<b>Q: Mengapa NIK saya dinyatakan 'Sudah Terdaftar'?</b><br/>A: NIK Anda sudah pernah digunakan mendaftar pada program beasiswa aktif. Untuk program SDSS, NIK hanya berlaku 1 kali seumur hidup.", body_style)],
        [Paragraph("<b>Q: Bagaimana jika ada dokumen yang salah diunggah setelah dikirim?</b><br/>A: Data yang sudah di-submit tidak dapat diubah sendiri. Apabila verifikator menemukan kesalahan, verifikator akan memberikan catatan penolakan agar Anda dapat mendaftar kembali pada periode berikutnya.", body_style)],
        [Paragraph("<b>Q: Di mana saya bisa mengunduh template Surat Pernyataan / Juknis?</b><br/>A: Template dokumen resmi dapat diunduh pada halaman <b>Informasi & Juknis</b> di bagian bawah seksi 'Dokumen Penting & Juknis'.", body_style)],
    ]
    faq_table = Table(faq_data, colWidths=[500])
    faq_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F1F5F9")),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('PADDING', (0,0), (-1,-1), 8),
    ]))
    story.append(faq_table)
    story.append(Spacer(1, 16))

    story.append(Paragraph("4.2 Layanan Informasi & Pusat Bantuan Resmi", h2_style))
    story.append(Paragraph("Jika Pemohon mengalami kendala teknis pendaftaran atau membutuhkan informasi lebih lanjut, silakan hubungi pusat layanan resmi kami:", body_style))
    story.append(Spacer(1, 6))

    contact_box = [
        [Paragraph("<b>CONTACT PERSON OFFICIAL</b>", ParagraphStyle('CH', fontName='Helvetica-Bold', fontSize=10, textColor=dark_color))],
        [Paragraph("• <b>Penanggung Jawab Layanan:</b> Bapak Akhyat", body_style)],
        [Paragraph("• <b>Nomor Telepon / WhatsApp:</b> 0813-3400-1600", body_style)],
        [Paragraph("• <b>Jam Pelayanan:</b> Senin — Jumat, pukul 08:00 — 16:00 WIB", body_style)],
        [Paragraph("• <b>Alamat Sekretariat:</b> Kantor Dinas Kepemudaan dan Olahraga Kab. Blitar, Jl. Raya Sawahan Pojok, Kec. Garum, Blitar", body_style)],
    ]
    contact_table = Table(contact_box, colWidths=[500])
    contact_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#FFFBEB")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#F59E0B")),
        ('PADDING', (0,0), (-1,-1), 10),
    ]))
    story.append(contact_table)

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"PDF Manual Book successfully generated at: {filename}")

if __name__ == "__main__":
    output_path = os.path.join(os.getcwd(), "public", "MANUAL_BOOK_PEMOHON_BEASISWA_BLITAR_MENGABDI.pdf")
    build_pdf(output_path)
