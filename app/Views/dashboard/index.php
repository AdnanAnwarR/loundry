<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Selamat Datang - E-library</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>

<body class="bg-white text-slate-800">

  <!-- Header / Nav -->
  <nav class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
    <div class="flex items-center gap-2">
      <div class="bg-indigo-600 p-2 rounded-xl text-white">
        <i data-lucide="library" class="w-6 h-6"></i>
      </div>
      <span class="text-2xl font-bold tracking-tight text-slate-900">E-Lib<span class="text-indigo-600">rary</span></span>
    </div>
    <div class="flex gap-4">
      <a href="<?= base_url('login') ?>" class="px-6 py-2.5 font-semibold text-slate-600 hover:text-indigo-600 transition-colors">Masuk</a>
      <a href="<?= base_url('register') ?>" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">Daftar</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <main class="max-w-7xl mx-auto px-6 py-16 md:py-24">
    <div class="grid lg:grid-cols-2 gap-16 items-center">
      <div class="space-y-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-sm font-bold border border-indigo-100">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
          </span>
          Tersedia 12.000+ Koleksi Digital
        </div>

        <h1 class="text-6xl font-extrabold text-slate-900 leading-[1.1]">
          Eksplorasi Dunia Lewat <span class="text-indigo-600">Literasi Digital</span>
        </h1>

        <p class="text-xl text-slate-500 leading-relaxed max-w-xl">
          Platform perpustakaan masa kini yang memudahkan kamu mencari referensi, meminjam ebook, dan mengelola koleksi bacaan dalam satu genggaman.
        </p>

        <div class="flex flex-wrap gap-4 pt-4">
          <a href="<?= base_url('login') ?>" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 flex items-center gap-2">
            Mulai Jelajahi <i data-lucide="arrow-right" class="w-5 h-5"></i>
          </a>
          <a href="#fitur" class="px-8 py-4 border-2 border-slate-200 text-slate-700 rounded-2xl font-bold hover:bg-slate-50 transition-all">
            Pelajari Fitur
          </a>
        </div>

        <div class="pt-8 border-t border-slate-100 flex gap-12">
          <div>
            <p class="text-3xl font-black text-slate-900">5k+</p>
            <p class="text-slate-400 font-medium">Member Aktif</p>
          </div>
          <div>
            <p class="text-3xl font-black text-slate-900">200+</p>
            <p class="text-slate-400 font-medium">Instansi Mitra</p>
          </div>
        </div>
      </div>

      <div class="relative hidden lg:block">
        <div class="bg-indigo-600 aspect-square rounded-[4rem] rotate-3 flex items-center justify-center shadow-2xl shadow-indigo-200">
          <i data-lucide="book-open" class="w-64 h-64 text-white opacity-20"></i>
        </div>
        <div class="absolute -bottom-10 -left-10 bg-white p-8 rounded-3xl shadow-2xl border border-slate-100 space-y-4 max-w-[280px]">
          <div class="flex items-center gap-4">
            <div class="bg-emerald-100 p-3 rounded-2xl text-emerald-600">
              <i data-lucide="shield-check"></i>
            </div>
            <div>
              <p class="font-bold text-slate-900">Aman & Resmi</p>
              <p class="text-xs text-slate-500">Semua buku memiliki lisensi.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>