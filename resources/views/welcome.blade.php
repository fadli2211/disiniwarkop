<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>UPTD PPA Kota Bogor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom vertical divider style */
        .divider-vertical {
            width: 2px;
            height: 28px;
            background-color: #3B82F6;
            /* Tailwind blue-500 */
            margin: 0 1rem;
        }
    </style>
</head>

<body class="bg-white font-sans text-gray-800">
    <header class="border-b border-gray-200">
        <div class="mx-auto flex items-center justify-between p-4">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('img/logo.png') }}"
                    alt="UPTD PPA Kota Bogor official logo with blue and pink abstract figures representing women and children, modern style"
                    class="w-16 h-16 object-contain"
                    onerror="this.onerror=null;this.src='https://placehold.co/72x72?text=No+Image'">
                <div class="text-xs space-y-1">
                    <div><span class="font-semibold">Telpon</span>: <span class="">(021) 31901446</span></div>
                    <div><span class="font-semibold">Email</span>: <span class="break-all">humas@kemenpppa.go.id</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}"
                    class="px-5 py-2 rounded-full border border-blue-400 bg-blue-100 text-blue-600 font-medium hover:bg-blue-200 transition">Masuk</a>
                <div class="divider-vertical" aria-hidden="true"></div>
                <a href="{{ route('register') }}"
                    class="px-5 py-2 rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">Daftar</a>
            </div>
        </div>
    </header>

    <section aria-label="Main call to action banner"
        class="bg-blue-600 text-white mx-auto px-4 py-3 text-center font-semibold text-lg md:text-xl">
        Sampaikan laporan anda terkait perlindungan perempuan dan anak
    </section>

    <main class="mx-auto py-8">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('img/logo.png') }}"
                alt="UPTD PPA Kota Bogor official logo in blue and pink with supporting graphical elements representing women and children"
                class="w-36 h-36 object-contain"
                onerror="this.onerror=null;this.src='https://placehold.co/144x144?text=No+Image'">
        </div>

        <div class="overflow-hidden rounded-lg shadow-lg">
            <img src="{{ asset('img/bg.png') }}" class="w-full object-cover"
                onerror="this.onerror=null;this.src='https://placehold.co/1200x600?text=No+Image'">
        </div>
    </main>
</body>

</html>
