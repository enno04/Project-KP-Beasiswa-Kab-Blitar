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
            # Running Header
            self.setFont("Helvetica-Bold", 8)
            self.setFillColor(colors.HexColor("#2B5C92"))
            self.drawString(36, 815, "MANUAL BOOK — SISTEM BEASISWA BLITAR MENGABDI (V2.2.0)")
            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#64748B"))
            self.drawRightString(559, 815, "PEMKAB BLITAR — DISPORA")
            
            self.setStrokeColor(colors.HexColor("#CBD5E1"))
            self.setLineWidth(0.75)
            self.line(36, 808, 559, 808)

            # Running Footer
            self.setStrokeColor(colors.HexColor("#CBD5E1"))
            self.setLineWidth(0.75)
            self.line(36, 42, 559, 42)

            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#64748B"))
            self.drawString(36, 28, "Dokumen Resmi Panduan Penggunaan Sistem Beasiswa Kabupaten Blitar © 2026")
            
            page_text = f"Halaman {self._pageNumber} dari {page_count}"
            self.drawRightString(559, 28, page_text)

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
    
    primary_color = colors.HexColor("#2B5C92")
    dark_color = colors.HexColor("#0C1446")
    accent_color = colors.HexColor("#FFD800")
    text_color = colors.HexColor("#1E293B")
    muted_color = colors.HexColor("#64748B")

    title_style = ParagraphStyle(
        'CoverTitle',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=24,
        leading=30,
        textColor=dark_color,
        alignment=1,
        spaceAfter=10
    )

    h1_style = ParagraphStyle(
        'CustomH1',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=12.5,
        leading=16,
        textColor=primary_color,
        spaceBefore=14,
        spaceAfter=6,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'CustomH2',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=10,
        leading=13,
        textColor=dark_color,
        spaceBefore=9,
        spaceAfter=4,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'CustomBody',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12.5,
        textColor=text_color,
        spaceAfter=4
    )

    bullet_style = ParagraphStyle(
        'CustomBullet',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12.5,
        textColor=text_color,
        leftIndent=12,
        spaceAfter=3
    )

    table_th = ParagraphStyle('TH', fontName='Helvetica-Bold', fontSize=8, textColor=colors.white)
    table_td = ParagraphStyle('TD', fontName='Helvetica', fontSize=8, leading=11, textColor=text_color)
    table_td_bold = ParagraphStyle('TDBold', fontName='Helvetica-Bold', fontSize=8, leading=11, textColor=dark_color)

    story = []

    # ═════════════════════════════════════════════════════════════════════════
    # COVER PAGE
    # ═════════════════════════════════════════════════════════════════════════
    story.append(Spacer(1, 25))
    
    badge_data = [[Paragraph("<font color='#0C1446'><b>DOKUMEN RESMI PANDUAN PENGGUNA TERPADU</b></font>", ParagraphStyle('Badge', fontName='Helvetica-Bold', fontSize=9, alignment=1))]]
    badge_table = Table(badge_data, colWidths=[320])
    badge_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), accent_color),
        ('ALIGN', (0,0), (-1,-1), 'CENTER'),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('BOTTOMPADDING', (0,0), (-1,-1), 6),
        ('TOPPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(badge_table)
    story.append(Spacer(1, 20))

    story.append(Paragraph("MANUAL BOOK", title_style))
    story.append(Paragraph("SISTEM BEASISWA BLITAR MENGABDI", ParagraphStyle('SubHeader', fontName='Helvetica-Bold', fontSize=15, leading=19, textColor=primary_color, alignment=1)))
    story.append(Spacer(1, 8))
    story.append(Paragraph("Pemerintah Kabupaten Blitar — Dinas Kepemudaan dan Olahraga (Dispora)", ParagraphStyle('SubText', fontName='Helvetica', fontSize=10.5, textColor=muted_color, alignment=1)))
    story.append(Spacer(1, 25))

    doc_info_data = [
        [Paragraph("<b>Versi Dokumen</b>", table_td_bold), Paragraph(": 2.2.0 (Final Official Release)", table_td)],
        [Paragraph("<b>Status Platform</b>", table_td_bold), Paragraph(": Website (Laravel 12 + Tailwind CSS v4)", table_td)],
        [Paragraph("<b>Tanggal Penyusunan</b>", table_td_bold), Paragraph(": 12 Agustus 2026", table_td)],
        [Paragraph("<b>Instansi Pengelola</b>", table_td_bold), Paragraph(": Dinas Kepemudaan dan Olahraga Kab. Blitar", table_td)],
        [Paragraph("<b>Contact Person Official</b>", table_td_bold), Paragraph(": Bapak Akhyat (0813-3400-1600)", table_td)],
        [Paragraph("<b>URL Login Administrator</b>", table_td_bold), Paragraph(": http://localhost:8000/mengabdi (Terselubung)", table_td)],
    ]
    doc_info_table = Table(doc_info_data, colWidths=[140, 360])
    doc_info_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F8FAFC")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#CBD5E1")),
        ('PADDING', (0,0), (-1,-1), 7),
    ]))
    story.append(doc_info_table)
    story.append(Spacer(1, 25))

    toc_summary = [
        [Paragraph("<b>STRUKTUR 14 BAB MANUAL BOOK SISTEM</b>", ParagraphStyle('H', fontName='Helvetica-Bold', fontSize=9, textColor=primary_color))],
        [Paragraph("BAB 1: Pendahuluan &nbsp;|&nbsp; BAB 2: Akses Website &nbsp;|&nbsp; BAB 3: Pendaftaran Masyarakat<br/>"
                   "BAB 4: Super Admin &nbsp;|&nbsp; BAB 5: Admin OPD &nbsp;|&nbsp; BAB 6: Admin Desa/Kelurahan<br/>"
                   "BAB 7: Admin Kecamatan &nbsp;|&nbsp; BAB 8: Admin Kabupaten &nbsp;|&nbsp; BAB 9: Workflow Program<br/>"
                   "BAB 10: Penilaian dan Ranking &nbsp;|&nbsp; BAB 11: Penetapan dan Output &nbsp;|&nbsp; BAB 12: Monitoring & Audit<br/>"
                   "BAB 13: Manajemen Akun &nbsp;|&nbsp; BAB 14: Penutup", body_style)]
    ]
    toc_sum_table = Table(toc_summary, colWidths=[500])
    toc_sum_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F1F5F9")),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor("#94A3B8")),
        ('PADDING', (0,0), (-1,-1), 9),
    ]))
    story.append(toc_sum_table)

    story.append(PageBreak())

    # ═════════════════════════════════════════════════════════════════════════
    # DAFTAR ISI & BAB 1 - 3
    # ═════════════════════════════════════════════════════════════════════════
    story.append(Paragraph("DAFTAR ISI MANUAL BOOK", h1_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=2, spaceAfter=8))

    babs = [
        ("BAB 1  PENDAHULAN", "Latar belakang, tujuan panduan, dan gambaran umum Sistem Beasiswa Blitar Mengabdi."),
        ("BAB 2  AKSES WEBSITE", "Halaman Publik, Informasi Beasiswa, Dokumen Penting, Pendaftaran, Cek Status."),
        ("BAB 3  PENDAFTARAN MASYARAKAT", "Alur formulir 5-step mandiri tanpa akun, review pendaftaran, persetujuan & submit."),
        ("BAB 4  SUPER ADMIN", "Dashboard, Master Referensi, Master Konfigurasi, User, Monitoring, Audit Log & Password."),
        ("BAB 5  ADMIN OPD", "Dashboard OPD, Daftar Pendaftar, Dokumen Sesuai Kewenangan, Verifikasi Dokumen."),
        ("BAB 6  ADMIN DESA/KELURAHAN", "Dashboard Desa, Data SDSS, Verifikasi Faktual, Penentuan 1 Calon & Surat Rekomendasi."),
        ("BAB 7  ADMIN KECAMATAN", "Dashboard Kecamatan, Rekomendasi Desa, Verifikasi Kecamatan, Meneruskan ke Kabupaten."),
        ("BAB 8  ADMIN KABUPATEN", "Dashboard Kabupaten, Verifikasi Administrasi, Wawancara, Penilaian SPK, Penetapan SK."),
        ("BAB 9  WORKFLOW PROGRAM", "Skema alur terinci 3 program: SDSS, Berdaya Berjaya, dan Bantuan Biaya Pendidikan."),
        ("BAB 10 PENILAIAN DAN RANKING", "Kriteria SPK, Bobot Terbobot, Perhitungan Weighted Sum, Pemeringkatan & Tie-Breaker."),
        ("BAB 11 PENETAPAN DAN OUTPUT", "Proses Penetapan Bupati, Status Lulus/Tidak Lulus, Daftar Penerima, Rekapitulasi & SK."),
        ("BAB 12 MONITORING DAN AUDIT", "Monitoring Pendaftaran, Verifikasi, Penilaian, Penetapan, serta Audit Log Activity."),
        ("BAB 13 MANAJEMEN AKUN", "Pengelolaan Profil Administrator, Pembaruan Password, dan Prosedur Logout."),
        ("BAB 14 PENUTUP", "Kesimpulan, Lembar Pengesahan & Layanan Kontak Official Dispora Kab. Blitar."),
    ]

    for b_title, b_desc in babs:
        story.append(Paragraph(f"<b>{b_title}</b> — <font color='#64748B'>{b_desc}</font>", ParagraphStyle('TOCItem', fontName='Helvetica', fontSize=8, leading=11, spaceAfter=3)))

    story.append(Spacer(1, 10))

    # BAB 1
    story.append(Paragraph("BAB 1  PENDAHULUAN", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("Sistem Beasiswa Blitar Mengabdi merupakan platform terpadu Pemerintah Kabupaten Blitar (Dinas Kepemudaan dan Olahraga) yang dirancang untuk mengelola pendaftaran, verifikasi, penilaian, hingga penetapan beasiswa daerah secara akuntabel, transparan, dan objektif.", body_style))
    story.append(Paragraph("Manual Book ini disusun sebagai acuan teknis bagi seluruh pemangku kepentingan, mulai dari masyarakat pemohon hingga tingkatan administrator Super Admin, Kabupaten, OPD, Kecamatan, dan Desa/Kelurahan.", body_style))

    # BAB 2
    story.append(Paragraph("BAB 2  AKSES WEBSITE", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("• <b>Halaman Publik:</b> Menampilkan banner slider kegiatan, statistik periode aktif, dan akses cepat ke layanan beasiswa.", bullet_style))
    story.append(Paragraph("• <b>Informasi Beasiswa:</b> Menyediakan rincian 3 program beasiswa (SDSS, Berdaya Berjaya, BBP), jadwal periode, dan persyaratan umum.", bullet_style))
    story.append(Paragraph("• <b>Dokumen Penting:</b> Halaman unduh berkas publik seperti Ebook Panduan Pendaftaran, SK Juknis, dan Template Surat Pernyataan (.docx).", bullet_style))
    story.append(Paragraph("• <b>Pendaftaran:</b> Portal formulir pendaftaran online terbuka tanpa perlu membuat akun pengguna.", bullet_style))
    story.append(Paragraph("• <b>Cek Status Pendaftaran:</b> Halaman pencarian status pengajuan menggunakan 16 digit NIK dan Tahun Pendaftaran.", bullet_style))

    # BAB 3
    story.append(Paragraph("BAB 3  PENDAFTARAN MASYARAKAT", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("Pendaftaran dilakukan secara mandiri melalui 5-Step Wizard:", body_style))
    story.append(Paragraph("1. <b>Memilih Periode, Program & Jalur:</b> Memilih program beasiswa aktif yang berstatus 'Sedang Dibuka'.", bullet_style))
    story.append(Paragraph("2. <b>Step 1 (Identitas):</b> Mengisi NIK (16 digit), Nama Lengkap, Alamat, No. HP/WA, Perguruan Tinggi, dan Program Studi.", bullet_style))
    story.append(Paragraph("3. <b>Step 2 (Data Orang Tua/Wali):</b> Mengisi data Ayah & Ibu, NIK Orang Tua, Pekerjaan, dan Penghasilan.", bullet_style))
    story.append(Paragraph("4. <b>Step 3 (Status Ekonomi):</b> Mengisi Penghasilan Gabungan, Kepemilikan Rumah/Lahan, dan Sertifikat KIP/PKH.", bullet_style))
    story.append(Paragraph("5. <b>Step 4 (Data Akademik):</b> Mengisi nilai IPK / Rapor Terakhir, Prestasi Juara, dan Keaktifan Organisasi.", bullet_style))
    story.append(Paragraph("6. <b>Step 5 (Upload Dokumen):</b> Mengunggah berkas KTP, KK, Surat Permohonan, Surat Pernyataan, dan SPTJM (Maks 10MB PDF/DOCX).", bullet_style))
    story.append(Paragraph("7. <b>Review Pendaftaran & Persetujuan Kebijakan Privasi:</b> Memeriksa ringkasan data dan menyetujui pernyataan keabsahan.", bullet_style))
    story.append(Paragraph("8. <b>Submit:</b> Mengirim pendaftaran. Setelah Submit, data dan berkas <b>TERKUNCI PERMANEN</b> dan tidak dapat diedit kembali.", bullet_style))

    story.append(PageBreak())

    # BAB 4 - 6
    story.append(Paragraph("BAB 4  SUPER ADMIN", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("Super Admin mengakses panel pengelolaan melalui URL terselubung <code>http://localhost:8000/mengabdi</code>.", body_style))
    
    sa_grid = [
        [Paragraph("<b>Sub-Modul Menu</b>", table_th), Paragraph("<b>Fungsi Utama Pengelolaan</b>", table_th)],
        [Paragraph("<b>Dashboard</b>", table_td_bold), Paragraph("Ringkasan statistik pendaftaran, status verifikasi, dan monitoring audit log.", table_td)],
        [Paragraph("<b>Master Referensi</b>", table_td_bold), Paragraph("Pengelolaan data OPD, Kecamatan, Desa/Kelurahan, Perguruan Tinggi, & User Role.", table_td)],
        [Paragraph("<b>Master Konfigurasi</b>", table_td_bold), Paragraph("Pengaturan Periode, Program, Jalur, Persyaratan, Jenis Dokumen, & Tahapan Seleksi.", table_td)],
        [Paragraph("<b>Kriteria, Pilihan & Bobot</b>", table_td_bold), Paragraph("Konfigurasi kriteria SPK Weighted Sum, sub-pilihan skor, dan pembobotan nilai.", table_td)],
        [Paragraph("<b>User Management</b>", table_td_bold), Paragraph("Menambah, mengedit, dan me-reset kata sandi akun administrator berbagai tingkatan.", table_td)],
        [Paragraph("<b>Monitoring & Audit Log</b>", table_td_bold), Paragraph("Memantau log aktivitas sistem secara real-time (Login, Edit, Verifikasi, Penetapan).", table_td)],
        [Paragraph("<b>Ubah Password</b>", table_td_bold), Paragraph("Fasilitas pembaruan kata sandi akun pribadi Super Admin secara aman.", table_td)],
    ]
    sa_t = Table(sa_grid, colWidths=[140, 360])
    sa_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('PADDING', (0,0), (-1,-1), 4.5),
    ]))
    story.append(sa_t)
    story.append(Spacer(1, 10))

    story.append(Paragraph("BAB 5  ADMIN OPD", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("• <b>Dashboard OPD:</b> Menampilkan ringkasan berkas pendaftar yang menjadi wewenang verifikasi OPD terkait.", bullet_style))
    story.append(Paragraph("• <b>Daftar Pendaftar:</b> Memuat daftar pemohon beasiswa beserta status verifikasi dokumennya.", bullet_style))
    story.append(Paragraph("• <b>Dokumen Sesuai Kewenangan:</b> OPD hanya memeriksa dokumen spesifik (misal: Dispora memeriksa sertifikat prestasi, Dinsos memeriksa KIP/PKH).", bullet_style))
    story.append(Paragraph("• <b>Verifikasi Dokumen:</b> Memberikan penilaian status <code>Valid</code>, <code>Tidak Valid</code>, atau <code>Tidak Wajib</code> beserta catatan alasan.", bullet_style))
    story.append(Spacer(1, 8))

    story.append(Paragraph("BAB 6  ADMIN DESA/KELURAHAN", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("Khusus untuk Program Satu Desa Satu Sarjana (SDSS):", body_style))
    story.append(Paragraph("• <b>Dashboard Desa:</b> Ringkasan jumlah pendaftar SDSS di wilayah desa/kelurahan setempat.", bullet_style))
    story.append(Paragraph("• <b>Daftar Pendaftar & Hasil Verifikasi:</b> Memantau hasil pemeriksaan berkas OPD bagi warga desanya.", bullet_style))
    story.append(Paragraph("• <b>Nilai & Ranking Desa:</b> Melihat skor otomatis SPK pendaftar khusus di wilayah desanya.", bullet_style))
    story.append(Paragraph("• <b>Penentuan 1 Calon Penerima:</b> Memilih <b>1 (satu) calon penerima resmi</b> dari desa berdasarkan hasil pertimbangan faktual.", bullet_style))
    story.append(Paragraph("• <b>Surat Rekomendasi:</b> Mengunggah berkas Surat Rekomendasi resmi dari Kepala Desa/Lurah ke dalam sistem.", bullet_style))

    story.append(PageBreak())

    # BAB 7 - 9
    story.append(Paragraph("BAB 7  ADMIN KECAMATAN", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("• <b>Dashboard Kecamatan:</b> Rekapitulasi pendaftar SDSS di seluruh desa/kelurahan dalam wilayah kecamatan.", bullet_style))
    story.append(Paragraph("• <b>Rekomendasi Desa & Surat Rekomendasi:</b> Memeriksa calon pilihan desa dan mengunduh berkas Surat Rekomendasi Kades.", bullet_style))
    story.append(Paragraph("• <b>Verifikasi Kecamatan & Meneruskan:</b> Memberikan validasi peninjauan tingkat kecamatan dan meneruskan berkas ke Kabupaten.", bullet_style))
    story.append(Spacer(1, 8))

    story.append(Paragraph("BAB 8  ADMIN KABUPATEN", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("Admin Kabupaten merupakan pengelola utama penetapan beasiswa daerah:", body_style))
    story.append(Paragraph("• <b>Dashboard Kabupaten:</b> Monitoring menyeluruh seluruh periode, program, verifikasi, dan penetapan.", bullet_style))
    story.append(Paragraph("• <b>Daftar Pendaftar & Verifikasi Administrasi:</b> Memeriksa keabsahan kelengkapan berkas secara komprehensif.", bullet_style))
    story.append(Paragraph("• <b>Wawancara (Berdaya Berjaya):</b> Menginput nilai tes wawancara pemohon melalui modal popup interaktif.", bullet_style))
    story.append(Paragraph("• <b>Penilaian & Ranking:</b> Menjalankan kalkulasi SPK otomatis dan melihat hasil pemeringkatan.", bullet_style))
    story.append(Paragraph("• <b>Penetapan & Output/Draft SK:</b> Menetapkan status kelulusan <code>Lulus — SK Terbit</code> dan menerbitkan Draft SK Bupati.", bullet_style))
    story.append(Spacer(1, 8))

    story.append(Paragraph("BAB 9  WORKFLOW PROGRAM BEASISWA", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    
    wf_grid = [
        [Paragraph("<b>Program Beasiswa</b>", table_th), Paragraph("<b>Alur Kerja (Workflow) Berjenjang</b>", table_th)],
        [Paragraph("<b>Satu Desa Satu Sarjana (SDSS)</b>", table_td_bold), Paragraph("Pendaftar → Verifikasi OPD → Penilaian SPK → Ranking Desa → Desa Pilih 1 Calon + Upload Rekomendasi → Verifikasi Kecamatan → Penetapan Kabupaten → SK Terbit (Selesai).", table_td)],
        [Paragraph("<b>Berdaya Berjaya</b>", table_td_bold), Paragraph("Pendaftar → Verifikasi OPD → Tes Wawancara → Penilaian SPK → Perangkingan Otomatis → Penetapan Kabupaten → SK Terbit (Selesai).", table_td)],
        [Paragraph("<b>Bantuan Biaya Pendidikan (BBP)</b>", table_td_bold), Paragraph("Pendaftar → Verifikasi OPD → Penilaian SPK → Perangkingan Otomatis → Penetapan Kabupaten → SK Terbit (Selesai).", table_td)],
    ]
    wf_t = Table(wf_grid, colWidths=[140, 360])
    wf_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('PADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(wf_t)

    story.append(PageBreak())

    # BAB 10 - 14
    story.append(Paragraph("BAB 10  PENILAIAN DAN RANKING", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("• <b>Kriteria & Bobot Terbobot:</b> Setiap jalur memiliki kriteria spesifik (Ekonomi, IPK, Prestasi, Wawancara) dengan bobot terkonfigurasi.", bullet_style))
    story.append(Paragraph("• <b>Perhitungan Nilai (Weighted Sum):</b> Sistem menghitung Total Skor = $\\sum (\\text{Skor Pilihan} / \\text{Skor Maks} \\times \\text{Bobot})$.", bullet_style))
    story.append(Paragraph("• <b>Ranking & Aturan Nilai Sama (Tie-Breaker):</b> Peringkat diperbarui otomatis. Jika terdapat Total Skor yang sama persis, pendaftar yang <b>mendaftar lebih awal (waktu submit pendaftaran)</b> berada di posisi lebih tinggi.", bullet_style))
    story.append(Spacer(1, 8))

    story.append(Paragraph("BAB 11  PENETAPAN DAN OUTPUT", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("• <b>Proses Penetapan:</b> Admin Kabupaten menetapkan status pendaftar berdasarkan hasil pemeringkatan dan kuota yang tersedia.", bullet_style))
    story.append(Paragraph("• <b>Lulus / Tidak Lulus:</b> Pendaftar yang ditetapkan memperoleh status <code>Lulus — SK Terbit</code>, sedangkan sisanya <code>Tidak Lulus</code>.", bullet_style))
    story.append(Paragraph("• <b>Daftar Penerima & Rekapitulasi:</b> Rekapitulasi data resmi penerima per kecamatan, desa, dan perguruan tinggi.", bullet_style))
    story.append(Paragraph("• <b>Draft SK Bupati:</b> Output administrasi otomatis dokumen naskah Draft Keputusan Bupati Blitar.", bullet_style))
    story.append(Spacer(1, 8))

    story.append(Paragraph("BAB 12  MONITORING DAN AUDIT", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("Super Admin memantau seluruh tahapan secara terpusat melalui Dasbor Monitoring (Pendaftaran, Verifikasi, Penilaian, Penetapan). Seluruh aktivitas penting dicatat pada <b>Audit Log System</b> (IP Address, Timestamp, Action, Old/New Value) demi menjamin transparansi penuh.", body_style))
    story.append(Spacer(1, 8))

    story.append(Paragraph("BAB 13  MANAJEMEN AKUN", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("• <b>Profil:</b> Melihat informasi akun pengguna dan role kewenangan yang dimiliki.", bullet_style))
    story.append(Paragraph("• <b>Ubah Password:</b> Fasilitas mengganti kata sandi akun secara berkala melalui formulir validasi password lama & konfirmasi.", bullet_style))
    story.append(Paragraph("• <b>Logout:</b> Mengakhiri sesi pengguna secara aman dari sistem.", bullet_style))
    story.append(Spacer(1, 8))

    story.append(Paragraph("BAB 14  PENUTUP", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=primary_color, spaceBefore=2, spaceAfter=6))
    story.append(Paragraph("Manual Book ini disusun sebagai pedoman operasional resmi Sistem Beasiswa Blitar Mengabdi V2.2.0. Diharapkan seluruh pihak dapat menjalankan tugas dan fungsinya sesuai dengan standar prosedur yang telah ditetapkan.", body_style))
    story.append(Spacer(1, 8))

    closing_box = [
        [Paragraph("<b>PUSAT LAYANAN INFORMASI & BANTUAN TEKNIS OFFICIAL</b>", ParagraphStyle('CH', fontName='Helvetica-Bold', fontSize=9.5, textColor=dark_color))],
        [Paragraph("• <b>Instansi:</b> Dinas Kepemudaan dan Olahraga (Dispora) Kabupaten Blitar", body_style)],
        [Paragraph("• <b>Contact Person WhatsApp:</b> Bapak Akhyat (0813-3400-1600)", body_style)],
        [Paragraph("• <b>Alamat Kantor:</b> Jl. Raya Sawahan Pojok, Kec. Garum, Kabupaten Blitar", body_style)],
        [Paragraph("• <b>Jam Pelayanan Resmi:</b> Senin — Jumat, 08:00 — 16:00 WIB", body_style)],
    ]
    closing_t = Table(closing_box, colWidths=[500])
    closing_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#FFFBEB")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#F59E0B")),
        ('PADDING', (0,0), (-1,-1), 9),
    ]))
    story.append(closing_t)

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"Full 14 Chapter Manual Book PDF successfully generated at: {filename}")

if __name__ == "__main__":
    public_output = os.path.join(os.getcwd(), "public", "MANUAL_BOOK_SISTEM_BEASISWA_BLITAR_MENGABDI.pdf")
    docs_output = os.path.join(os.getcwd(), "docs", "MANUAL_BOOK_SISTEM_BEASISWA_BLITAR_MENGABDI.pdf")
    
    build_pdf(public_output)
    build_pdf(docs_output)
