<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Pendaftaran Diterima</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f6f6f8; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1152d4, #1d4ed8); padding: 40px 32px; text-align: center; }
        .header h1 { color: white; font-size: 24px; font-weight: 800; margin-bottom: 6px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 14px; }
        .body { padding: 32px; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; padding: 8px 16px; border-radius: 100px; font-weight: 700; font-size: 14px; margin-bottom: 24px; }
        .body h2 { color: #0f172a; font-size: 20px; font-weight: 700; margin-bottom: 12px; }
        .body p { color: #475569; line-height: 1.7; font-size: 15px; margin-bottom: 16px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .info-box h3 { color: #0f172a; font-size: 13px; font-weight: 700; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #64748b; }
        .info-row .value { color: #0f172a; font-weight: 600; }
        .credential-box { background: #eff6ff; border: 2px solid #bfdbfe; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .credential-box h3 { color: #1e40af; font-size: 13px; font-weight: 700; margin-bottom: 12px; text-transform: uppercase; }
        .credential-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #bfdbfe; font-size: 14px; }
        .credential-row:last-child { border-bottom: none; }
        .credential-row .label { color: #3b82f6; }
        .credential-row .value { color: #1e40af; font-weight: 700; font-family: monospace; font-size: 15px; }
        .btn { display: block; background: #1152d4; color: white !important; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-weight: 700; font-size: 15px; text-align: center; margin: 24px 0; }
        .warning { background: #fef9c3; border: 1px solid #fde047; border-radius: 10px; padding: 14px 16px; font-size: 13px; color: #854d0e; margin: 16px 0; }
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
        <div class="badge">Pendaftaran Diterima</div>
        <h2>Selamat, {{ $pendaftaran->nama_lengkap }}!</h2>
        <p>Kami dengan senang hati memberitahukan bahwa pendaftaran magang Anda di <strong>DPMPTSP</strong> telah <strong>diterima</strong>. Selamat bergabung!</p>

        <div class="info-box">
            <h3>Detail Pendaftaran</h3>
            <div class="info-row">
                <span class="label">Nama:</span>
                <span class="value">{{ $pendaftaran->nama_lengkap }}</span>
            </div>
            <div class="info-row">
                <span class="label">Universitas:</span>
                <span class="value">{{ $pendaftaran->universitas }}</span>
            </div>
            <div class="info-row">
                <span class="label">Jurusan:</span>
                <span class="value">{{ $pendaftaran->jurusan }}</span>
            </div>
            @if($pendaftaran->tanggal_mulai)
            <div class="info-row">
                <span class="label">Tanggal Mulai:</span>
                <span class="value">{{ \Carbon\Carbon::parse($pendaftaran->tanggal_mulai)->isoFormat('D MMMM Y') }}</span>
            </div>
            @endif
            @if($pendaftaran->tanggal_selesai)
            <div class="info-row">
                <span class="label">Tanggal Selesai:</span>
                <span class="value">{{ \Carbon\Carbon::parse($pendaftaran->tanggal_selesai)->isoFormat('D MMMM Y') }}</span>
            </div>
            @endif
        </div>

        <div class="credential-box">
            <h3>Akun Portal Magang Anda</h3>
            <div class="credential-row">
                <span class="label">URL Portal:</span>
                <span class="value">{{ config('app.url') }}/login</span>
            </div>
            <div class="credential-row">
                <span class="label">Email:</span>
                <span class="value">{{ $pendaftaran->email }}</span>
            </div>
            <div class="credential-row">
                <span class="label">Password:</span>
                <span class="value">{{ $password }}</span>
            </div>
        </div>

        <div class="warning" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;">
            <strong>⚠️ Peringatan Keamanan:</strong> Harap segera ubah password sementara Anda melalui menu <strong>Profil</strong> setelah pertama kali berhasil login demi menjaga keamanan akun Anda.
        </div>
        
        <a href="{{ config('app.url') }}/login" class="btn">Login ke Portal Magang</a>

        @if($pendaftaran->catatan_admin && !str_contains($pendaftaran->catatan_admin, '[SISTEM]'))
        <div class="info-box">
            <h3>Pesan dari Admin</h3>
            <p style="margin:0;">{{ $pendaftaran->catatan_admin }}</p>
        </div>
        @endif

        <p>Jika ada pertanyaan hubungi: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></p>
        <p>Salam,<br><strong>Tim MagangDPMPTSP</strong></p>
    </div>
    <div class="footer">
        © {{ date('Y') }} MagangDPMPTSP. Email ini dikirim otomatis, mohon tidak membalas.
    </div>
</div>
</body>
</html>
