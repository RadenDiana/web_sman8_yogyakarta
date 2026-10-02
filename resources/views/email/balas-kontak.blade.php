<!DOCTYPE html>
<html lang="id">
<body style="font-family: Arial, sans-serif; background: #f1f5f9; padding: 24px;">
    <div style="max-width: 560px; margin: auto; background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="background: #2563eb; color: #fff; padding: 20px 24px;">
            <h2 style="margin: 0; font-size: 18px;">SMA Negeri 8 Yogyakarta</h2>
        </div>
        <div style="padding: 24px;">
            <p>Halo <strong>{{ $kontak->nama }}</strong>,</p>
            <p>Terima kasih telah menghubungi kami. Berikut balasan untuk pesanmu dengan subjek
               <strong>"{{ $kontak->subjek }}"</strong>:</p>

            <div style="background: #f1f5f9; border-radius: 10px; padding: 16px; margin: 16px 0; white-space: pre-wrap;">
                {{ $balasan }}
            </div>

            <p style="font-size: 12px; color: #64748b;">
                Pesan aslimu ({{ $kontak->created_at->translatedFormat('d M Y') }}):<br>
                "{{ $kontak->pesan }}"
            </p>

            <p style="margin-bottom: 0;">Salam,<br><strong>Admin SMA Negeri 8 Yogyakarta</strong></p>
        </div>
    </div>
</body>
</html>