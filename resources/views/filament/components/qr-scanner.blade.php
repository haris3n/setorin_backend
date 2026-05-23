<div class="col-span-full py-4 px-6 bg-gray-50 dark:bg-gray-800/50 rounded-xl border-2 border-dashed border-emerald-500/40 dark:border-emerald-500/20 flex flex-col items-center justify-center text-center gap-3" style="background-color: #f0fdf4; border: 2px dashed #10b981; padding: 24px; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;">
    
    <!-- Scanner Icon -->
    <div style="background-color: #d1fae5; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
        <svg style="width: 36px; height: 36px; color: #059669;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 12v1.5m0 2.25H18a.75.75 0 0 0 .75-.75v-.75m-1.5 1.5H15a.75.75 0 0 0-.75.75v.75m1.5-1.5v1.5m-1.5-3v-1.5m-1.5 1.5H12a.75.75 0 0 0-.75.75v.75m1.5-1.5h1.5m-1.5-3v-1.5M3.75 12h1.5m1.5 0h1.5m-1.5 1.5v1.5m-1.5-1.5v1.5M12 3.75v1.5M12 9v1.5M20.25 3.75v1.5M20.25 9v1.5M3.75 20.25h1.5M12 20.25h1.5M20.25 20.25h1.5" />
        </svg>
    </div>

    <!-- Scan Button -->
    <button 
        type="button"
        x-data="{
            showScanner: false,
            html5QrcodeScanner: null,
            initScanner() {
                try {
                    if (this.html5QrcodeScanner) return;
                    
                    if (typeof Html5Qrcode === 'undefined') {
                        const script = document.createElement('script');
                        script.src = 'https://unpkg.com/html5-qrcode';
                        script.type = 'text/javascript';
                        script.onload = () => {
                            try {
                                this.html5QrcodeScanner = new Html5Qrcode('qr-reader');
                            } catch (e) {
                                console.error('Gagal instansiasi Html5Qrcode setelah onload:', e);
                            }
                        };
                        document.head.appendChild(script);
                    } else {
                        this.html5QrcodeScanner = new Html5Qrcode('qr-reader');
                    }
                } catch (e) {
                    console.error('Error initScanner:', e);
                }
            },
            startScanner() {
                this.showScanner = true;
                this.$nextTick(() => {
                    try {
                        this.initScanner();
                        setTimeout(() => {
                            try {
                                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                                
                                if (!this.html5QrcodeScanner) {
                                    this.html5QrcodeScanner = new Html5Qrcode('qr-reader');
                                }

                                this.html5QrcodeScanner.start(
                                    { facingMode: 'environment' },
                                    config,
                                    (decodedText, decodedResult) => {
                                        console.log('Barcode/QR Terdeteksi:', decodedText);
                                        
                                        // Set data di form Filament
                                        $wire.set('data.scan_code', decodedText);
                                        
                                        this.stopScanner();
                                    },
                                    (errorMessage) => {
                                        // ignore
                                    }
                                ).catch(err => {
                                    console.error('Gagal start scanner:', err);
                                    alert('Gagal mengakses kamera. Pastikan izin kamera aktif.');
                                    this.showScanner = false;
                                });
                            } catch (err) {
                                console.error('Gagal start scanner:', err);
                                this.showScanner = false;
                            }
                        }, 300);
                    } catch (err) {
                        console.error('Gagal persiapkan scanner:', err);
                        this.showScanner = false;
                    }
                });
            },
            stopScanner() {
                try {
                    this.showScanner = false;
                    if (this.html5QrcodeScanner) {
                        if (this.html5QrcodeScanner.isScanning) {
                            this.html5QrcodeScanner.stop().then(() => {
                                console.log('Scanner stopped successfully');
                            }).catch(err => {
                                console.error('Gagal stop scanner:', err);
                            });
                        }
                    }
                } catch (e) {
                    console.error('Exception in stopScanner:', e);
                }
            }
        }"
        @click="startScanner()"
        style="background-color: #10b981; color: white; padding: 12px 24px; border-radius: 8px; border: none; font-weight: bold; font-size: 15px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4); transition: background-color 0.2s;"
        onmouseover="this.style.backgroundColor='#059669'"
        onmouseout="this.style.backgroundColor='#10b981'"
    >
        <!-- Camera Icon inside Button -->
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span>Mulai Scan Kartu Nasabah</span>

        <!-- Scanner Camera Modal Overlay (teleported to body for escaping dialog limits) -->
        <template x-teleport="body">
            <div 
                x-show="showScanner" 
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-950/80 backdrop-blur-sm"
                x-transition
                style="display: none;"
            >
                <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-2xl border border-gray-200 dark:border-gray-800" @click.away="stopScanner()">
                    <div class="flex items-center justify-between mb-4 border-b border-gray-100 dark:border-gray-800 pb-3" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 16px;">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white" style="margin: 0; font-family: sans-serif; font-size: 16px; font-weight: bold; color: #111827;">Arahkan Kamera ke Barcode/QR Nasabah</h3>
                        <button 
                            type="button" 
                            @click="stopScanner()"
                            style="background: none; border: none; font-size: 20px; color: #9ca3af; cursor: pointer;"
                        >
                            ✕
                        </button>
                    </div>
                    
                    <div class="overflow-hidden rounded-xl bg-black aspect-square w-full mx-auto relative border border-gray-700" style="border-radius: 12px; overflow: hidden; background: black; border: 1px solid #374151;">
                        <div id="qr-reader" class="w-full h-full" style="width: 100%; height: 100%;"></div>
                    </div>

                    <div class="mt-4 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400" style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; font-family: sans-serif; font-size: 12px; color: #6b7280;">
                        <span>💡 Dekatkan Barcode/QR ke kamera</span>
                        <button 
                            type="button" 
                            @click="stopScanner()"
                            style="background-color: #f3f4f6; color: #374151; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </button>
</div>
