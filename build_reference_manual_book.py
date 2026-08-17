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
        
        # Don't draw running header/footer on cover page (page 1)
        if self._pageNumber > 1:
            # Header
            self.setFont("Helvetica-Bold", 8)
            self.setFillColor(colors.HexColor("#2B5C92"))
            self.drawString(36, 815, "Manual Book — Sistem Beasiswa Blitar Mengabdi")
            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#64748B"))
            self.drawRightString(559, 815, "Dispora Kabupaten Blitar")
            
            self.setStrokeColor(colors.HexColor("#E2E8F0"))
            self.setLineWidth(0.75)
            self.line(36, 808, 559, 808)

            # Footer
            self.setStrokeColor(colors.HexColor("#E2E8F0"))
            self.setLineWidth(0.75)
            self.line(36, 42, 559, 42)

            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#64748B"))
            self.drawString(36, 28, "Dokumen ini disusun sebagai panduan penggunaan Sistem Beasiswa Blitar Mengabdi")
            
            page_text = f"{self._pageNumber}/{page_count}"
            self.drawRightString(559, 28, page_text)

        self.restoreState()

def make_alert_box(box_type, title, text, styles):
    """
    Creates styled alert boxes (Tip, Info, Warning, Danger) exactly matching the reference manual book style.
    """
    if box_type == 'tip':
        bg = colors.HexColor("#ECFDF5")
        border = colors.HexColor("#10B981")
        text_c = colors.HexColor("#065F46")
        icon = "💡 <b>Petunjuk:</b> "
    elif box_type == 'info':
        bg = colors.HexColor("#EFF6FF")
        border = colors.HexColor("#3B82F6")
        text_c = colors.HexColor("#1E40AF")
        icon = "ℹ️ <b>Informasi:</b> "
    elif box_type == 'warning':
        bg = colors.HexColor("#FFFBEB")
        border = colors.HexColor("#F59E0B")
        text_c = colors.HexColor("#92400E")
        icon = "⚠️ <b>Perhatian:</b> "
    else: # danger
        bg = colors.HexColor("#FEF2F2")
        border = colors.HexColor("#EF4444")
        text_c = colors.HexColor("#991B1B")
        icon = "🔴 <b>Penting!</b> "

    p_style = ParagraphStyle(
        'AlertP',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12,
        textColor=text_c
    )

    full_text = f"{icon}{title} — {text}" if title else f"{icon}{text}"
    p = Paragraph(full_text, p_style)

    t = Table([[p]], colWidths=[500])
    t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), bg),
        ('BOX', (0,0), (-1,-1), 0.75, border),
        ('PADDING', (0,0), (-1,-1), 8),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ]))
    return t

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
    
    primary_color = colors.HexColor("#2B5C92") # Navy Pekat
    dark_color = colors.HexColor("#0C1446")    # Midnight Navy
    accent_color = colors.HexColor("#FFD800")  # Bumblebee Yellow
    header_table_color = colors.HexColor("#2B5C92")
    text_color = colors.HexColor("#1E293B")
    muted_color = colors.HexColor("#64748B")

    title_style = ParagraphStyle(
        'CoverTitle',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=24,
        leading=28,
        textColor=dark_color,
        alignment=1,
        spaceAfter=10
    )

    h1_style = ParagraphStyle(
        'CustomH1',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=14,
        leading=18,
        textColor=primary_color,
        spaceBefore=14,
        spaceAfter=6,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'CustomH2',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=14,
        textColor=dark_color,
        spaceBefore=10,
        spaceAfter=4,
        keepWithNext=True
    )

    h3_style = ParagraphStyle(
        'CustomH3',
        parent=styles['Heading3'],
        fontName='Helvetica-Bold',
        fontSize=9.5,
        leading=13,
        textColor=primary_color,
        spaceBefore=8,
        spaceAfter=3,
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
    code_style = ParagraphStyle('CodeInline', fontName='Courier-Bold', fontSize=8, textColor=colors.HexColor("#0F172A"))

    story = []

    # ═════════════════════════════════════════════════════════════════════════
    # COVER PAGE (Matching Reference Page 1)
    # ═════════════════════════════════════════════════════════════════════════
    story.append(Spacer(1, 40))
    
    # Pill Badge "📖 MANUAL BOOK"
    badge_data = [[Paragraph("<font color='#FFFFFF'><b>📖 MANUAL BOOK</b></font>", ParagraphStyle('Badge', fontName='Helvetica-Bold', fontSize=10, alignment=1))]]
    badge_table = Table(badge_data, colWidths=[160])
    badge_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), primary_color),
        ('ALIGN', (0,0), (-1,-1), 'CENTER'),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('BOTTOMPADDING', (0,0), (-1,-1), 6),
        ('TOPPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(badge_table)
    story.append(Spacer(1, 25))

    story.append(Paragraph("Sistem Beasiswa Terpadu<br/><b>Blitar Mengabdi</b>", title_style))
    story.append(HRFlowable(width="120", thickness=3, color=accent_color, spaceBefore=4, spaceAfter=14))
    
    story.append(Paragraph("Buku Panduan Pengguna", ParagraphStyle('SubHeader', fontName='Helvetica', fontSize=14, leading=18, textColor=primary_color, alignment=1)))
    story.append(Spacer(1, 20))

    # Pill URL Box
    url_box_data = [[Paragraph("🌐 &nbsp; <b>http://localhost:8000</b> &nbsp; | &nbsp; Login Admin: <code>/mengabdi</code>", ParagraphStyle('UrlBox', fontName='Helvetica', fontSize=10, textColor=dark_color, alignment=1))]]
    url_box_table = Table(url_box_data, colWidths=[420])
    url_box_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F8FAFC")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#CBD5E1")),
        ('ALIGN', (0,0), (-1,-1), 'CENTER'),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('PADDING', (0,0), (-1,-1), 10),
    ]))
    story.append(url_box_table)
    story.append(Spacer(1, 35))

    story.append(Paragraph("<b>Dinas Kepemudaan dan Olahraga</b>", ParagraphStyle('Instansi', fontName='Helvetica-Bold', fontSize=11, textColor=dark_color, alignment=1)))
    story.append(Paragraph("Pemerintah Kabupaten Blitar, Jawa Timur", ParagraphStyle('SubInstansi', fontName='Helvetica', fontSize=10, textColor=muted_color, alignment=1)))
    story.append(Spacer(1, 30))

    story.append(Paragraph("<i>Dokumen ini disusun sebagai panduan penggunaan resmi Sistem Informasi Manajemen Beasiswa Terpadu Kabupaten Blitar (Beasiswa Blitar Mengabdi) V2.2.0.</i>", ParagraphStyle('ItalicCover', fontName='Helvetica-Oblique', fontSize=9, leading=13, textColor=muted_color, alignment=1)))

    story.append(PageBreak())

    # ═════════════════════════════════════════════════════════════════════════
    # DAFTAR ISI (Matching Reference Page 2 & 3)
    # ═════════════════════════════════════════════════════════════════════════
    story.append(Paragraph("Daftar Isi", ParagraphStyle('DaftarIsiTitle', fontName='Helvetica-Bold', fontSize=20, textColor=dark_color, alignment=1, spaceAfter=15)))

    toc_table_data = [
        [Paragraph("<b>NO.</b>", table_th), Paragraph("<b>BAB / SUB-BAB</b>", table_th)],
        
        # BAB 1
        [Paragraph("<b>1</b>", table_td_bold), Paragraph("<b>Pendahuluan</b>", table_td_bold)],
        [Paragraph("1.1", table_td), Paragraph("Tentang Aplikasi", table_td)],
        [Paragraph("1.2", table_td), Paragraph("Pengguna Sistem", table_td)],
        [Paragraph("1.3", table_td), Paragraph("Alamat Akses", table_td)],
        [Paragraph("1.4", table_td), Paragraph("Persyaratan Sistem", table_td)],
        
        # BAB 2
        [Paragraph("<b>2</b>", table_td_bold), Paragraph("<b>Panduan Pemohon / Masyarakat (Halaman Publik)</b>", table_td_bold)],
        [Paragraph("2.1", table_td), Paragraph("Halaman Beranda", table_td)],
        [Paragraph("2.2", table_td), Paragraph("Halaman Informasi & Dokumen Penting (Juknis)", table_td)],
        [Paragraph("2.3", table_td), Paragraph("Halaman Persyaratan & SPK", table_td)],
        [Paragraph("2.4", table_td), Paragraph("Halaman Hasil Seleksi & Penetapan", table_td)],
        [Paragraph("2.5", table_td), Paragraph("Melakukan Pendaftaran Online (Alur 5-Step)", table_td)],
        [Paragraph("2.6", table_td), Paragraph("Review Pendaftaran & Persetujuan Kebijakan Privasi", table_td)],
        [Paragraph("2.7", table_td), Paragraph("Penguncian Data & Submit Pendaftaran", table_td)],
        [Paragraph("2.8", table_td), Paragraph("Cek Status Pendaftaran & Cetak Tanda Bukti (PDF)", table_td)],
        [Paragraph("2.9", table_td), Paragraph("Alur Pendaftaran & Cek Status (Ringkasan Workflow)", table_td)],
        
        # BAB 3
        [Paragraph("<b>3</b>", table_td_bold), Paragraph("<b>Panduan Admin (Panel Administrasi)</b>", table_td_bold)],
        [Paragraph("3.1", table_td), Paragraph("Login ke Panel Admin (Route <code>/mengabdi</code>)", table_td)],
        [Paragraph("3.2", table_td), Paragraph("Dashboard Administrator", table_td)],
        [Paragraph("3.3", table_td), Paragraph("Navigasi Sidebar & Struktur Hak Akses", table_td)],
        [Paragraph("3.4", table_td), Paragraph("Panduan Super Admin (Master Data, Konfigurasi SPK & Audit Log)", table_td)],
        [Paragraph("3.5", table_td), Paragraph("Panduan Admin OPD (Verifikasi Dokumen Kewenangan)", table_td)],
        [Paragraph("3.6", table_td), Paragraph("Panduan Admin Desa/Kelurahan (Verifikasi Faktual & Rekomendasi SDSS)", table_td)],
        [Paragraph("3.7", table_td), Paragraph("Panduan Admin Kecamatan (Peninjauan Rekomendasi SDSS)", table_td)],
        [Paragraph("3.8", table_td), Paragraph("Panduan Admin Kabupaten (Wawancara, Penilaian, Penetapan & Output)", table_td)],
        [Paragraph("3.9", table_td), Paragraph("Manajemen Akun (Profil, Ubah Password, Logout)", table_td)],
        
        # BAB 4
        [Paragraph("<b>4</b>", table_td_bold), Paragraph("<b>Troubleshooting / FAQ & Layanan Bantuan</b>", table_td_bold)],
        [Paragraph("4.1", table_td), Paragraph("Masalah Umum Pemohon", table_td)],
        [Paragraph("4.2", table_td), Paragraph("Masalah Umum Admin", table_td)],
        [Paragraph("4.3", table_td), Paragraph("Kontak Layanan Bantuan Official Dispora", table_td)],
    ]

    toc_table = Table(toc_table_data, colWidths=[50, 450])
    toc_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#E2E8F0")),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('PADDING', (0,0), (-1,-1), 4.5),
        ('BACKGROUND', (0,1), (-1,1), colors.HexColor("#F1F5F9")),
        ('BACKGROUND', (0,6), (-1,6), colors.HexColor("#F1F5F9")),
        ('BACKGROUND', (0,16), (-1,16), colors.HexColor("#F1F5F9")),
        ('BACKGROUND', (0,26), (-1,26), colors.HexColor("#F1F5F9")),
    ]))
    story.append(toc_table)

    story.append(PageBreak())

    # ═════════════════════════════════════════════════════════════════════════
    # BAB 1: PENDAHULUAN (Matching Reference Page 4)
    # ═════════════════════════════════════════════════════════════════════════
    story.append(Paragraph("1. Pendahuluan", h1_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=2, spaceAfter=8))

    story.append(Paragraph("1.1 Tentang Aplikasi", h2_style))
    story.append(Paragraph("<b>Sistem Beasiswa Blitar Mengabdi</b> adalah aplikasi web terpadu Pemerintah Kabupaten Blitar yang dirancang untuk mengoperasionalkan seluruh tahapan seleksi beasiswa daerah (Satu Desa Satu Sarjana, Berdaya Berjaya, dan Bantuan Biaya Pendidikan). Sistem ini memungkinkan masyarakat melakukan pendaftaran mandiri tanpa akun secara transparan, serta memberikan panel administrasi berjenjang bagi pengelola Kabupaten, OPD, Kecamatan, dan Desa.", body_style))

    story.append(Paragraph("1.2 Pengguna Sistem", h2_style))
    story.append(Paragraph("Sistem ini memiliki <b>enam kelompok pengguna</b> dengan peran dan batas hak akses yang spesifik:", body_style))

    user_types_data = [
        [Paragraph("<b>JENIS PENGGUNA</b>", table_th), Paragraph("<b>AKSES</b>", table_th), Paragraph("<b>DESKRIPSI HAK AKSES & PERAN UTAMA</b>", table_th)],
        
        [Paragraph("<b>Pemohon / Publik (Guest)</b>", table_td_bold), Paragraph("Halaman Utama & Portal Pendaftaran", table_td), Paragraph("Masyarakat umum / mahasiswa yang mendaftar beasiswa 5-step, mengunduh Juknis, dan mengecek status kelulusan via NIK.", table_td)],
        
        [Paragraph("<b>Super Admin</b>", table_td_bold), Paragraph("Panel Administrasi (<code>/mengabdi</code>)", table_td), Paragraph("Pengelolaan penuh master data referensi, pengguna, konfigurasi SPK, dokumen publik, dan Audit Log aktivitas.", table_td)],
        
        [Paragraph("<b>Admin Kabupaten</b>", table_td_bold), Paragraph("Panel Administrasi (<code>/mengabdi</code>)", table_td), Paragraph("Pemeriksaan administrasi akhir, input tes wawancara, perangkingan SPK, penetapan SK Bupati, dan output.", table_td)],
        
        [Paragraph("<b>Admin OPD</b>", table_td_bold), Paragraph("Panel Administrasi (<code>/mengabdi</code>)", table_td), Paragraph("Verifikasi keabsahan dokumen pendaftaran sesuai wewenang dinas terkait (Dispora, Dinsos, dll).", table_td)],
        
        [Paragraph("<b>Admin Kecamatan</b>", table_td_bold), Paragraph("Panel Administrasi (<code>/mengabdi</code>)", table_td), Paragraph("Peninjauan berkas pendaftar SDSS dan validasi Surat Rekomendasi tingkat kecamatan.", table_td)],
        
        [Paragraph("<b>Admin Desa/Kelurahan</b>", table_td_bold), Paragraph("Panel Administrasi (<code>/mengabdi</code>)", table_td), Paragraph("Verifikasi faktual domisili, melihat nilai SPK desa, memilih 1 calon SDSS, dan mengunggah Surat Rekomendasi.", table_td)],
    ]
    user_types_t = Table(user_types_data, colWidths=[120, 110, 270])
    user_types_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('PADDING', (0,0), (-1,-1), 4.5),
    ]))
    story.append(user_types_t)
    story.append(Spacer(1, 8))

    story.append(Paragraph("1.3 Alamat Akses", h2_style))
    
    url_access_data = [
        [Paragraph("<b>HALAMAN / KEBUTUHAN</b>", table_th), Paragraph("<b>URL / ALAMAT AKSES</b>", table_th)],
        [Paragraph("Website Publik (Beranda)", table_td_bold), Paragraph("<code>http://localhost:8000/</code>", table_td)],
        [Paragraph("Informasi & Juknis", table_td_bold), Paragraph("<code>http://localhost:8000/informasi</code>", table_td)],
        [Paragraph("Formulir Pendaftaran Online", table_td_bold), Paragraph("<code>http://localhost:8000/pendaftaran</code>", table_td)],
        [Paragraph("Cek Status & Cetak Bukti", table_td_bold), Paragraph("<code>http://localhost:8000/cek-status</code>", table_td)],
        [Paragraph("Login Panel Admin (Terselubung)", table_td_bold), Paragraph("<code>http://localhost:8000/mengabdi</code>", table_td)],
    ]
    url_access_t = Table(url_access_data, colWidths=[180, 320])
    url_access_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('PADDING', (0,0), (-1,-1), 4.5),
    ]))
    story.append(url_access_t)
    story.append(Spacer(1, 8))

    story.append(Paragraph("1.4 Persyaratan Sistem", h2_style))
    story.append(Paragraph("Untuk mengakses website ini secara optimal, pengguna memerlukan:", body_style))
    story.append(Paragraph("• <b>Browser Web:</b> Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari (versi terbaru).", bullet_style))
    story.append(Paragraph("• <b>Koneksi Internet:</b> Stabil (disarankan kecepatan minimal 2 Mbps).", bullet_style))
    story.append(Paragraph("• <b>Perangkat:</b> Desktop/Laptop, Tablet, atau Smartphone (Layout Full Responsive).", bullet_style))

    story.append(PageBreak())

    # ═════════════════════════════════════════════════════════════════════════
    # BAB 2: PANDUAN PEMOHON / MASYARAKAT (Matching Reference Page 5 - 9)
    # ═════════════════════════════════════════════════════════════════════════
    story.append(Paragraph("2. Panduan Pemohon (Halaman Publik)", h1_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=2, spaceAfter=8))
    story.append(Paragraph("Bagian ini menjelaskan cara menggunakan website dari sisi pemohon / masyarakat umum.", body_style))

    story.append(Paragraph("2.1 Halaman Beranda", h2_style))
    story.append(Paragraph("Halaman beranda merupakan halaman pertama yang tampil saat mengakses website utama:", body_style))

    home_comp_data = [
        [Paragraph("<b>KOMPONEN</b>", table_th), Paragraph("<b>DESKRIPSI & FUNGSI UTAMA</b>", table_th)],
        [Paragraph("Hero Section", table_td_bold), Paragraph("Banner utama dengan slider foto kegiatan, judul Beasiswa Blitar Mengabdi, dan tombol CTA 'Daftar Sekarang'.", table_td)],
        [Paragraph("Informasi Program", table_td_bold), Paragraph("Kartu ringkasan 3 program beasiswa utama (SDSS, Berdaya Berjaya, dan BBP).", table_td)],
        [Paragraph("Statistik Periode", table_td_bold), Paragraph("Jumlah pendaftar, kuota penerima, dan periode aktif saat ini.", table_td)],
        [Paragraph("Alur Seleksi", table_td_bold), Paragraph("Visual tahapan alur seleksi pendaftaran hingga pengumuman SK.", table_td)],
        [Paragraph("FAQ Accordion", table_td_bold), Paragraph("Daftar pertanyaan yang sering diajukan beserta jawaban interaktif.", table_td)],
    ]
    home_comp_t = Table(home_comp_data, colWidths=[130, 370])
    home_comp_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('PADDING', (0,0), (-1,-1), 4.5),
    ]))
    story.append(home_comp_t)
    story.append(Spacer(1, 8))

    story.append(make_alert_box('tip', 'Navigasi Responsive', 'Navbar dilengkapi efek transparan pada bagian atas dan berubah solid saat digulirkan ke bawah.', styles))
    story.append(Spacer(1, 10))

    story.append(Paragraph("2.2 Halaman Informasi & Dokumen Penting (Juknis)", h2_style))
    story.append(Paragraph("Halaman ini menyajikan petunjuk teknis (Juknis) lengkap dan fasilitas pengunduhan berkas publik resmi:", body_style))
    story.append(Paragraph("• <b>Ebook Panduan Pendaftaran 2026 (.pdf):</b> Panduan lengkap petunjuk teknis pemohon.", bullet_style))
    story.append(Paragraph("• <b>SK Juknis Beasiswa Blitar Mengabdi (.pdf):</b> Surat Keputusan Kepala Dinas Juknis resmi.", bullet_style))
    story.append(Paragraph("• <b>Template Surat Permohonan & Pernyataan (.docx):</b> Berkas format resmi yang dapat diisi pemohon.", bullet_style))
    story.append(Spacer(1, 10))

    story.append(Paragraph("2.3 Melakukan Pendaftaran Online (Alur 5-Step)", h2_style))
    story.append(Paragraph("Akses halaman pendaftaran via menu <b>Pendaftaran → Formulir Pendaftaran</b>. Isi formulir secara berurutan:", body_style))

    story.append(make_alert_box('info', 'Status Pendaftaran', 'Pastikan periode pendaftaran dalam status DIBUKA sebelum mengisi formulir.', styles))
    story.append(Spacer(1, 8))

    # Step Table (Matching Reference Page 7 style)
    step1_data = [
        [Paragraph("<b>FIELD (STEP 1: IDENTITAS)</b>", table_th), Paragraph("<b>KETERANGAN</b>", table_th), Paragraph("<b>WAJIB</b>", table_th)],
        [Paragraph("NIK", table_td_bold), Paragraph("Nomor Induk Kependudukan 16 digit resmi Kab. Blitar", table_td), Paragraph("✔", table_td_bold)],
        [Paragraph("Nama Lengkap", table_td_bold), Paragraph("Nama pemohon sesuai KTP / Kartu Keluarga", table_td), Paragraph("✔", table_td_bold)],
        [Paragraph("Alamat & Wilayah", table_td_bold), Paragraph("Alamat domisili, RT/RW, Kecamatan, dan Desa", table_td), Paragraph("✔", table_td_bold)],
        [Paragraph("No. HP / WhatsApp", table_td_bold), Paragraph("Nomor WhatsApp aktif untuk pemberitahuan", table_td), Paragraph("✔", table_td_bold)],
        [Paragraph("Perguruan Tinggi & Prodi", table_td_bold), Paragraph("Nama kampus dan program studi pemohon", table_td), Paragraph("✔", table_td_bold)],
    ]
    step1_t = Table(step1_data, colWidths=[150, 290, 60])
    step1_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('ALIGN', (2,0), (2,-1), 'CENTER'),
        ('PADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(step1_t)
    story.append(Spacer(1, 8))

    step5_data = [
        [Paragraph("<b>FIELD (STEP 5: UNGGAH DOKUMEN)</b>", table_th), Paragraph("<b>KETERANGAN (PDF / DOCX MAKS 10 MB)</b>", table_th), Paragraph("<b>WAJIB</b>", table_th)],
        [Paragraph("KTP Pemohon", table_td_bold), Paragraph("Scan KTP Asli / Surat Keterangan Domisili", table_td), Paragraph("✔", table_td_bold)],
        [Paragraph("Kartu Keluarga (KK)", table_td_bold), Paragraph("Scan Kartu Keluarga Kabupaten Blitar", table_td), Paragraph("✔", table_td_bold)],
        [Paragraph("Surat Permohonan", table_td_bold), Paragraph("Surat Permohonan resmi sesuai format program", table_td), Paragraph("✔", table_td_bold)],
        [Paragraph("Surat Pernyataan", table_td_bold), Paragraph("Surat Pernyataan bermaterai 10.000", table_td), Paragraph("✔", table_td_bold)],
        [Paragraph("SPTJM Kebenaran Data", table_td_bold), Paragraph("Surat Pertanggungjawaban Mutlak Kebenaran Dokumen", table_td), Paragraph("✔", table_td_bold)],
        [Paragraph("Berkas Pendukung", table_td_bold), Paragraph("Sertifikat Prestasi / Kartu KIP / PKH / SKTM", table_td), Paragraph("✖ (Opsional)", table_td)],
    ]
    step5_t = Table(step5_data, colWidths=[150, 290, 60])
    step5_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('ALIGN', (2,0), (2,-1), 'CENTER'),
        ('PADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(step5_t)
    story.append(Spacer(1, 10))

    story.append(make_alert_box('warning', 'Penguncian Permanen Data', 'Setelah tombol Submit diklik, seluruh data dan berkas akan TERKUNCI PERMANEN dan tidak dapat diubah kembali.', styles))
    story.append(Spacer(1, 10))

    story.append(Paragraph("2.4 Cek Status Pendaftaran & Cetak Tanda Bukti", h2_style))
    story.append(Paragraph("Pemohon dapat mengecek kelulusan dan mencetak tanda bukti resmi:", body_style))
    story.append(Paragraph("1. Akses menu <b>Pendaftaran → Cek Status & Cetak Bukti</b>.", bullet_style))
    story.append(Paragraph("2. Masukkan <b>16 Digit NIK</b> dan Pilih <b>Tahun Pendaftaran</b>.", bullet_style))
    story.append(Paragraph("3. Klik <b>'Cek Status'</b>. Grafik Stepper 5 Stage alur seleksi akan tampil.", bullet_style))
    story.append(Paragraph("4. Klik tombol <b>'Cetak Tanda Bukti Pendaftaran (PDF)'</b> untuk mengunduh dokumen resmi.", bullet_style))

    story.append(Spacer(1, 10))
    story.append(Paragraph("2.5 Alur Pendaftaran (Ringkasan Workflow Diagram)", h2_style))

    # Diagram Box (Matching Reference Page 9)
    diag_text = (
        "Buka Halaman Pendaftaran → Pilih Program & Jalur Beasiswa\n"
        "↓\n"
        "Pengisian Step 1 (Identitas Pemohon) → Step 2 (Orang Tua) → Step 3 (Ekonomi) → Step 4 (Akademik)\n"
        "↓\n"
        "Step 5 (Upload Dokumen Persyaratan PDF/DOCX maks 10MB)\n"
        "↓\n"
        "Review Pendaftaran & Centang Persetujuan Kebijakan Privasi\n"
        "↓\n"
        "Klik 'Submit Pendaftaran' → Data & Dokumen Terkunci Permanen\n"
        "↓\n"
        "Proses Verifikasi Berkelanjutan (OPD / Desa / Kecamatan / Kabupaten)\n"
        "↓\n"
        "Cek Status via NIK → Cetak / Simpan Tanda Bukti Pendaftaran Resmi (PDF)"
    )
    diag_p = Paragraph(diag_text.replace("\n", "<br/>"), ParagraphStyle('DiagP', fontName='Courier', fontSize=8, leading=12, textColor=dark_color))
    diag_t = Table([[diag_p]], colWidths=[500])
    diag_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F8FAFC")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#CBD5E1")),
        ('PADDING', (0,0), (-1,-1), 10),
        ('ALIGN', (0,0), (-1,-1), 'CENTER'),
    ]))
    story.append(diag_t)

    story.append(PageBreak())

    # ═════════════════════════════════════════════════════════════════════════
    # BAB 3: PANDUAN ADMIN (PANEL ADMINISTRASI) (Matching Reference Page 10 - 22)
    # ═════════════════════════════════════════════════════════════════════════
    story.append(Paragraph("3. Panduan Admin (Panel Administrasi)", h1_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=2, spaceAfter=8))
    story.append(Paragraph("Bagian ini menjelaskan cara menggunakan panel administrasi bagi petugas dan pengelola beasiswa.", body_style))

    story.append(Paragraph("3.1 Login ke Panel Admin", h2_style))
    story.append(Paragraph("1. Buka browser dan ketik URL terselubung: <code>http://localhost:8000/mengabdi</code>.", bullet_style))
    story.append(Paragraph("2. Masukkan <b>Username</b> dan <b>Password</b> akun administrator Anda.", bullet_style))
    story.append(Paragraph("3. Klik tombol <b>'Masuk Ke Dashboard'</b>.", bullet_style))
    story.append(Spacer(1, 6))

    story.append(make_alert_box('warning', 'Keamanan Percobaan Login', 'Percobaan login dibatasi maksimal 5 kali per menit. Jika melebihi batas, akun akan terkunci sementara.', styles))
    story.append(Spacer(1, 10))

    story.append(Paragraph("3.2 Dashboard Administrator", h2_style))
    story.append(Paragraph("Setelah berhasil login, administrator diarahkan ke halaman Dashboard sesuai role kewenangannya:", body_style))

    dash_cards_data = [
        [Paragraph("<b>KARTU STATISTIK</b>", table_th), Paragraph("<b>DESKRIPSI DATA DISPLAY</b>", table_th)],
        [Paragraph("Total Pendaftar", table_td_bold), Paragraph("Jumlah akumulasi pendaftaran yang masuk pada periode aktif.", table_td)],
        [Paragraph("Status Verifikasi", table_td_bold), Paragraph("Jumlah berkas dalam status Belum Diverifikasi, Valid, dan Ditolak.", table_td)],
        [Paragraph("Pencapaian SPK", table_td_bold), Paragraph("Jumlah pendaftar yang telah dihitung skor SPK Weighted Sum-nya.", table_td)],
        [Paragraph("Penetapan SK", table_td_bold), Paragraph("Jumlah pendaftar yang telah resmi ditetapkan Lulus SK Bupati.", table_td)],
    ]
    dash_cards_t = Table(dash_cards_data, colWidths=[140, 360])
    dash_cards_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('PADDING', (0,0), (-1,-1), 4.5),
    ]))
    story.append(dash_cards_t)
    story.append(Spacer(1, 10))

    story.append(Paragraph("3.3 Panduan Super Admin", h2_style))
    story.append(Paragraph("Super Admin mengelola seluruh konfigurasi sistem melalui menu sidebar:", body_style))
    story.append(Paragraph("• <b>Master Data:</b> Kelola Pengguna, OPD, Kecamatan, Desa/Kelurahan, dan Perguruan Tinggi.", bullet_style))
    story.append(Paragraph("• <b>Master Konfigurasi:</b> Kelola Periode, Program Beasiswa, Jalur, Persyaratan, dan Tahapan Seleksi.", bullet_style))
    story.append(Paragraph("• <b>Konfigurasi SPK:</b> Pengaturan Kriteria, Sub-pilihan Nilai, dan Pembobotan Terbobot.", bullet_style))
    story.append(Paragraph("• <b>Dokumen Publik:</b> Mengunggah Ebook Panduan dan SK Juknis untuk halaman depan.", bullet_style))
    story.append(Paragraph("• <b>Audit Log System:</b> Melihat riwayat rekaman seluruh aktivitas administrator secara terperinci.", bullet_style))
    story.append(Spacer(1, 10))

    story.append(Paragraph("3.4 Panduan Admin OPD (Verifikasi Dokumen)", h2_style))
    story.append(Paragraph("Admin OPD memverifikasi keabsahan fisik dokumen pemohon sesuai instansi wewenangnya. Klik ikon pensil untuk memberi status <code>Valid</code>, <code>Tidak Valid</code>, atau <code>Tidak Wajib</code> beserta Catatan Alasan.", body_style))
    story.append(Spacer(1, 10))

    story.append(Paragraph("3.5 Panduan Admin Desa/Kelurahan (Khusus SDSS)", h2_style))
    story.append(Paragraph("Admin Desa melihat data pendaftar SDSS di desanya, mengecek skor SPK otomatis, menentukan <b>1 calon penerima desa</b>, dan mengunggah berkas Surat Rekomendasi Kades/Lurah.", body_style))
    story.append(Spacer(1, 10))

    story.append(Paragraph("3.6 Panduan Admin Kabupaten (Wawancara & Penetapan SK)", h2_style))
    story.append(Paragraph("Admin Kabupaten menginput skor tes wawancara (Berdaya Berjaya), mengeksekusi kalkulasi SPK, menetapkan status <code>Lulus — SK Terbit</code>, dan menerbitkan Draft SK Bupati.", body_style))

    story.append(PageBreak())

    # ═════════════════════════════════════════════════════════════════════════
    # BAB 4: TROUBLESHOOTING / FAQ (Matching Reference Page 22 - 24)
    # ═════════════════════════════════════════════════════════════════════════
    story.append(Paragraph("4. Troubleshooting / FAQ", h1_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=2, spaceAfter=8))

    story.append(Paragraph("4.1 Masalah Umum Pemohon", h2_style))
    
    faq_user_data = [
        [Paragraph("<b>MASALAH</b>", table_th), Paragraph("<b>PENYEBAB</b>", table_th), Paragraph("<b>SOLUSI</b>", table_th)],
        [Paragraph("NIK dinyatakan 'Sudah Terdaftar'", table_td_bold), Paragraph("NIK sudah pernah digunakan mendaftar atau penerima tahun lalu.", table_td), Paragraph("Gunakan NIK lain atau daftar program lain jika status sebelumnya ditolak.", table_td)],
        [Paragraph("Gagal upload berkas", table_td_bold), Paragraph("Ukuran berkas melebihi 10MB atau format bukan PDF/DOCX.", table_td), Paragraph("Kecilkan ukuran file (compress) dan komparasi ke format PDF/DOCX.", table_td)],
        [Paragraph("Cek Status tidak ditemukan", table_td_bold), Paragraph("Salah memasukkan 16 digit NIK atau tahun pendaftaran.", table_td), Paragraph("Periksa kembali NIK pada KTP/KK dan pilih tahun pendaftaran yang tepat.", table_td)],
    ]
    faq_user_t = Table(faq_user_data, colWidths=[130, 160, 210])
    faq_user_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('PADDING', (0,0), (-1,-1), 4.5),
    ]))
    story.append(faq_user_t)
    story.append(Spacer(1, 10))

    story.append(Paragraph("4.2 Masalah Umum Admin", h2_style))
    
    faq_admin_data = [
        [Paragraph("<b>MASALAH</b>", table_th), Paragraph("<b>PENYEBAB</b>", table_th), Paragraph("<b>SOLUSI</b>", table_th)],
        [Paragraph("Tidak bisa login admin", table_td_bold), Paragraph("Username/password salah atau status akun nonaktif.", table_td), Paragraph("Pastikan kredensial benar atau hubungi Super Admin untuk reset password.", table_td)],
        [Paragraph("Login terblokir sementara", table_td_bold), Paragraph("Terlalu banyak percobaan login gagal (>5x/menit).", table_td), Paragraph("Tunggu 1 menit lalu coba melakukan login kembali.", table_td)],
        [Paragraph("Dokumen tidak muncul di OPD", table_td_bold), Paragraph("Pemetaan jenis dokumen ke OPD belum dikonfigurasi.", table_td), Paragraph("Super Admin harus memetakan kewenangan jenis dokumen ke OPD di master.", table_td)],
    ]
    faq_admin_t = Table(faq_admin_data, colWidths=[130, 160, 210])
    faq_admin_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('PADDING', (0,0), (-1,-1), 4.5),
    ]))
    story.append(faq_admin_t)
    story.append(Spacer(1, 12))

    story.append(Paragraph("4.3 Kontak Layanan Bantuan", h2_style))
    story.append(Paragraph("Jika mengalami kendala teknis yang tidak dapat diatasi, silakan hubungi:", body_style))
    story.append(Spacer(1, 4))

    contact_data = [
        [Paragraph("<b>INFORMASI</b>", table_th), Paragraph("<b>DETAIL CONTACT PERSON OFFICIAL</b>", table_th)],
        [Paragraph("Instansi Pengelola", table_td_bold), Paragraph("Dinas Kepemudaan dan Olahraga (Dispora) Kabupaten Blitar", table_td)],
        [Paragraph("Penanggung Jawab", table_td_bold), Paragraph("Bapak Akhyat", table_td)],
        [Paragraph("WhatsApp / Telepon", table_td_bold), Paragraph("0813-3400-1600", table_td)],
        [Paragraph("Alamat Sekretariat", table_td_bold), Paragraph("Jl. Raya Sawahan Pojok, Kec. Garum, Kabupaten Blitar", table_td)],
        [Paragraph("Jam Pelayanan", table_td_bold), Paragraph("Senin — Jumat, 08:00 — 16:00 WIB", table_td)],
    ]
    contact_t = Table(contact_data, colWidths=[140, 360])
    contact_t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), header_table_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('PADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(contact_t)
    story.append(Spacer(1, 20))

    story.append(Paragraph("<b>— Akhir Dokumen Manual Book —</b>", ParagraphStyle('EndDoc', fontName='Helvetica-Bold', fontSize=10, textColor=dark_color, alignment=1)))
    story.append(Spacer(1, 10))
    story.append(Paragraph("<i>Dokumen ini disusun untuk mendukung penggunaan Sistem Beasiswa Blitar Mengabdi.<br/>Pemerintah Kabupaten Blitar, Jawa Timur, Indonesia.</i>", ParagraphStyle('EndDocItalic', fontName='Helvetica-Oblique', fontSize=8.5, leading=12, textColor=muted_color, alignment=1)))

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"Reference-style Manual Book PDF successfully generated at: {filename}")

if __name__ == "__main__":
    public_output = os.path.join(os.getcwd(), "public", "MANUAL_BOOK_SISTEM_BEASISWA_BLITAR_MENGABDI.pdf")
    docs_output = os.path.join(os.getcwd(), "docs", "MANUAL_BOOK_SISTEM_BEASISWA_BLITAR_MENGABDI.pdf")
    
    build_pdf(public_output)
    build_pdf(docs_output)
