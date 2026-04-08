<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f6f6f8; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1152d4, #1d4ed8); padding: 40px 32px; text-align: center; }
        .header h1 { color: white; font-size: 24px; font-weight: 800; margin-bottom: 6px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 14px; }
        .body { padding: 32px; }
        .badge { display: inline-block; background: #fee2e2; color: #991b1b; padding: 8px 16px; border-radius: 100px; font-weight: 700; font-size: 14px; margin-bottom: 24px; }
        .body h2 { color: #0f172a; font-size: 20px; font-weight: 700; margin-bottom: 12px; }
        .body p { color: #475569; line-height: 1.7; font-size: 15px; margin-bottom: 16px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .info-box h3 { color: #0f172a; font-size: 13px; font-weight: 700; margin-bottom: 12px; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 32px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>MagangDPMPTSP</h1>
        <p>Sistem Informasi Magang</p>
    </div>
    <div class="body">
        <div class="badge">Pendaftaran Tidak Diterima</div>
        <h2>Halo, {{ $pendaftaran->nama_lengkap }}</h2>
        <p>Terima kasih telah mendaftar program magang di <strong>DPMPTSP</strong>. Setelah melalui proses seleksi, kami menyampaikan bahwa pendaftaran Anda <strong>belum dapat kami terima</strong> pada periode ini.</p>

        @if($pendaftaran->catatan_admin && !str_contains($pendaftaran->catatan_admin, '[SISTEM]'))
        <div class="info-box">
            <h3>Alasan dari Admin</h3>
            <p style="margin:0;">{{ $pendaftaran->catatan_admin }}</p>
        </div>
        @endif

        <p>Kami mendorong Anda untuk terus mengembangkan diri dan mencoba kembali pada periode berikutnya.</p>
        <p>Salam,<br><strong>Tim MagangDPMPTSP</strong></p>
    </div>
    <div class="footer">
        © {{ date('Y') }} MagangDPMPTSP. Email ini dikirim otomatis, mohon tidak membalas.
    </div>
</div>
</body>
</html>
