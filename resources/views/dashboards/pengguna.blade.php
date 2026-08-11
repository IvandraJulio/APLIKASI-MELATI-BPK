@extends('layouts.app')

@section('title', 'Beranda - Portal Layanan TI BPK')

@section('content')
<div class="h-[calc(100vh-8.5rem)] flex flex-col justify-between" x-data="berandaPage()">
    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full items-stretch">
        
        <!-- LEFT COLUMN: Chatbot (virtual assistant) -->
        <div :class="showForm ? 'lg:col-span-8' : 'lg:col-span-12 max-w-4xl mx-auto w-full'" 
             class="relative bg-white border border-[#e2e6ea] rounded-2xl shadow-sm flex flex-col h-full overflow-hidden transition-all duration-300"
             @dragover.prevent="isDragging = true"
             @dragenter.prevent="isDragging = true">
             
             <!-- Drag and Drop Overlay -->
             <div x-show="isDragging" 
                  @dragleave.prevent="isDragging = false"
                  @drop.prevent="handleDrop($event)"
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 scale-95"
                  x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="transition ease-in duration-150"
                  x-transition:leave-start="opacity-100 scale-100"
                  x-transition:leave-end="opacity-0 scale-95"
                  class="absolute inset-0 bg-[#b26d27]/90 backdrop-blur-xs z-50 flex flex-col items-center justify-center text-white border-2 border-dashed border-[#F0DCC0] rounded-2xl m-2"
                  style="display: none;">
                  <div class="p-6 rounded-full bg-white/10 mb-4 animate-bounce pointer-events-none">
                      <i data-lucide="upload-cloud" class="w-12 h-12 text-white"></i>
                  </div>
                  <h3 class="text-lg font-bold pointer-events-none">Lepaskan Gambar di Sini</h3>
                  <p class="text-xs text-[#F0DCC0] mt-1 font-medium pointer-events-none">Unggah screenshot atau foto kendala TI Anda</p>
             </div>
             
             <!-- Chat Header -->
             <div class="bg-[#F0DCC0] text-[#78430e] px-5 py-4 flex items-center gap-3 shrink-0 border-b border-orange-100">
                 <div class="w-9 h-9 rounded-xl bg-[#b26d27] flex items-center justify-center text-white shadow-xs">
                     <i data-lucide="bot" class="w-5.5 h-5.5"></i>
                 </div>
                 <div>
                     <h3 class="text-sm font-bold font-display leading-tight flex items-center gap-1.5 text-gray-800">
                         <span>Asisten Virtual Layanan TI</span>
                         <span class="text-[9px] px-1.5 py-0.5 rounded-full font-mono font-bold text-white bg-emerald-600">
                             GEMINI AI
                         </span>
                     </h3>
                     <p class="text-[10px] text-gray-500 font-medium">Bantu isi form otomatis dengan deskripsi masalah Anda</p>
                 </div>
             </div>

             <!-- Chat Messages -->
             <div class="flex-1 overflow-y-auto p-5 space-y-4 bg-[#fcfbfa]" id="chat-box">
                 <template x-for="msg in chatMessages" :key="msg.id">
                     <div class="flex gap-3 max-w-[85%]" :class="msg.sender === 'user' ? 'ml-auto flex-row-reverse' : 'mr-auto'">
                         <!-- Avatar -->
                         <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-xs"
                              :class="msg.sender === 'bot' ? 'bg-[#fcf4ec] text-[#b26d27] border border-[#f7e3ce]' : 'bg-[#F0DCC0] text-[#78430e]'">
                             <template x-if="msg.sender === 'bot'">
                                 <i data-lucide="bot" class="w-4 h-4"></i>
                             </template>
                             <template x-if="msg.sender === 'user'">
                                 <i data-lucide="user" class="w-4 h-4"></i>
                             </template>
                         </div>

                         <!-- Bubble -->
                         <div class="space-y-2">
                             <div class="p-3.5 rounded-2xl text-xs leading-relaxed shadow-xs border"
                                  :class="msg.sender === 'bot' ? 'bg-[#F3EDE2] text-gray-700 rounded-tl-none border-gray-200/60' : 'bg-[#F0DCC0] text-gray-800 border-orange-200/50 rounded-tr-none'"
                                  x-html="formatMarkdown(msg.text)"></div>

                             <!-- Related FAQ Recommendation Button for Bot Response -->
                             <template x-if="shouldShowFaqButton(msg)">
                                 <div class="mt-1 pb-1">
                                     <a :href="getFaqLink(msg.recommendation)" class="inline-flex items-center gap-1.5 px-4.5 py-2.5 bg-[#b26d27] hover:bg-[#9b5a1b] text-white text-[11px] font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                                         <span x-text="'Lihat Panduan ' + (msg.recommendation.sub || 'FAQ')"></span>
                                     </a>
                                 </div>
                             </template>

                             <!-- Image Sent by User -->
                             <template x-if="msg.image">
                                 <div class="rounded-xl overflow-hidden border border-slate-200 max-w-xs shadow-xs mt-1 cursor-pointer" @click="openImageLightbox(msg.image.dataUrl)">
                                     <img :src="msg.image.dataUrl" class="w-full max-h-48 object-cover hover:scale-102 transition-transform duration-200">
                                 </div>
                             </template>

                             <!-- Confirmation Prompt -->
                             <template x-if="msg.sender === 'bot' && msg.recommendation && msg.showConfirmation">
                                 <div class="bg-[#FCF7ED] border border-[#f5dcb3] rounded-xl p-3.5 shadow-xs space-y-2.5 max-w-full">
                                     <p class="text-[11px] font-bold text-[#78430e] leading-snug">
                                         Apakah Anda ingin membuat tiket bantuan untuk rekomendasi ini atau melanjutkan chat?
                                     </p>
                                     <div class="flex gap-2">
                                         <button type="button" @click="msg.showConfirmation = false; msg.confirmed = true; applyRecommendation(msg.recommendation);" class="flex-1 bg-[#b26d27] hover:bg-[#9b5a1b] text-white py-1.5 px-2.5 rounded-lg text-[10px] font-bold transition-all cursor-pointer text-center">
                                             Ya, Buat Tiket
                                         </button>
                                         <button type="button" @click="msg.showConfirmation = false; msg.confirmed = false;" class="flex-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 py-1.5 px-2.5 rounded-lg text-[10px] font-bold transition-all cursor-pointer text-center">
                                             Tidak, Lanjutkan
                                         </button>
                                     </div>
                                 </div>
                             </template>

                             <!-- Recommendation Card -->
                             <template x-if="msg.sender === 'bot' && msg.recommendation && !msg.showConfirmation && msg.confirmed">
                                 <div class="bg-white border border-[#f7e3ce] rounded-xl p-3.5 shadow-sm space-y-3 max-w-full">
                                     <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-2">
                                         <span class="text-[10px] font-bold text-[#b26d27] uppercase tracking-wider flex items-center gap-1">
                                             <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Rekomendasi Layanan
                                         </span>
                                         <span class="text-[9px] font-bold px-1.5 py-0.5 rounded"
                                               :class="msg.recommendation.confidence === 'Tinggi' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100'"
                                               x-text="msg.recommendation.confidence"></span>
                                     </div>
                                     <div class="text-[11px] space-y-1 text-gray-700 font-medium leading-relaxed">
                                         <div>Kategori: <strong x-text="msg.recommendation.category"></strong></div>
                                         <div>Sub-Layanan: <strong x-text="msg.recommendation.sub"></strong></div>
                                         <div>Layanan: <strong x-text="msg.recommendation.service"></strong></div>
                                     </div>
                                     <button @click="applyRecommendation(msg.recommendation)" class="w-full bg-[#b26d27] hover:bg-[#9b5a1b] text-white py-1.5 px-3 rounded-lg text-[10px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer">
                                         <span>Gunakan Rekomendasi & Buka Form</span>
                                         <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                     </button>
                                 </div>
                             </template>
                         </div>
                     </div>
                 </template>
                 
                 <!-- Typing Indicator -->
                 <div x-show="chatLoading" class="flex gap-3 max-w-[85%] mr-auto" style="display: none;">
                     <div class="w-8 h-8 rounded-full bg-[#fcf4ec] text-[#b26d27] flex items-center justify-center border border-[#f7e3ce]">
                         <i data-lucide="bot" class="w-4 h-4"></i>
                     </div>
                     <div class="bg-white text-gray-500 rounded-2xl rounded-tl-none border border-gray-100 p-3 text-xs flex items-center gap-1 shadow-xs">
                         <span class="animate-bounce">●</span>
                         <span class="animate-bounce" style="animation-delay: 0.2s">●</span>
                         <span class="animate-bounce" style="animation-delay: 0.4s">●</span>
                     </div>
                 </div>
             </div>

             <!-- Attachment Preview Panel -->
             <div x-show="attachedImage" class="p-2.5 bg-slate-50/60 border-t border-[#e2e6ea] flex items-center gap-3 shrink-0" style="display: none;">
                 <div class="relative w-14 h-14 rounded-xl overflow-hidden border border-slate-200 shadow-xs bg-white flex items-center justify-center">
                     <img :src="attachedImage ? attachedImage.dataUrl : ''" class="w-full h-full object-cover">
                     <button type="button" @click="removeAttachedImage()" class="absolute top-0.5 right-0.5 bg-black/60 text-white rounded-full p-0.5 hover:bg-black/80 transition-all cursor-pointer flex items-center justify-center">
                         <i data-lucide="x" class="w-2.5 h-2.5"></i>
                     </button>
                 </div>
                 <div class="text-[10px] text-gray-500 font-semibold min-w-0">
                     <p class="text-gray-800 font-bold truncate max-w-[180px] sm:max-w-[240px]" x-text="attachedImage ? attachedImage.name : ''"></p>
                     <p x-text="attachedImage ? formatBytes(attachedImage.size) : ''"></p>
                 </div>
             </div>

             <!-- Chat Input Form -->
             <form @submit.prevent="sendChatMessage()" class="p-3.5 bg-white border-t border-[#e2e6ea] flex gap-2 shrink-0 items-center">
                 <!-- Attachment Button -->
                 <button type="button" @click="document.getElementById('chat-file-input').click()" class="text-gray-400 hover:text-[#b26d27] p-2 rounded-full hover:bg-slate-50 transition-all cursor-pointer flex items-center justify-center shrink-0">
                     <i data-lucide="paperclip" class="w-5 h-5"></i>
                 </button>
                 <input type="file" id="chat-file-input" class="hidden" accept="image/*" @change="handleImageUpload($event)">

                 <input type="text" x-model="chatInput" @paste="handleImagePaste($event)" placeholder="Tulis kendala Anda atau paste screenshot..." class="flex-1 bg-[#f5f6f8] border border-slate-100 focus:border-[#b26d27] focus:ring-1 focus:ring-[#b26d27]/30 text-gray-800 rounded-full px-5 py-3 text-xs outline-none transition-all placeholder:text-gray-400 font-semibold">
                 <button type="submit" class="bg-[#b26d27] hover:bg-[#9b5a1b] text-white w-10 h-10 rounded-full flex items-center justify-center shrink-0 cursor-pointer shadow-sm hover:shadow-md transition-all">
                     <i data-lucide="send" class="w-4 h-4"></i>
                 </button>
             </form>
        </div>

        <!-- RIGHT COLUMN: Form Permintaan Layanan -->
        <div x-show="showForm" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="lg:col-span-4 bg-white p-5 md:p-6 rounded-2xl border border-[#e2e6ea] shadow-xs flex flex-col justify-between h-full relative"
             style="display: none;">
             
             <div class="flex flex-col h-full justify-between">
                 <div class="space-y-4">
                     <div class="flex justify-between items-start">
                          <div>
                              <h2 class="text-sm font-bold text-gray-800 font-display">Form Permintaan Layanan</h2>
                          </div>
                         <button @click="showForm = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                             <i data-lucide="x" class="w-4 h-4"></i>
                         </button>
                     </div>

                     <div x-show="successMessage" class="p-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-semibold flex items-center gap-3">
                         <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                         <div>
                             <p class="font-bold">Laporan Terkirim!</p>
                             <p class="text-[10px] text-emerald-700 mt-0.5" x-text="'Tiket ' + successMessage + ' berhasil dibuat.'"></p>
                             <a :href="'/dashboard/detail?id=' + successMessage" class="text-[#b26d27] hover:underline font-bold mt-1 inline-block">Lihat Detail Tiket →</a>
                         </div>
                     </div>

                     <form @submit.prevent="submitForm()" class="space-y-3.5">
                          <!-- Kategori Select -->
                          <div class="relative" x-data="{ open: false, hoveredCat: null }" @click.away="open = false; hoveredCat = null">
                              <label class="block text-[10px] font-bold text-gray-800 mb-1">Kategori (level 1)</label>
                              <button type="button" @click="open = !open" 
                                      class="w-full flex items-center justify-between bg-[#EFE9DF] border-2 border-[#3D3025] text-[#5A4535] rounded-xl px-4 py-2.5 text-xs font-bold transition-all outline-none">
                                  <span x-text="kategori ? formatDisplay(kategori) : '-Pilih Kategori-'"></span>
                                  <i data-lucide="chevron-down" class="w-4 h-4 text-[#8C7662] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                              </button>
                              <div x-show="open" 
                                   x-transition:enter="transition ease-out duration-100"
                                   x-transition:enter-start="opacity-0 scale-95"
                                   x-transition:enter-end="opacity-100 scale-100"
                                   x-transition:leave="transition ease-in duration-75"
                                   x-transition:leave-start="opacity-100 scale-100"
                                   x-transition:leave-end="opacity-0 scale-95"
                                   class="absolute z-50 left-0 right-0 mt-1 bg-[#EFE9DF] border-2 border-[#3D3025] rounded-xl shadow-lg overflow-hidden flex flex-col"
                                   style="display: none;">
                                   <template x-for="cat in catalog">
                                       <button type="button" 
                                               @mouseenter="hoveredCat = cat.category"
                                               @mouseleave="hoveredCat = null"
                                               @click="kategori = cat.category; onCategoryChange(); open = false; hoveredCat = null;"
                                               class="w-full text-left px-4 py-2.5 text-xs text-[#785E4E] hover:bg-[#E6DDD0] font-semibold transition-colors cursor-pointer"
                                               x-text="formatDisplay(cat.category)">
                                       </button>
                                   </template>
                              </div>
                                                             <!-- Tooltip Card -->
                               <div x-show="open && hoveredCat"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-x-2"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-2"
                                    class="hidden lg:block absolute right-[calc(100%+0.75rem)] top-[54px] w-72 bg-[#FDFBF7] border-2 border-[#3D3025] rounded-xl p-4 shadow-lg z-[60] pointer-events-none text-left"
                                    style="display: none;">
                                    <div class="text-[10px] font-extrabold uppercase text-[#b26d27] mb-1.5 tracking-wider" x-text="formatDisplay(hoveredCat)"></div>
                                    <div class="text-xs text-[#5A4535] font-semibold leading-relaxed whitespace-pre-line">
                                        <template x-if="getExplanation(hoveredCat)">
                                            <span x-text="getExplanation(hoveredCat)"></span>
                                        </template>
                                        <template x-if="!getExplanation(hoveredCat)">
                                            <span class="text-red-500 font-bold">(?)</span>
                                        </template>
                                    </div>
                               </div>
                          </div>

                          <!-- Sub-Layanan Select -->
                          <div class="relative" x-data="{ open: false, hoveredSub: null }" @click.away="open = false; hoveredSub = null">
                              <label class="block text-[10px] font-bold text-gray-800 mb-1">Sub-Layanan (level 2)</label>
                              <button type="button" @click="if (kategori) open = !open" :disabled="!kategori"
                                      class="w-full flex items-center justify-between bg-[#EFE9DF] border-2 border-[#3D3025] text-[#5A4535] rounded-xl px-4 py-2.5 text-xs font-bold transition-all outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                                  <span x-text="subLayanan ? formatDisplay(subLayanan) : '-Pilih Sub-Layanan-'"></span>
                                  <i data-lucide="chevron-down" class="w-4 h-4 text-[#8C7662] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                              </button>
                              <div x-show="open" 
                                   x-transition:enter="transition ease-out duration-100"
                                   x-transition:enter-start="opacity-0 scale-95"
                                   x-transition:enter-end="opacity-100 scale-100"
                                   x-transition:leave="transition ease-in duration-75"
                                   x-transition:leave-start="opacity-100 scale-100"
                                   x-transition:leave-end="opacity-0 scale-95"
                                   class="absolute z-50 left-0 right-0 mt-1 bg-[#EFE9DF] border-2 border-[#3D3025] rounded-xl shadow-lg overflow-hidden flex flex-col"
                                   style="display: none;">
                                   <template x-for="sub in getSubLayananList()">
                                       <button type="button" 
                                               @mouseenter="hoveredSub = sub.name"
                                               @mouseleave="hoveredSub = null"
                                               @click="subLayanan = sub.name; onSubChange(); open = false; hoveredSub = null;"
                                               class="w-full text-left px-4 py-2.5 text-xs text-[#785E4E] hover:bg-[#E6DDD0] font-semibold transition-colors cursor-pointer"
                                               x-text="formatDisplay(sub.name)">
                                       </button>
                                   </template>
                              </div>
                                                             <!-- Tooltip Card -->
                               <div x-show="open && hoveredSub"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-x-2"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-2"
                                    class="hidden lg:block absolute right-[calc(100%+0.75rem)] top-[54px] w-72 bg-[#FDFBF7] border-2 border-[#3D3025] rounded-xl p-4 shadow-lg z-[60] pointer-events-none text-left"
                                    style="display: none;">
                                    <div class="text-[10px] font-extrabold uppercase text-[#b26d27] mb-1.5 tracking-wider" x-text="formatDisplay(hoveredSub)"></div>
                                    <div class="text-xs text-[#5A4535] font-semibold leading-relaxed whitespace-pre-line">
                                        <template x-if="getExplanation(hoveredSub)">
                                            <span x-text="getExplanation(hoveredSub)"></span>
                                        </template>
                                        <template x-if="!getExplanation(hoveredSub)">
                                            <span class="text-red-500 font-bold">(?)</span>
                                        </template>
                                    </div>
                               </div>
                          </div>

                          <!-- Detail Layanan Select (Level 3) -->
                          <div class="relative" x-show="getDetailLayananList().length > 0" x-data="{ open: false, hoveredItem: null }" @click.away="open = false; hoveredItem = null" style="display: none;">
                              <label class="block text-[10px] font-bold text-gray-800 mb-1">Detail Layanan (level 3)</label>
                              <button type="button" @click="if (subLayanan) open = !open" :disabled="!subLayanan"
                                      class="w-full flex items-center justify-between bg-[#EFE9DF] border-2 border-[#3D3025] text-[#5A4535] rounded-xl px-4 py-2.5 text-xs font-bold transition-all outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                                  <span x-text="detailLayanan ? formatDisplay(detailLayanan) : '-Pilih Detail-'"></span>
                                  <i data-lucide="chevron-down" class="w-4 h-4 text-[#8C7662] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                              </button>
                              <div x-show="open" 
                                   x-transition:enter="transition ease-out duration-100"
                                   x-transition:enter-start="opacity-0 scale-95"
                                   x-transition:enter-end="opacity-100 scale-100"
                                   x-transition:leave="transition ease-in duration-75"
                                   x-transition:leave-start="opacity-100 scale-100"
                                   x-transition:leave-end="opacity-0 scale-95"
                                   class="absolute z-50 left-0 right-0 mt-1 bg-[#EFE9DF] border-2 border-[#3D3025] rounded-xl shadow-lg overflow-hidden flex flex-col"
                                   style="display: none;">
                                   <template x-for="item in getDetailLayananList()">
                                        <button type="button" 
                                                @mouseenter="hoveredItem = item"
                                                @mouseleave="hoveredItem = null"
                                                @click="detailLayanan = item; open = false; hoveredItem = null;"
                                                class="w-full text-left px-4 py-2.5 text-xs text-[#785E4E] hover:bg-[#E6DDD0] font-semibold transition-colors cursor-pointer"
                                                x-text="formatDisplay(item)">
                                        </button>
                                    </template>
                               </div>
                               <!-- Tooltip Card -->
                               <div x-show="open && hoveredItem"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-x-2"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-2"
                                    class="hidden lg:block absolute right-[calc(100%+0.75rem)] top-[54px] w-72 bg-[#FDFBF7] border-2 border-[#3D3025] rounded-xl p-4 shadow-lg z-[60] pointer-events-none text-left"
                                    style="display: none;">
                                    <div class="text-[10px] font-extrabold uppercase text-[#b26d27] mb-1.5 tracking-wider" x-text="formatDisplay(hoveredItem)"></div>
                                    <div class="text-xs text-[#5A4535] font-semibold leading-relaxed whitespace-pre-line">
                                        <template x-if="getExplanation(hoveredItem)">
                                            <span x-text="getExplanation(hoveredItem)"></span>
                                        </template>
                                        <template x-if="!getExplanation(hoveredItem)">
                                            <span class="text-red-500 font-bold">(?)</span>
                                        </template>
                                    </div>
                               </div>
                           </div>

                         <!-- Detail Masalah -->
                         <div>
                             <label class="block text-[10px] font-bold text-gray-800 mb-1">Deskripsi permintaan layanan</label>
                             <textarea x-model="detailMasalah" rows="5" placeholder="Tulis deskripsi kendala/permintaan..." class="w-full bg-white border border-slate-200 focus:border-[#b26d27] text-gray-800 rounded-xl px-3.5 py-2.5 text-xs outline-none transition-all font-semibold placeholder:text-gray-300"></textarea>
                         </div>

                         <!-- Remote Support Checkbox -->
                         <div class="flex items-center gap-2 py-1">
                             <input type="checkbox" x-model="bisaRemote" id="bisaRemoteCheckbox" class="w-4 h-4 rounded text-[#b26d27] border-gray-300 focus:ring-[#b26d27] cursor-pointer">
                             <label for="bisaRemoteCheckbox" class="text-[10px] font-bold text-gray-800 cursor-pointer">Dapat diselesaikan secara remote / jarak jauh (tanpa kunjungan fisik)</label>
                         </div>

                         <!-- Submit Button -->
                         <button type="submit" :disabled="loadingSubmit" class="w-full bg-[#E7BE8D] hover:bg-[#d9ab75] text-white font-bold text-xs py-3 rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                             <i data-lucide="send" class="w-4 h-4"></i>
                             <span x-text="loadingSubmit ? 'Mengirim...' : 'Kirim permintaan layanan'"></span>
                         </button>
                     </form>
                 </div>
             </div>
        </div>

    </div>

    <!-- Lightbox Modal -->
    <div x-show="lightboxOpen" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4" @click="lightboxOpen = false" style="display: none;" x-transition>
        <div class="relative max-w-4xl max-h-[90vh] bg-transparent" @click.stop>
            <img :src="lightboxImageUrl" class="max-w-full max-h-[80vh] object-contain rounded-xl shadow-2xl">
            <button @click="lightboxOpen = false" class="absolute -top-10 right-0 text-white font-bold hover:text-slate-300 transition-colors flex items-center gap-1 cursor-pointer text-xs">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Tutup</span>
            </button>
        </div>
    </div>

    <!-- Success Ticket Modal -->
    <div x-show="showSuccessModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-[100] p-4" 
         style="display: none;">
        <div class="bg-white rounded-3xl p-6 md:p-8 max-w-sm w-full shadow-2xl text-center border border-gray-100 relative" @click.stop>
            <!-- Icon -->
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
            </div>
            
            <h3 class="text-base font-bold text-gray-900 font-display">Tiket Berhasil Dibuat</h3>
            
            <p class="text-xs text-gray-600 mt-2 leading-relaxed font-medium">
                Permintaan layanan Anda telah berhasil terdaftar dengan kode tiket:
            </p>
            
            <div class="mt-3 py-2 px-4 bg-[#FAF4EE] border border-[#F0DCC0] rounded-xl inline-block">
                <span class="text-sm font-extrabold text-[#b26d27] font-mono tracking-wide" x-text="createdTicketCode"></span>
            </div>

            <div class="mt-6">
                <button type="button" 
                        @click="showSuccessModal = false" 
                        class="w-full bg-[#b26d27] hover:bg-[#9b5a1b] text-white py-2.5 px-4 rounded-xl font-bold text-xs transition-all cursor-pointer shadow-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function berandaPage() {
        return {
            kategori: '',
            subLayanan: '',
            detailLayanan: '',
            detailMasalah: '',
            bisaRemote: false,
            loadingSubmit: false,
            successMessage: '',
            showForm: false,
            showSuccessModal: false,
            createdTicketCode: '',
            attachedImage: null,
            isDragging: false,
            lightboxOpen: false,
            lightboxImageUrl: '',
            
            userId: '{{ Auth::user()->username }}',
            chatHistoryKey: '',
            chatHistoryTimeKey: '',
            getFaqLink(rec) {
                if (!rec) return 'http://127.0.0.1:8000/dashboard/faq';
                
                let topic = 'identitas';
                if (rec.category) {
                    const cat = rec.category.toLowerCase();
                    if (cat.includes('identitas')) topic = 'identitas';
                    else if (cat.includes('data')) topic = 'data';
                    else if (cat.includes('aplikasi')) topic = 'aplikasi';
                    else if (cat.includes('teknologi')) topic = 'teknologi';
                    else if (cat.includes('perangkat')) topic = 'perangkat';
                    else if (cat.includes('dukungan')) topic = 'dukungan';
                    else if (cat.includes('informasi')) topic = 'informasi';
                }
                
                let search = '';
                if (rec.sub) {
                    const sub = rec.sub.toLowerCase();
                    if (sub.includes('akun')) search = 'akun';
                    else if (sub.includes('tte')) search = 'tte';
                    else if (sub.includes('segel')) search = 'segel';
                    else if (sub.includes('email')) search = 'email';
                    else if (sub.includes('mfa')) search = 'mfa';
                    else if (sub.includes('lan') || sub.includes('intranet') || sub.includes('wifi')) search = 'wifi';
                    else if (sub.includes('internet')) search = 'internet';
                    else if (sub.includes('vpn')) search = 'vpn';
                    else if (sub.includes('hosting')) search = 'hosting';
                    else if (sub.includes('komputer') || sub.includes('perangkat') || sub.includes('laptop')) search = 'perangkat';
                }
                
                if (!search && rec.service) {
                    const srv = rec.service.toLowerCase();
                    if (srv.includes('akun')) search = 'akun';
                    else if (srv.includes('tte')) search = 'tte';
                    else if (srv.includes('segel')) search = 'segel';
                    else if (srv.includes('email')) search = 'email';
                    else if (srv.includes('mfa')) search = 'mfa';
                    else if (srv.includes('vpn')) search = 'vpn';
                    else if (srv.includes('lan')) search = 'lan';
                    else if (srv.includes('wifi')) search = 'wifi';
                    else if (srv.includes('laptop') || srv.includes('komputer')) search = 'perangkat';
                }
                
                return `http://127.0.0.1:8000/dashboard/faq?topic=${topic}&search=${search}`;
            },

            // Chatbot State
            chatInput: '',
            chatLoading: false,
            chatMessages: [
                {
                    id: 'welcome',
                    sender: 'bot',
                    text: 'Halo! Saya adalah Asisten Virtual Layanan TI BPK berbasis Google Gemini AI. Deskripsikan kendala atau permintaan layanan Anda dalam bahasa sehari-hari (contoh: "kabel LAN saya rusak" atau "lupa password email dinas"), dan saya akan merekomendasikan kategori layanan yang tepat secara cerdas.'
                }
            ],
            timerExpired: false,
            firstUserMessageTime: null,
            remainingSeconds: 300,
            timerInterval: null,

            catalog: [
                {
                    category: "Layanan Identitas",
                    subs: [
                        { name: "Layanan Akun", items: [] },
                        { name: "Layanan TTE", items: [] },
                        { name: "Layanan Segel Elektronik", items: [] },
                        { name: "Layanan Email", items: [] },
                        { name: "Layanan MFA", items: [] }
                    ]
                },
                {
                    category: "Layanan Data",
                    subs: [
                        { name: "Pengelolaan Data", items: ["Perencanaan Data", "Pengumpulan Data", "Pengolahan Data", "Penyimpanan Data", "Penyebarluasan Data", "Analisis Data", "Pengamanan Data", "Pemusnahan Data"] },
                        { name: "Layanan Sistem Layanan Data", items: ["BIDICS Dashboard", "BIDICS-SSA"] }
                    ]
                },
                {
                    category: "Layanan Aplikasi",
                    subs: [
                        { name: "Pengembangan Aplikasi", items: [] },
                        { name: "Aplikasi Pemeriksaan", items: [] },
                        { name: "Aplikasi Kelembagaan", items: [] },
                        { name: "Aplikasi Pendukung", items: [] },
                        { name: "Aplikasi Kolaborasi", items: [] },
                        { name: "Layanan Survei", items: [] }
                    ]
                },
                {
                    category: "Layanan Teknologi",
                    subs: [
                        { name: "Layanan Intranet", items: ["Pembuatan Local Area Network (LAN)", "Pengaturan konfigurasi LAN", "Penonaktifan LAN", "Penyediaan kabel LAN", "Pemasangan perangkat Wireless Fidelity (Wifi)", "Pengaturan konfigurasi Wifi", "Penonaktifan Wifi"] },
                        { name: "Layanan Internet", items: ["Pemasangan perangkat koneksi internet", "Pengaturan konfigurasi perangkat koneksi internet", "Penonaktifan perangkat koneksi internet"] },
                        { name: "Layanan Virtual Private Network", items: ["Pemasangan VPN", "Pengaturan konfigurasi VPN", "Penonaktifan VPN"] },
                        { name: "Layanan Hosting", items: ["Pendaftaran hosting subdomain", "Pengaturan konfigurasi hosting subdomain", "Penonaktifan hosting subdomain"] }
                    ]
                },
                {
                    category: "Layanan Perangkat",
                    subs: [
                        { name: "Standarisasi Perangkat Komputer", items: [] },
                        { name: "Pemeliharaan Perangkat", items: [] },
                        { name: "Peminjaman Perangkat", items: [] },
                        { name: "Penyediaan Barang Persediaan", items: [] }
                    ]
                },
                {
                    category: "Layanan Dukungan TI Untuk Kegiatan Khusus",
                    subs: [
                        { name: "Pendampingan Personel TI", items: [] }
                    ]
                },
                {
                    category: "Layanan Informasi",
                    subs: [
                        { name: "Knowledge Base Produk TI", items: [] },
                        { name: "Informasi Produk TI", items: [] },
                        { name: "Tugas dan Fungsi Biro TI", items: [] }
                    ]
                }
            ],

            subbagRouting: {
                'Layanan Identitas': 'Subbagian Pelayanan TIK',
                'Layanan Data': 'Subbagian Tata Kelola Data',
                'Layanan Aplikasi': 'Subbagian Pengembangan Sistem Informasi Pemeriksaan',
                'Layanan Teknologi': 'Subbagian Pengelolaan Infrastruktur dan Jaringan',
                'Layanan Perangkat': 'Subbagian Pelayanan TIK',
                'Layanan Dukungan TI Untuk Kegiatan Khusus': 'Subbagian Pelayanan TIK',
                'Layanan Informasi': 'Subbagian MIOT',
                'Layanan TTE': 'Subbagian Keamanan Informasi',
                'Layanan Segel Elektronik': 'Subbagian Keamanan Informasi',
                'Layanan MFA': 'Subbagian Keamanan Informasi',
                'Layanan Sistem Layanan Data': 'Subbagian Sains Data',
                'Aplikasi Kelembagaan': 'Subbagian Pengembangan Sistem Informasi Kelembagaan',
                'Aplikasi Pendukung': 'Subbagian Pengembangan Sistem Informasi Kelembagaan',
                'Aplikasi Kolaborasi': 'Subbagian Pelayanan TIK',
                'Layanan Survei': 'Subbagian Pelayanan TIK',
            },

            formatDisplay(text) {
                if (!text) return '';
                if (text === 'Layanan Dukungan TI Untuk Kegiatan Khusus') {
                    return 'Layanan dukungan TI';
                }
                let parts = text.split(' ');
                if (parts.length > 1) {
                    return parts[0] + ' ' + parts.slice(1).join(' ').toLowerCase();
                }
                return text;
            },

            getExplanation(key) {
                const explanations = {
                    // Layanan Identitas (Level 2 subs)
                    "Layanan Akun": "Cakupan Layanan Akun: Pembuatan akun, Perubahan akun, Pengaktifan/penonaktifan akun, dan reset password akun.",
                    "Layanan TTE": "Layanan TTE dengan cakupan: Informasi TTE, Pendaftaran, verifikasi data pendaftar, Pembaruan dan Penonaktifan TTE.",
                    "Layanan Segel Elektronik": "Layanan Segel Elektronik dengan lingkup: Informasi layanan, Registrasi dan verifikasi calon penanggung jawab segel, Penerbitan segel elektronik, Pembaruan dan Penonaktifan segel elektronik.",
                    "Layanan Email": "Cakupan Layanan Email: Pembuatan, perubahan, dan penonaktifan akun email, akun Shared Mailbox, Mailing list, dan broadcast email.",
                    "Layanan MFA": "Cakupan Layanan MFA: Pendaftaran, Perubahan, dan Penonaktifan MFA.",

                    // Layanan Data (Level 3 items)
                    "Perencanaan Data": "Layanan Perencanaan Data mencakup: Pendaftaran kebutuhan data, dan Perubahan kebutuhan data.",
                    "Pengumpulan Data": "Layanan Pengumpulan Data mencakup: Penyediaan, Penerimaan, dan Pertukaran data.",
                    "Pengolahan Data": "Layanan Pengolahan Data mencakup: Kompilasi, Pembersihan, serta Verifikasi dan validasi data.",
                    "Penyimpanan Data": "Layanan Penyimpanan Data mencakup: Pengunggahan, Restore, dan Pembuatan salinan/backup data.",
                    "Penyebarluasan Data": "Layanan Penyebarluasan Data mencakup: Pemberian akses data, Pendistribusian data, dan Pertukaran data antar Satuan Kerja.",
                    "Analisis Data": "Layanan Analisis Data: Pengembangan model analitik, Pengembangan cluster penyajian data, Perubahan model analitik data, Perubahan cluster penyajian data, Pendaftaran, Perubahan, dan Pencabutan akses, Penyediaan data dan Pendampingan analisis data.",
                    "Pengamanan Data": "Layanan Pengamanan Data: Pengamanan perangkat pengolahan data, Enkripsi data, Penandatanganan Pakta Integritas Penggunaan Data BPK, Penandatanganan Perjanjian Kerahasiaan Data, dan Pengembalian data di saat pengguna data sudah tidak memiliki kewenangan menggunakan data.",
                    "Pemusnahan Data": "Layanan Pemusnahan Data mencakup: Informasi data yang dimusnahkan, dan Pemusnahan fisik data.",
                    "BIDICS Dashboard": "BIDICS Dashboard mencakup: Pengembangan model analitik, Pengembangan cluster, dan Platform BIDICS-SSA.",
                    "BIDICS-SSA": "BIDICS-SSA: Layanan ini mencakup pemanfaatan hasil analisis data yang disajikan pada BIDICS-SSA.",

                    // Layanan Aplikasi (Level 2 subs)
                    "Pengembangan Aplikasi": "Pengembangan Aplikasi:\nPembangunan, perubahan, dan penghapusan.",
                    "Aplikasi Pemeriksaan": "Aplikasi Pemeriksaan:\nTerkait dengan aplikasi pendukung proses bisnis pemeriksaan BPK yang dikembangkan secara in-house, outsource, atau kombinasi (in-house dan outsource).",
                    "Aplikasi Kelembagaan": "Aplikasi Kelembagaan:\nAplikasi pendukung proses bisnis kelembagaan.",
                    "Aplikasi Pendukung": "Aplikasi Pendukung:\nMerupakan aplikasi pihak ketiga yang dapat diterapkan pada perangkat di luar aplikasi standar BPK (install/uninstall, upgrade, patch aplikasi, dan konfigurasi aplikasi).",
                    "Aplikasi Kolaborasi": "Aplikasi Kolaborasi:\nTerkait dengan pengelolaan aplikasi pendukung perkantoran dan aplikasi lain yang disediakan pada platform on-premise dan/atau on-cloud, digunakan untuk pelaksanaan tugas BPK secara kolaboratif di mana Pegawai BPK dapat bekerja bersama-sama.",
                    "Layanan Survei": "Layanan Survei:\nMerupakan layanan untuk mendukung pelaksanaan pengumpulan data dan informasi dari berbagai responden.",

                    // Layanan Teknologi (Level 2 subs)
                    "Layanan Intranet": "Layanan Intranet:\nCakupan: Pembuatan, konfigurasi, dan penonaktifan LAN, Pemasangan, konfigurasi, dan penonaktifan Wi-Fi.",
                    "Layanan Internet": "Layanan Internet:\nCakupan: Pemasangan, pengaturan, dan penonaktifan internet.",
                    "Layanan Virtual Private Network": "Layanan Virtual Private Network (VPN):\nCakupan: Pemasangan dan penonaktifan VPN.",
                    "Layanan Hosting": "Layanan Hosting:\nLayanan ini mencakup: Pendaftaran hosting subdomain, Pembuatan Virtual Machine (VM), Pengaturan kapasitas penyimpanan dan komputasi, Pengaturan konfigurasi hosting subdomain, serta Pengaktifan atau penonaktifan hosting subdomain.",

                    // Layanan Perangkat (Level 2 subs)
                    "Standarisasi Perangkat Komputer": "Standarisasi Perangkat:\nMencakup standarisasi perangkat baru dan perangkat dalam rangka pemeliharaan.",
                    "Peminjaman Perangkat": "Layanan Peminjaman Perangkat:\nPeminjaman modem, router, access point Wi-Fi, serta peminjaman perangkat video conference dan perangkat pendukung lainnya.",
                    "Pemeliharaan Perangkat": "Layanan Pemeliharaan Perangkat Komputer:\nPemeliharaan Personal Computer (PC) berupa desktop dan laptop, Konfigurasi periferal baik yang dilakukan di ruangan layanan TI secara langsung maupun remote, serta pemberian penugasan kepada personel untuk melakukan kunjungan ke tempat perangkat berada.",
                    "Penyediaan Barang Persediaan": "Penyediaan Barang Persediaan:\nMencakup penyediaan suku cadang dan periferal komputer lainnya.",

                    // Layanan Dukungan TI (Level 1 category & Level 2 sub)
                    "Layanan Dukungan TI Untuk Kegiatan Khusus": "Layanan Dukungan TI untuk Kegiatan Khusus:\nMencakup penugasan personel sebagai narasumber dan pendamping dukungan teknis kegiatan, termasuk pemenuhan kebutuhan perangkat yang diperlukan.",
                    "Pendampingan Personel TI": "Layanan Dukungan TI untuk Kegiatan Khusus:\nMencakup penugasan personel sebagai narasumber dan pendamping dukungan teknis kegiatan, termasuk pemenuhan kebutuhan perangkat yang diperlukan.",

                    // Layanan Informasi (Level 1 category & Level 2 subs)
                    "Layanan Informasi": "Layanan Informasi:\nKnowledge base produk TI, Informasi dan produk layanan TI, serta Tugas dan fungsi Biro TI.",
                    "Knowledge Base Produk TI": "Layanan Informasi:\nKnowledge base produk TI, Informasi dan produk layanan TI, serta Tugas dan fungsi Biro TI.",
                    "Informasi Produk TI": "Layanan Informasi:\nKnowledge base produk TI, Informasi dan produk layanan TI, serta Tugas dan fungsi Biro TI.",
                    "Tugas dan Fungsi Biro TI": "Layanan Informasi:\nKnowledge base produk TI, Informasi dan produk layanan TI, serta Tugas dan fungsi Biro TI.",
                };
                return explanations[key] || '';
            },

            getBotResponseCount() {
                return this.chatMessages.filter(m => m.sender === 'bot' && m.id !== 'welcome').length;
            },

            shouldShowFaqButton(msg) {
                if (msg.sender !== 'bot' || !msg.recommendation) {
                    return false;
                }
                const solvingMessages = this.chatMessages.filter(m => m.sender === 'bot' && m.recommendation);
                const index = solvingMessages.findIndex(m => m.id === msg.id);
                return index >= 0 && index < 2;
            },

            formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
            },

            init() {
                this.chatHistoryKey = 'melati_chat_history_' + this.userId;
                this.chatHistoryTimeKey = 'melati_chat_history_time_' + this.userId;

                const savedTime = localStorage.getItem(this.chatHistoryTimeKey);
                const savedMessages = localStorage.getItem(this.chatHistoryKey);

                if (savedTime && savedMessages) {
                    const timeDiff = Date.now() - parseInt(savedTime);
                    const tenMinutes = 10 * 60 * 1000;
                    if (timeDiff < tenMinutes) {
                        this.chatMessages = JSON.parse(savedMessages);
                    } else {
                        this.clearChatHistory();
                    }
                }
                localStorage.setItem(this.chatHistoryTimeKey, Date.now().toString());

                this.$watch('chatMessages', value => {
                    localStorage.setItem(this.chatHistoryKey, JSON.stringify(value));
                    localStorage.setItem(this.chatHistoryTimeKey, Date.now().toString());
                });

                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('showForm') === 'true') {
                    this.showForm = true;
                }
                this.$nextTick(() => {
                    this.scrollChat();
                    if (window.lucide) lucide.createIcons();
                });
            },

            clearChatHistory() {
                localStorage.removeItem(this.chatHistoryKey);
                localStorage.removeItem(this.chatHistoryTimeKey);
                this.chatMessages = [
                    {
                        id: 'welcome',
                        sender: 'bot',
                        text: 'Halo! Saya adalah Asisten Virtual Layanan TI BPK berbasis Google Gemini AI. Deskripsikan kendala atau permintaan layanan Anda dalam bahasa sehari-hari (contoh: "kabel LAN saya rusak" atau "lupa password email dinas"), dan saya akan merekomendasikan kategori layanan yang tepat secara cerdas.'
                    }
                ];
            },

            onCategoryChange() {
                this.subLayanan = '';
                this.detailLayanan = '';
            },

            onSubChange() {
                this.detailLayanan = '';
            },

            getSubLayananList() {
                const node = this.catalog.find(c => c.category === this.kategori);
                return node ? node.subs : [];
            },

            getDetailLayananList() {
                const subs = this.getSubLayananList();
                const subNode = subs.find(s => s.name === this.subLayanan);
                return subNode ? subNode.items : [];
            },

            applyRecommendation(rec) {
                this.kategori = rec.category;
                this.showForm = true;
                if (rec && rec.problem_analysis) {
                    this.detailMasalah = rec.problem_analysis;
                }
                this.$nextTick(() => {
                    this.subLayanan = rec.sub;
                    this.$nextTick(() => {
                        this.detailLayanan = rec.service;
                    });
                });
            },

            async submitForm() {
                if (!this.kategori || !this.subLayanan || !this.detailMasalah.trim()) {
                    alert('Harap isi semua kolom form yang diperlukan.');
                    return;
                }

                this.loadingSubmit = true;
                this.successMessage = '';

                try {
                    const response = await fetch('/api/tickets', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            layananKategori: this.kategori,
                            layananSub: this.subLayanan,
                            layanan: this.detailLayanan || this.subLayanan,
                            detail: this.detailMasalah,
                            bisa_remote: this.bisaRemote
                        })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.successMessage = data.id;
                        this.createdTicketCode = data.id;
                        this.showForm = false;
                        this.showSuccessModal = true;
                        this.kategori = '';
                        this.subLayanan = '';
                        this.detailLayanan = '';
                        this.detailMasalah = '';
                        this.bisaRemote = false;
                        this.$nextTick(() => {
                            if (window.lucide) lucide.createIcons();
                        });
                    } else {
                        alert(data.error || 'Gagal mengirim tiket.');
                    }
                } catch (err) {
                    alert('Terjadi kesalahan jaringan.');
                } finally {
                    this.loadingSubmit = false;
                }
            },

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.processImageFile(file);
                }
            },

            handleImagePaste(event) {
                const items = (event.clipboardData || event.originalEvent.clipboardData).items;
                for (let index in items) {
                    const item = items[index];
                    if (item.kind === 'file' && item.type.indexOf('image/') !== -1) {
                        const file = item.getAsFile();
                        this.processImageFile(file);
                        event.preventDefault();
                        break;
                    }
                }
            },

            handleDrop(event) {
                this.isDragging = false;
                const files = event.dataTransfer.files;
                if (files && files.length > 0) {
                    this.processImageFile(files[0]);
                }
            },

            processImageFile(file) {
                if (!file.type.startsWith('image/')) {
                    alert('Harap unggah file gambar saja.');
                    return;
                }
                if (file.type === 'image/gif' || file.name.toLowerCase().endsWith('.gif')) {
                    alert('Format GIF tidak didukung. Harap unggah format gambar lain seperti PNG atau JPEG.');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran gambar maksimal adalah 5MB.');
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    const base64Data = e.target.result.split(',')[1];
                    this.attachedImage = {
                        name: file.name || 'Pasted-Screenshot.png',
                        size: file.size,
                        mimeType: file.type,
                        dataUrl: e.target.result,
                        base64: base64Data
                    };
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });
                };
                reader.readAsDataURL(file);
            },

            removeAttachedImage() {
                this.attachedImage = null;
                const fileInput = document.getElementById('chat-file-input');
                if (fileInput) fileInput.value = '';
            },

            openImageLightbox(url) {
                this.lightboxImageUrl = url;
                this.lightboxOpen = true;
                this.$nextTick(() => {
                    lucide.createIcons();
                });
            },

            formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            },

            async sendChatMessage() {
                if (!this.chatInput.trim() && !this.attachedImage) return;

                const userText = this.chatInput.trim();
                const newUserMsg = {
                    id: 'usr-' + Date.now(),
                    sender: 'user',
                    text: userText || 'Mengirim gambar...',
                    image: this.attachedImage ? {
                        dataUrl: this.attachedImage.dataUrl,
                        mimeType: this.attachedImage.mimeType,
                        data: this.attachedImage.base64
                    } : null
                };

                this.chatMessages.push(newUserMsg);
                this.chatInput = '';
                
                const currentAttachedImage = this.attachedImage;
                this.removeAttachedImage();
                
                this.chatLoading = true;

                // Start 5-minute timer on first user message
                if (!this.firstUserMessageTime) {
                    this.firstUserMessageTime = Date.now();
                    this.remainingSeconds = 300;
                    this.timerInterval = setInterval(() => {
                        const elapsed = Math.floor((Date.now() - this.firstUserMessageTime) / 1000);
                        this.remainingSeconds = Math.max(0, 300 - elapsed);
                        if (this.remainingSeconds <= 0) {
                            this.timerExpired = true;
                            clearInterval(this.timerInterval);
                        }
                    }, 1000);
                }

                this.scrollChat();

                try {
                    const updatedMessages = this.chatMessages.map(m => {
                        const msgObj = {
                            sender: m.sender,
                            text: m.text
                        };
                        if (m.image) {
                            msgObj.image = {
                                mimeType: m.image.mimeType,
                                data: m.image.data
                            };
                        }
                        return msgObj;
                    });

                    const response = await fetch('/api/chat-recommend', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ messages: updatedMessages })
                    });

                    const data = await response.json();
                    this.chatLoading = false;

                    if (response.ok && data.reply) {
                        this.chatMessages.push({
                            id: 'bot-' + Date.now(),
                            sender: 'bot',
                            text: data.reply,
                            recommendation: data.recommendation,
                            showConfirmation: data.recommendation ? !!data.suggest_ticket : false,
                            confirmed: false
                        });
                    } else {
                        throw new Error("Backend error or rate limit");
                    }
                } catch (err) {
                    console.warn("AI Chatbot failed, using local keyword matching fallback:", err);
                    this.localFallback(userText || '');
                } finally {
                    this.$nextTick(() => {
                        this.scrollChat();
                        lucide.createIcons();
                    });
                }
            },

            localFallback(userText) {
                this.chatLoading = false;
                const cleanedInput = userText.toLowerCase();

                const rules = [
                    { keywords: ["lan", "kabel lan", "kabel", "colokan lan", "port lan"], category: "Layanan Teknologi", sub: "Layanan Intranet", service: "Penyediaan kabel LAN ruang kerja", hint: "Cobalah untuk menggunakan kabel LAN cadangan atau periksa koneksi colokan port LAN pada komputer/dinding." },
                    { keywords: ["wifi", "wi-fi", "wireless", "internet kantor", "sinyal wifi"], category: "Layanan Teknologi", sub: "Layanan Intranet", service: "Pengaturan konfigurasi Wifi Biro", hint: "Pastikan Wifi laptop Anda dalam keadaan ON, disconnect dan hubungkan kembali, atau periksa sinyal hotspot Anda." },
                    { keywords: ["mfa", "multi factor", "google authenticator", "otp", "token mfa"], category: "Layanan Identitas", sub: "Layanan MFA", service: "Registrasi Multi-Factor Authentication Baru", hint: "Buka aplikasi Google Authenticator di handphone Anda, pastikan sinkronisasi waktu sudah tepat." },
                    { keywords: ["mfa reset", "reset mfa", "authenticator", "otp salah"], category: "Layanan Identitas", sub: "Layanan MFA", service: "Reset Token MFA / Google Authenticator", hint: "Jika OTP tidak sinkron, silakan atur sinkronisasi waktu (Time Sync) pada aplikasi Google Authenticator Anda." },
                    { keywords: ["vpn", "vpn bpk", "akses intranet", "vpn error", "connect vpn"], category: "Layanan Teknologi", sub: "Layanan Virtual Private Network", service: "Pemasangan VPN BPK di Laptop", hint: "Pastikan koneksi internet Anda stabil sebelum mengaktifkan VPN BPK. Coba sambungkan ulang client VPN Anda." },
                    { keywords: ["buat akun", "akun baru", "daftar portal", "user baru"], category: "Layanan Identitas", sub: "Layanan Akun", service: "Pembuatan Akun Baru Portal BPK", hint: "Silakan akses halaman registrasi di portal utama untuk membuat akun baru BPK." },
                    { keywords: ["lupa password", "reset password", "password locked", "ganti sandi"], category: "Layanan Identitas", sub: "Layanan Akun", service: "Reset Password / Masalah Login", hint: "Cobalah reset password menggunakan fitur lupa password di portal, pastikan Caps Lock tidak aktif." },
                    { keywords: ["tte", "tanda tangan", "tanda tangan elektronik", "sertifikat tte"], category: "Layanan Identitas", sub: "Layanan TTE", service: "Registrasi Sertifikat TTE Baru", hint: "Pastikan token USB TTE terpasang dan jalankan aplikasi support e-Signer BPK dengan benar." },
                    { keywords: ["email", "email dinas", "email bpk", "buat email"], category: "Layanan Identitas", sub: "Layanan Email", service: "Pembuatan Email Baru @bpk.go.id", hint: "Silakan hubungi Administrator untuk pembuatan email baru BPK." },
                    { keywords: ["email penuh", "kuota email", "storage email"], category: "Layanan Identitas", sub: "Layanan Email", service: "Masalah Kuota Email Penuh", hint: "Silakan buka webmail BPK, pastikan folder kiriman/sampah dibersihkan untuk melegakan kapasitas kotak masuk." },
                    { keywords: ["laptop lambat", "laptop lemot", "notebook", "upgrade ram", "perbaikan laptop"], category: "Layanan Perangkat", sub: "Pemeliharaan Perangkat", service: "Perbaikan Kerusakan Fisik Laptop Dinas", hint: "Unggah foto fisik laptop/bukti penyerahan ke Biro TI jika memerlukan perbaikan fisik." },
                    { keywords: ["router", "router rusak", "modem jaringan"], category: "Layanan Teknologi", sub: "Layanan Internet", service: "Pemasangan perangkat koneksi internet", hint: "Unggah foto fisik perangkat router/modem yang bermasalah untuk memverifikasi kerusakan." },
                    { keywords: ["proyektor", "projector", "proyektor buram", "kabel hdmi proyektor"], category: "Layanan Perangkat", sub: "Peminjaman Perangkat", service: "Peminjaman Projector / Proyektor", hint: "Unggah foto proyektor/perangkat yang berkendala jika memerlukan perbaikan/peminjaman baru." },
                    { keywords: ["mikrofon", "mic rapat", "sound system", "mikrofon konferensi"], category: "Layanan Perangkat", sub: "Peminjaman Perangkat", service: "Peminjaman Sound System", hint: "Unggah foto fisik mikrofon/sound system yang mengalami kendala untuk verifikasi fisik." },
                    { keywords: ["smartboard", "layar interaktif", "touchscreen rapat", "display interaktif"], category: "Layanan Perangkat", sub: "Pemeliharaan Perangkat", service: "Pemeliharaan Perangkat", hint: "Unggah foto layar interaktif/smartboard yang mengalami kendala teknis." },
                    { keywords: ["cairan", "air", "ketumpahan", "basah"], category: "Layanan Perangkat", sub: "Pemeliharaan Perangkat", service: "Perbaikan Kerusakan Fisik Laptop Dinas", hint: "Segera matikan daya perangkat apabila laptop terkena cairan (tumpahan air) dan cabut baterai/charger. Jangan dinyalakan sampai benar-benar kering." },
                    { keywords: ["virus", "antivirus", "malware", "scan laptop"], category: "Layanan Perangkat", sub: "Pemeliharaan Perangkat", service: "Instalasi Antivirus / Scan Malware Perangkat", hint: "Gunakan antivirus ter-update dan lakukan pemindaian penuh (Full Scan) pada direktori sistem Anda." },
                    { keywords: ["siap", "siap-bpk", "audit siap", "unggah kkp"], category: "Layanan Aplikasi", sub: "Aplikasi Pemeriksaan", service: "SiAP-BPK (Sistem Informasi Pemeriksaan)", hint: "Pastikan status internet aktif untuk menyinkronkan KKP offline ke sistem SiAP BPK." },
                    { keywords: ["sisdm", "kepegawaian", "absen sisdm", "cuti sisdm"], category: "Layanan Aplikasi", sub: "Aplikasi Kelembagaan", service: "Aplikasi Kepegawaian (SISDM BPK)", hint: "Untuk SISDM, pastikan jaringan intranet terhubung atau gunakan VPN BPK jika di luar kantor." },
                    { keywords: ["e-office", "eoffice", "persuratan", "naskah dinas"], category: "Layanan Aplikasi", sub: "Aplikasi Kelembagaan", service: "Aplikasi Persuratan Dinas (E-Office)", hint: "Periksa daftar surat masuk/keluar di E-Office, lakukan reload halaman jika respon lambat." }
                ];

                let bestRule = null;
                let maxScore = 0;

                for (const rule of rules) {
                    let score = 0;
                    for (const kw of rule.keywords) {
                        if (cleanedInput.includes(kw)) {
                            score += kw.split(" ").length;
                        }
                    }
                    if (score > maxScore) {
                        maxScore = score;
                        bestRule = rule;
                    }
                }

                if (maxScore > 0 && bestRule) {
                    const confidence = maxScore >= 2 ? "Tinggi" : "Sedang";
                    const localAnalysis = `- **Masalah Utama:** ${bestRule.service}\n- **Detail Kendala:** ${userText}\n- **Dampak:** Terganggunya aktivitas pekerjaan pegawai.`;
 
                    // Special location check for Wi-Fi configurations
                    let isWifi = bestRule.service === 'Pengaturan konfigurasi Wifi Biro';
                    if (isWifi) {
                        const wifiBotMessages = this.chatMessages.filter(m => m.sender === 'bot' && m.recommendation && m.recommendation.service === 'Pengaturan konfigurasi Wifi Biro');
                        
                        if (wifiBotMessages.length === 0) {
                            // LANGKAH 1: Solving pertama (troubleshooting tips)
                            this.chatMessages.push({
                                id: 'bot-' + Date.now(),
                                sender: 'bot',
                                text: `[Offline Fallback] Masalah Anda terdeteksi berkaitan dengan ${bestRule.service}. Tips Solusi: ${bestRule.hint} Apakah kendala Anda sudah teratasi?`,
                                recommendation: {
                                    category: bestRule.category,
                                    sub: bestRule.sub,
                                    service: bestRule.service,
                                    confidence: confidence,
                                    problem_analysis: localAnalysis
                                },
                                showConfirmation: false,
                                confirmed: false
                            });
                        } else {
                            // LANGKAH 2 & 3: Check if location is provided in chat history
                            const lowerChat = this.chatMessages.map(m => m.text.toLowerCase()).join(' ');
                            const hasMenara = lowerChat.includes('menara');
                            const hasLantai = lowerChat.includes('lantai') || /\blt\s*\d+/i.test(lowerChat) || /\bfloor\s*\d+/i.test(lowerChat);
                            const hasLocation = hasMenara && hasLantai;
 
                            if (!hasLocation) {
                                // LANGKAH 2: Tanya lokasi (menara & lantai)
                                this.chatMessages.push({
                                    id: 'bot-' + Date.now(),
                                    sender: 'bot',
                                    text: '[Offline Fallback] Mohon maaf atas ketidaknyamanan Anda. Sebelum kami membuatkan tiket bantuan untuk perbaikan koneksi Wi-Fi Anda, mohon informasikan terlebih dahulu lokasi Anda saat ini (menara mana dan lantai berapa)?',
                                    recommendation: {
                                        category: bestRule.category,
                                        sub: bestRule.sub,
                                        service: bestRule.service,
                                        confidence: confidence,
                                        problem_analysis: localAnalysis
                                    },
                                    showConfirmation: false,
                                    confirmed: false
                                });
                            } else {
                                // LANGKAH 3: Penawaran tiket (location is provided)
                                this.chatMessages.push({
                                    id: 'bot-' + Date.now(),
                                    sender: 'bot',
                                    text: '[Offline Fallback] Baik, terima kasih atas informasi lokasinya. Saya menemukan rekomendasi layanan yang sesuai untuk kendala Anda.',
                                    recommendation: {
                                        category: bestRule.category,
                                        sub: bestRule.sub,
                                        service: bestRule.service,
                                        confidence: confidence,
                                        problem_analysis: localAnalysis
                                    },
                                    showConfirmation: true,
                                    confirmed: false
                                });
                            }
                        }
                    } else if (this.getBotResponseCount() < 5) {
                        // Under 6th bot bubble: Only give troubleshooting tips/solving help
                        this.chatMessages.push({
                            id: 'bot-' + Date.now(),
                            sender: 'bot',
                            text: `[Offline Fallback] Masalah Anda terdeteksi berkaitan dengan ${bestRule.service}. Tips Solusi: ${bestRule.hint} Apakah kendala Anda sudah teratasi?`,
                            recommendation: {
                                category: bestRule.category,
                                sub: bestRule.sub,
                                service: bestRule.service,
                                confidence: confidence,
                                problem_analysis: localAnalysis
                            },
                            showConfirmation: false,
                            confirmed: false
                        });
                    } else {
                        // 5th bot bubble or later: Suggest ticket creation
                        this.chatMessages.push({
                            id: 'bot-' + Date.now(),
                            sender: 'bot',
                            text: '[Offline Fallback] Saya menemukan rekomendasi layanan yang sesuai untuk kendala Anda.',
                            recommendation: {
                                category: bestRule.category,
                                sub: bestRule.sub,
                                service: bestRule.service,
                                confidence: confidence,
                                problem_analysis: localAnalysis
                            },
                            showConfirmation: true,
                            confirmed: false
                        });
                    }
                } else {
                    const fallbackReplies = [
                        "Maaf, saya belum menemukan solusi yang cocok. Bisakah Anda menjelaskan masalah Anda dengan kata kunci lain? Seperti menggunakan kata 'email', 'wifi', 'laptop', 'siap-bpk', atau 'password'.",
                        "Saya tidak menemukan solusi untuk kata kunci tersebut di basis data offline. Harap jelaskan kembali detail kendala hardware, software, atau akun yang Anda alami."
                    ];
                    const reply = fallbackReplies[Math.floor(Math.random() * fallbackReplies.length)];
                    this.chatMessages.push({
                        id: 'bot-' + Date.now(),
                        sender: 'bot',
                        text: reply
                    });
                }
            },

            scrollChat() {
                const chatBox = document.getElementById('chat-box');
                if (chatBox) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            },

            formatMarkdown(text) {
                if (!text) return '';
                // Escape HTML first to prevent XSS
                let temp = document.createElement('div');
                temp.textContent = text;
                let safeHtml = temp.innerHTML;
                
                // Replace **text** with <strong>text</strong>
                safeHtml = safeHtml.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                // Replace *text* with <em>text</em>
                safeHtml = safeHtml.replace(/\*(.*?)\*/g, '<em>$1</em>');
                // Replace newlines with <br>
                safeHtml = safeHtml.replace(/\n/g, '<br>');
                return safeHtml;
            }
        };
    }
</script>
@endpush
