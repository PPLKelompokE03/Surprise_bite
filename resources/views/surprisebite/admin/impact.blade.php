<x-layouts.admin title="Impact Tracker" active="impact">
    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            <p class="text-sm font-semibold text-[#00a63e]">Dampak lingkungan</p>
            <h2 class="mt-1 text-2xl font-black text-slate-900">Ringkasan untuk admin</h2>
            <p class="mt-1 text-sm text-slate-600">Metrik dari mystery box terjual (settlement, capture, COD terkonfirmasi).</p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl bg-gradient-to-br from-[#00a63e] to-[#059669] p-6 text-white shadow-lg shadow-emerald-900/25">
                    <div class="text-sm font-semibold text-white/90">Total Meals Saved</div>
                    <div class="mt-2 text-3xl font-black tabular-nums">{{ number_format($mealsSaved) }}</div>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-[#ea580c] to-[#f54900] p-6 text-white shadow-lg shadow-orange-900/25">
                    <div class="text-sm font-semibold text-white/90">Food Waste Reduced</div>
                    <div class="mt-2 text-3xl font-black tabular-nums">
                        {{ number_format($wasteDisplay['value'], $wasteDisplay['decimals'], ',', '.') }}
                        <span class="text-xl font-bold text-white/90">{{ $wasteDisplay['unit'] === 'kg' ? 'kg' : 'ton' }}</span>
                    </div>
                    <p class="mt-2 text-xs text-white/80">~2,2 kg dicegah per kotak (estimasi).</p>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-[#00a63e] to-[#059669] p-6 text-white shadow-lg shadow-emerald-900/25 sm:col-span-2 lg:col-span-1">
                    <div class="text-sm font-semibold text-white/90">Active Users</div>
                    <div class="mt-2 text-3xl font-black tabular-nums">{{ number_format($activeUsers) }}</div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
                <div class="text-sm font-black text-slate-900">Waste reduction</div>
                <div class="mt-6 rounded-xl border border-orange-100 bg-orange-50/80 p-5">
                    <div class="text-xs font-bold text-orange-900/80">Total limbah dicegah</div>
                    <div class="mt-1 text-2xl font-black text-orange-700 tabular-nums">
                        {{ number_format($wasteKg, $wasteKg < 10 ? 1 : 0, ',', '.') }} kg
                    </div>
                    <div class="mt-2 text-sm text-slate-600">
                        Setara {{ number_format($wasteTons, 3, ',', '.') }} ton — makanan yang tidak jatuh ke pembuangan sampah.
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
                <div class="text-sm font-black text-slate-900">Monthly Trend {{ $trendYear }}</div>
                <div class="mt-6 space-y-4">
                    @php
                        $maxMeals = max(1, max(array_column($monthlyTrend, 'meals')));
                    @endphp
                    @foreach ($monthlyTrend as $b)
                        @php
                            $wasteLabel = $b['waste_kg'] < 100
                                ? rtrim(rtrim(number_format($b['waste_kg'], 1, ',', ''), '0'), ',') . ' kg'
                                : rtrim(rtrim(number_format($b['waste_tons'], 2, ',', ''), '0'), ',') . ' ton';
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="font-bold text-slate-800">{{ $b['m'] }}</div>
                                <div class="text-slate-600">{{ number_format($b['meals']) }} meals <span class="text-slate-400">•</span> {{ $wasteLabel }} dicegah</div>
                            </div>
                            <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-600 to-orange-500" style="width: {{ (int) round(($b['meals'] / $maxMeals) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
