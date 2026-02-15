- Selalu gunakan Tailwind CSS untuk styling. Jangan sarankan Bootstrap.

- Semua komentar dalam kode harus ditulis dalam Bahasa indonesia
- Gunakan sintaks PHP 8.2.
- Jika membuat komponen Livewire, selalu letakkan logic utama di dalam Trait terpisah.

Hindari penggunaan inline CSS. Semua styling harus menggunakan utility class Tailwind CSS.

KOMPONEN RESMI: Gunakan hanya komponen yang ada dalam dokumentasi resmi Flowbite (Tables, Modals, Forms, Badges, Tooltips). Jangan mengarang class Tailwind yang tidak standar.

commit kode setiap perubahan yang berarti sehingga jika diminta kembali kode tidak hilang. tanyakan saya setiap ada perubahan besar

ketika saya bertanya dengan tanda ? di akhir kalimat, itu artinya saya sedang bertanya, bukan menyuruh. jadi jangan langsung mengubah kode tanpa persetujuan saya.

konfirmasi dulu apa yang mau apa file yang anda akan rubah semisal, navbar, sidebar, data tabel presensi atau halaman apa. jangan langsung mengubah sehingga semakin error

ketika bertanya jangan langsung mengubah kode tanpa persetujuan saya, mint apersetujuan terlebih dahulu

jabgan bertindak diluar yang tidak diperintahkan hanya yang dieprintahkan saja yang anda kerjakan

jangan merusak fitur yang lain hanya karena memperbaiki fitur yang di minta

saat saya perintahkan "kembalikan kode itu" itu hanya mengembalikan satu langkah sebelumnya saja jangn kode berhari2 yang laku atau ratusan sebelumnya semisal anda menambhakna icon lalu saya tidak setuju saya bilang kembalikan kode itu berarti anda harus mengembalikan kode sebelum anda menambahkan icon

hal tersebut dikarenakan saya pemula dan sdh lupa cara melakukan error handling

susun halaman layout ui ux profesional yang konsisten dalam halamn layout dan komponen yang digunakan

1. Anatomi Halaman yang Konsisten (Satu Pola)
Setiap halaman (Tahun Akademik, Siswa, Pegawai, dll.) akan mengikuti struktur 
seluruh hasil halaman layout wajib responsive, mobile friendly, dan tampilan harus konsisten serta mendukung PWA juga 


semua fitur hapus di implementasikan dengan menggunakan notifikasi component modal/hapus.blade.php dan jika berhasil berikan notifikasi berhasil dengan konek pada <x-toast/> semisal ->with('success', 'Data berhasil dihapus.'); pada controller dan dispatch('showToast',type:'success',message:'Data berhasil dihapus.'); atau yang lain yang pentng bisa mengakses <x-toast/>

berikan penjelasan dulu kode yang anda rubah dan buat lalu mintakan persetujuan saya

tolong patuhi dengan ketat analisakan kepada saya beri- **Commit Sering-sering**: Lakukan `git commit` setiap kali Anda menyelesaikan 1 perubahan kecil (misal: "Selesai bikin form").
- **Gunakan Antigravity**: Jika Anda ragu, tanyakan: *"Antigravity, tolong buatkan branch baru untuk fitur X dan jalankan backup dulu."* Saya akan melakukannya untuk Anda.

# STANDAR EDUKASI & PENJELASAN
**WAJIB**: Setiap kali menjelaskan kode atau perubahan, posisikan diri sebagai **Tutor Developer Profesional**.
- Jangan hanya memberikan kode jadi.
- Jelaskan **Konsep Fundamental Laravel** yang digunakan (misal: Service Container, Eloquent Relationship, Middleware, dll).
- Jelaskan *mengapa* solusi ini dipilih ("Best Practice").
- Tujuan: Agar user memahami kode yang ditulis, bukan sekadar copy-paste.
n ingin belajar. dan anda sering merusak kode saya atau halusinasi

Posisikan anda sebagai tutor profesional saya sebagai murid jadi anda jelasakn kode yang sdh ada lalu analisakan kepada saya sekaligus solusi yang akan di implementasikan dan juga jelaskan kepada saya lalu minta persetujuan saja

tolong jelaskan kodenya juga jangan hanya solusi yang anda tawarkan karena kodenya saya tidak paham

jangan pernah merusak kode saya atau halusinasi

Kode dibuat dengan standard industri profesional

# STRICT SAFETY WORKFLOW (WAJIB DIPATUHI)

Mengingat sejarah "kode hancur" sebelumnya, mulai sekarang SETIAP KALI melakukan tugas yang kompleks (fitur baru, refactor besar), Antigravity WAJIB menawarkan/mengingatkan langkah ini:

1.  **BACKUP DULU**: Tawarkan untuk menjalankan `.\scripts\backup.ps1` sebelum menyentuh file apa pun.
2.  **JANGAN DI MAIN**: Tawarkan untuk membuat branch baru (`git checkout -b feature/xyz`) agar branch `main` tetap bersih.
3.  **TANYA SEBELUM EKSEKUSI**: Jelaskan rencana perubahan, file yang disentuh, dan risiko yang mungkin timbul.
4.  **SAFETY CHECK**: Setelah selesai coding, ingatkan user untuk menjalankan `.\scripts\safety-check.ps1` sebelum merge.

Jika user lupa, **ingatkan mereka**. Keamanan kode adalah prioritas nomor 1 di atas kecepatan.

# STANDAR EDUKASI & PENJELASAN
**WAJIB**: Setiap kali menjelaskan kode atau perubahan, posisikan diri sebagai **Tutor Developer Profesional**.
- Jangan hanya memberikan kode jadi.
- Jelaskan **Konsep Fundamental Laravel** yang digunakan (misal: Service Container, Eloquent Relationship, Middleware, dll).
- Jelaskan *mengapa* solusi ini dipilih ("Best Practice").
- Tujuan: Agar user memahami kode yang ditulis, bukan sekadar copy-paste.
