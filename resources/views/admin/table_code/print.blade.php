<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            width: 33%;
            padding: 15px;
            vertical-align: top;
            text-align: center;
        }
        .qr-container {
            border: 1px solid #333;
            border-radius: 8px;
            padding: 10px;
            display: inline-block;
            page-break-inside: avoid;
        }
        .qr-code {
            margin-bottom: 10px;
        }
        .qr-container strong {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .qr-container div:last-child {
            font-size: 13px;
            color: #555;
        }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>

    <table>
        @forelse($qrs->chunk(3) as $chunk)
            <tr>
                @foreach($chunk as $qr)
                    <td>
                        <div class="qr-container">
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $qr->base64 }}" width="150" height="150">
                            </div>
                            <strong>{{ $qr->code }}</strong>
                            <div>Meja {{ $qr->number ?? '-' }}</div>
                        </div>
                    </td>
                @endforeach
                @for($i = $chunk->count(); $i < 3; $i++)
                    <td></td>
                @endfor
            </tr>
        @empty
            <tr><td colspan="3">Tidak ada QR dengan status 0</td></tr>
        @endforelse
    </table>
</body>
</html>
