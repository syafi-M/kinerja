<footer class="fixed bottom-0 w-full bg-slate-800/95 backdrop-blur-xl border-t border-white/5">
    <div class="max-w-7xl mx-auto px-6 py-1">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">
            <!-- Left: Copyright -->
            <div class="flex items-center gap-2 text-slate-400 text-sm order-2 md:order-1">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>© {{ Carbon\Carbon::now()->format('Y') }} All rights reserved</span>
            </div>

            <!-- Center: Company -->
            <div class="flex items-center gap-2 order-1 md:order-2">
                <div class="h-6 w-px bg-gradient-to-b from-transparent via-slate-600 to-transparent hidden md:block">
                </div>
                <a href="https://sac-po.com"
                    class="group flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-white/5 transition-all duration-300">
                    <div
                        class="w-6 h-6 rounded-md bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                        S
                    </div>
                    <span class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors">
                        PT. Surya Amanah Cendekia
                    </span>
                </a>
                <div class="h-6 w-px bg-gradient-to-b from-transparent via-slate-600 to-transparent hidden md:block">
                </div>
            </div>

            <!-- Right: Developers -->
            <div class="flex items-center gap-1 text-xs text-slate-500 order-3">
                <span>Built by</span>
                <a href="https://github.com/aditlfp"
                    class="group flex items-center gap-1.5 px-2 py-1 rounded-md hover:bg-white/5 transition-all duration-300">
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                    </svg>
                    <span class="text-slate-400 group-hover:text-white transition-colors font-medium">Aditlfp</span>
                </a>
                <span class="text-slate-600">&</span>
                <a href="https://github.com/syafi-M"
                    class="group flex items-center gap-1.5 px-2 py-1 rounded-md hover:bg-white/5 transition-all duration-300">
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                    </svg>
                    <span class="text-slate-400 group-hover:text-white transition-colors font-medium">Syafi-M</span>
                </a>
            </div>
        </div>
    </div>
</footer>
