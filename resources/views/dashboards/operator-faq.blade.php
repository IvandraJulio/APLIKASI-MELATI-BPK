@extends('layouts.app')

@section('title', 'Kelola FAQ - Operator Biro TI BPK')

@section('content')
<div class="h-[calc(100vh-8.5rem)] flex flex-col justify-between animate-in fade-in duration-300" x-data="operatorFaqPage()">
    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full items-stretch">
        
        <!-- LEFT PANEL: Article List (Col 5) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-[#e2e6ea] shadow-xs flex flex-col h-full overflow-hidden">
            <!-- Search & Filter Header -->
            <div class="p-4 border-b border-[#e2e6ea] space-y-3 shrink-0">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-sm font-bold text-gray-800 font-display">Daftar Artikel FAQ</h3>
                    <div class="flex items-center gap-2">
                        <a href="/api/operator/dataset/export" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer no-underline">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Dataset AI</span>
                        </a>
                        <button type="button" @click="openCreateModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#b26d27] hover:bg-[#9b5a1b] text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>
                
                <!-- Search Input -->
                <div class="relative">
                    <input type="text" x-model="search" placeholder="Cari judul, konten, subkategori..." class="w-full bg-slate-50 border border-slate-200 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-xl pl-9.5 pr-4 py-2 text-xs outline-none transition-all placeholder:text-gray-400 font-medium">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                </div>

                <!-- Category Filter -->
                <div class="mb-4" x-data="{ openFilterDropdown: false }">
                    <label class="block text-[8px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider font-mono">Filter Kategori</label>
                    <div class="relative" @click.away="openFilterDropdown = false">
                        <button @click="openFilterDropdown = !openFilterDropdown" type="button" 
                                class="w-full flex items-center justify-between bg-white border border-[#b26d27] text-gray-800 rounded-xl px-4 py-2.5 text-xs outline-none transition-all font-bold cursor-pointer">
                            <span class="truncate pr-2" x-text="categoryFilter === 'All' ? 'Semua Kategori' : categoryFilter"></span>
                            <svg class="w-4 h-4 text-gray-400 pointer-events-none transition-transform shrink-0" :class="openFilterDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="openFilterDropdown" 
                             x-transition.origin.top.left 
                             class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-slate-100"
                             style="display: none;">
                            <button @click="categoryFilter = 'All'; openFilterDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="categoryFilter === 'All' ? 'text-[#b26d27]' : ''">Semua Kategori</button>
                            <template x-for="cat in Object.keys(categoriesData)" :key="cat">
                                <button @click="categoryFilter = cat; openFilterDropdown = false;" type="button" class="w-full text-left p-3 text-xs text-gray-900 hover:bg-slate-50 font-bold transition-colors bg-white cursor-pointer" :class="categoryFilter === cat ? 'text-[#b26d27]' : ''" x-text="cat"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scrollable Article List -->
            <div class="flex-1 overflow-y-auto divide-y divide-[#e2e6ea]">
                <template x-for="art in filteredArticles()" :key="art.id">
                    <div @click="selectArticle(art.id)" 
                         class="p-4 text-left cursor-pointer transition-all"
                         :class="selectedId === art.id ? 'bg-[#fcf4ec] border-l-4 border-l-[#b26d27]' : 'hover:bg-slate-50/50'">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="font-bold text-[#b26d27] text-[10px] uppercase bg-orange-50 px-2 py-0.5 rounded" x-text="art.category"></span>
                            <span class="text-[10px] text-gray-400 font-mono" x-text="'Likes: ' + art.likes"></span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 truncate" x-text="art.title"></h4>
                        <template x-if="art.subcategory">
                            <div class="flex flex-wrap gap-1 mt-1">
                                <span class="text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded" x-text="art.subcategory"></span>
                                <template x-if="art.service">
                                    <span class="text-[10px] text-blue-750 font-semibold bg-blue-50 px-1.5 py-0.5 rounded" x-text="art.service"></span>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
                <div x-show="filteredArticles().length === 0" class="p-8 text-center text-gray-400 text-xs">
                    Tidak ada artikel ditemukan.
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Article Details & Actions (Col 7) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-[#e2e6ea] shadow-xs flex flex-col h-full overflow-hidden">
            <template x-if="getSelectedArticle()">
                <div class="flex flex-col h-full overflow-hidden">
                    <!-- Details Header -->
                    <div class="p-5 border-b border-[#e2e6ea] shrink-0 bg-slate-50/40 flex justify-between items-start gap-4">
                        <div>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md text-[10px] font-bold" x-text="getSelectedArticle().category"></span>
                            <template x-if="getSelectedArticle().subcategory">
                                <span class="ml-1.5 px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-md text-[10px] font-bold" x-text="getSelectedArticle().subcategory"></span>
                            </template>
                            <template x-if="getSelectedArticle().service">
                                <span class="ml-1.5 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-bold" x-text="getSelectedArticle().service"></span>
                            </template>
                            <h2 class="text-base font-bold text-gray-800 font-display mt-2" x-text="getSelectedArticle().title"></h2>
                            <p class="text-[10px] text-gray-400 mt-1" x-text="'Likes: ' + getSelectedArticle().likes"></p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2 shrink-0">
                            <button type="button" @click="openEditModal(getSelectedArticle())" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors cursor-pointer" title="Edit Artikel">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </button>
                            <button type="button" @click="confirmDelete(getSelectedArticle().id)" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer" title="Hapus Artikel">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Scrollable Body -->
                    <div class="flex-1 overflow-y-auto p-5 space-y-6">
                        <!-- HTML Content Preview -->
                        <div class="space-y-2 bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Preview Konten</h3>
                            <div class="prose max-w-none text-xs text-gray-700 leading-relaxed font-medium mt-3" x-html="getSelectedArticle().content"></div>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="!getSelectedArticle()">
                <div class="flex flex-col items-center justify-center h-full text-center p-8 text-gray-400 space-y-3">
                    <div class="p-4 bg-slate-50 rounded-full text-slate-300">
                        <i data-lucide="help-circle" class="w-10 h-10"></i>
                    </div>
                    <p class="text-xs font-bold">Pilih artikel dari daftar di samping untuk melihat preview dan mengelola.</p>
                </div>
            </template>
        </div>
    </div>

    <!-- CREATE/EDIT MODAL OVERLAY -->
    <div x-show="formOpen" 
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4" 
         style="display: none;" 
         x-transition>
         
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col z-10" @click.stop>
            <!-- Modal Header -->
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800 font-display" x-text="editingId ? 'Edit Artikel FAQ' : 'Tambah Artikel FAQ Baru'"></h3>
                <button @click="formOpen = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Form Body -->
            <form @submit.prevent="submitForm()" class="p-6 space-y-4 overflow-y-auto max-h-[75vh]">
                <!-- Title -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-700 mb-1">Judul Artikel</label>
                    <input type="text" x-model="form.title" required placeholder="Contoh: Cara Reset Password Email" class="w-full bg-slate-50 border border-slate-200 focus:border-[#b26d27] text-gray-800 rounded-xl px-4 py-2.5 text-xs outline-none transition-all font-semibold">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-700 mb-1">Kategori (Level 1)</label>
                    <select x-model="form.category" @change="form.subcategory = ''" required class="w-full bg-slate-50 border border-slate-200 focus:border-[#b26d27] text-gray-800 rounded-xl px-4 py-2.5 text-xs outline-none transition-all font-semibold">
                        <option value="">- Pilih Kategori -</option>
                        <template x-for="cat in Object.keys(categoriesData)" :key="cat">
                            <option :value="cat" x-text="cat"></option>
                        </template>
                    </select>
                </div>

                <!-- Subcategory -->
                <div x-show="form.category && getSubcategoriesForForm().length > 0">
                    <label class="block text-[10px] font-bold text-gray-700 mb-1">Subkategori (Level 2)</label>
                    <select x-model="form.subcategory" @change="form.service = ''" class="w-full bg-slate-50 border border-slate-200 focus:border-[#b26d27] text-gray-800 rounded-xl px-4 py-2.5 text-xs outline-none transition-all font-semibold">
                        <option value="">- Pilih Subkategori -</option>
                        <template x-for="sub in getSubcategoriesForForm()" :key="sub">
                            <option :value="sub" x-text="sub"></option>
                        </template>
                    </select>
                </div>

                <!-- Service (Level 3) -->
                <div x-show="form.category && categoriesData[form.category]?.type === '3-level' && form.subcategory && getServicesForForm().length > 0">
                    <label class="block text-[10px] font-bold text-gray-700 mb-1">Detail Layanan (Level 3)</label>
                    <select x-model="form.service" class="w-full bg-slate-50 border border-slate-200 focus:border-[#b26d27] text-gray-800 rounded-xl px-4 py-2.5 text-xs outline-none transition-all font-semibold">
                        <option value="">- Pilih Layanan -</option>
                        <template x-for="svc in getServicesForForm()" :key="svc">
                            <option :value="svc" x-text="svc"></option>
                        </template>
                    </select>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-700 mb-1">Konten Artikel (HTML diperbolehkan)</label>
                    <textarea x-model="form.content" required rows="6" placeholder="Masukkan konten solusi atau troubleshooting..." class="w-full bg-slate-50 border border-slate-200 focus:border-[#b26d27] text-gray-800 rounded-xl px-4 py-2.5 text-xs outline-none transition-all font-semibold"></textarea>
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="formOpen = false" class="flex-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="submit" :disabled="loadingSubmit" class="flex-1 bg-[#b26d27] hover:bg-[#9b5a1b] text-white py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer text-center disabled:opacity-50">
                        <span x-text="loadingSubmit ? 'Menyimpan...' : 'Simpan Artikel'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div x-show="deleteConfirmOpen" 
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-[60] flex items-center justify-center p-4" 
         style="display: none;" 
         x-transition>
         
        <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl p-6 text-center space-y-4" @click.stop>
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            </div>
            
            <h3 class="text-sm font-bold text-gray-900 font-display">Hapus Artikel FAQ</h3>
            <p class="text-xs text-gray-600 leading-relaxed font-medium">Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan.</p>
            
            <div class="flex gap-3 pt-2">
                <button type="button" @click="deleteConfirmOpen = false" class="flex-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer text-center">
                    Batal
                </button>
                <button type="button" @click="executeDelete()" :disabled="loadingSubmit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer text-center disabled:opacity-50">
                    Hapus
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function operatorFaqPage() {
        return {
            articles: @json($articles),
            selectedId: null,
            search: '',
            categoryFilter: 'All',
            formOpen: false,
            editingId: null,
            loadingSubmit: false,
            deleteConfirmOpen: false,
            deleteId: null,
            form: {
                title: '',
                category: '',
                subcategory: '',
                service: '',
                content: '',
            },
            categoriesData: {
                'Layanan Identitas': {
                    type: '2-level',
                    subs: ['Layanan Akun', 'Layanan TTE (Tanda Tangan Elektronik)', 'Layanan Segel Elektronik', 'Layanan Email', 'Layanan MFA (Multi-Factor Authentication)']
                },
                'Layanan Data': {
                    type: '2-level',
                    subs: [
                        'Perencanaan Data', 'Pengumpulan Data', 'Pengolahan Data', 'Penyimpanan Data', 
                        'Penyebarluasan Data', 'Analisis Data', 'Pengamanan Data', 'Pemusnahan Data',
                        'BIDICS Dashboard', 'BIDICS-SSA'
                    ]
                },
                'Layanan Aplikasi': {
                    type: '2-level',
                    subs: ['Pengembangan Aplikasi', 'Aplikasi Pemeriksaan', 'Aplikasi Kelembagaan', 'Aplikasi Pendukung', 'Aplikasi Kolaborasi', 'Layanan Survei']
                },
                'Layanan Teknologi': {
                    type: '3-level',
                    subs: {
                        'Layanan Intranet': [
                            'Pembuatan Local Area Network (LAN)', 
                            'Pengaturan konfigurasi LAN', 
                            'Penonaktifan LAN', 
                            'Penyediaan kabel LAN', 
                            'Pemasangan perangkat Wireless Fidelity (Wifi)', 
                            'Pengaturan konfigurasi Wifi', 
                            'Penonaktifan Wifi'
                        ],
                        'Layanan Internet': [
                            'Pemasangan perangkat koneksi internet', 
                            'Pengaturan konfigurasi perangkat koneksi internet', 
                            'Penonaktifan perangkat koneksi internet'
                        ],
                        'Layanan Virtual Private Network': [
                            'Pemasangan VPN', 
                            'Pengaturan konfigurasi VPN', 
                            'Penonaktifan VPN'
                        ],
                        'Layanan Hosting': [
                            'Pendaftaran hosting subdomain', 
                            'Pengaturan konfigurasi hosting subdomain', 
                            'Penonaktifan hosting subdomain'
                        ]
                    }
                },
                'Layanan Perangkat': {
                    type: '2-level',
                    subs: ['Standarisasi Perangkat Komputer', 'Pemeliharaan Perangkat', 'Peminjaman Perangkat', 'Penyediaan Barang Persediaan']
                },
                'Layanan Dukungan TI': {
                    type: '2-level',
                    subs: ['Pendampingan Personel TI']
                },
                'Layanan Informasi': {
                    type: '2-level',
                    subs: ['Knowledge Base Produk TI', 'Informasi Produk TI', 'Tugas dan Fungsi Biro TI']
                }
            },
            selectArticle(id) {
                this.selectedId = id;
            },
            getSelectedArticle() {
                return this.articles.find(a => a.id === this.selectedId);
            },
            filteredArticles() {
                let query = this.search.trim().toLowerCase();
                return this.articles.filter(a => {
                    // Match Category Filter
                    if (this.categoryFilter !== 'All' && a.category !== this.categoryFilter) {
                        return false;
                    }
                    // Match Search Query
                    if (query) {
                        const matchTitle = a.title.toLowerCase().includes(query);
                        const matchContent = a.content.toLowerCase().includes(query);
                        const matchSub = a.subcategory ? a.subcategory.toLowerCase().includes(query) : false;
                        const matchSvc = a.service ? a.service.toLowerCase().includes(query) : false;
                        if (!matchTitle && !matchContent && !matchSub && !matchSvc) {
                            return false;
                        }
                    }
                    return true;
                });
            },
            getSubcategoriesForForm() {
                if (!this.form.category) return [];
                const cat = this.categoriesData[this.form.category];
                if (!cat) return [];
                if (cat.type === '2-level') {
                    return cat.subs;
                } else {
                    return Object.keys(cat.subs);
                }
            },
            getServicesForForm() {
                if (!this.form.category || !this.form.subcategory) return [];
                const cat = this.categoriesData[this.form.category];
                if (!cat || cat.type !== '3-level') return [];
                return cat.subs[this.form.subcategory] || [];
            },
            openCreateModal() {
                this.editingId = null;
                this.form.title = '';
                this.form.category = '';
                this.form.subcategory = '';
                this.form.service = '';
                this.form.content = '';
                this.formOpen = true;
            },
            openEditModal(article) {
                this.editingId = article.id;
                this.form.title = article.title;
                this.form.category = article.category;
                this.form.subcategory = article.subcategory || '';
                this.form.service = article.service || '';
                this.form.content = article.content;
                this.formOpen = true;
            },
            confirmDelete(id) {
                this.deleteId = id;
                this.deleteConfirmOpen = true;
            },
            async executeDelete() {
                this.loadingSubmit = true;
                try {
                    const res = await fetch(`/api/operator/faq/${this.deleteId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    const result = await res.json();
                    
                    if (result.success) {
                        // Remove from local array
                        this.articles = this.articles.filter(a => a.id !== this.deleteId);
                        if (this.selectedId === this.deleteId) {
                            this.selectedId = null;
                        }
                        this.deleteConfirmOpen = false;
                        alert(result.message);
                    } else {
                        alert('Gagal menghapus artikel.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan saat menghapus artikel.');
                } finally {
                    this.loadingSubmit = false;
                }
            },
            async submitForm() {
                this.loadingSubmit = true;
                const url = this.editingId ? `/api/operator/faq/${this.editingId}` : '/api/operator/faq';
                
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.form)
                    });
                    const result = await res.json();
                    
                    if (result.success) {
                        if (this.editingId) {
                            // Update local array
                            const idx = this.articles.findIndex(a => a.id === this.editingId);
                            if (idx !== -1) {
                                this.articles[idx] = result.article;
                            }
                        } else {
                            // Push new item to local list
                            this.articles.unshift(result.article);
                            this.selectedId = result.article.id;
                        }
                        this.formOpen = false;
                        alert(result.message);
                    } else {
                        alert('Gagal menyimpan artikel.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan saat menyimpan artikel.');
                } finally {
                    this.loadingSubmit = false;
                }
            }
        }
    }
</script>
@endpush
