<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengingat Pengisian Kuesioner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background: #f3f4f6; margin:0; padding:0; font-family: Arial, sans-serif;">
    <div style="max-width: 480px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px #dbeafe; padding: 32px 28px;">
        <div style="text-align: center;">
            <h2 style="color: #0e7490; font-size: 1.5rem; font-weight: bold; margin-bottom: 8px;">
                Tracer Study Politeknik Negeri Batam
            </h2>
        </div>
        <div style="margin-top: 24px;">
            <p style="color: #222; margin-bottom: 18px; text-align: left;">
                Halo,
            </p>
            <p style="color: #222; margin-bottom: 18px; text-align: left;">
                Kami mengingatkan Anda untuk segera mengisi kuesioner periode: <b>{{ $periode->periode_name }}</b>
            </p>
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ url('/login') }}" style="background: linear-gradient(90deg,#0ea5e9,#0e7490); color: #fff; padding: 12px 36px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 1rem; box-shadow: 0 2px 8px #bae6fd;">
                    Isi Kuesioner
                </a>
            </div>
            <p style="color: #64748b; font-size: 13px; margin-top: 32px; text-align: left;">
                Jika Anda sudah mengisi kuesioner, abaikan email ini.<br>
                Terima kasih atas partisipasi Anda.<br>
                <span style="font-weight: bold; color: #0e7490;">Tracer Study Politeknik Negeri Batam</span>
            </p>
        </div>
    </div>
</body>
</html>
