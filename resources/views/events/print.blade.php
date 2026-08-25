<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - {{ $event->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #111;
            margin: 24px;
        }
        h1 { font-size: 18px; margin: 0 0 2px; }
        h2 { font-size: 13px; margin: 24px 0 8px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        p.subtitle { margin: 0 0 20px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        th, td { text-align: left; padding: 4px 6px; border-bottom: 1px solid #eee; }
        th { color: #666; font-weight: 600; }
        td.amount, th.amount { text-align: right; white-space: nowrap; }
        .summary { display: flex; gap: 24px; margin-bottom: 8px; }
        .summary div { flex: 1; }
        .summary span { display: block; color: #666; }
        .summary strong { font-size: 14px; }
        .empty { color: #999; padding: 8px 6px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>

<body onload="window.print()">
    <button class="no-print" onclick="window.print()">Cetak / Simpan PDF</button>

    <h1>Laporan Keuangan</h1>
    <p class="subtitle">{{ $event->name }} &middot; dicetak {{ now()->format('d M Y H:i') }}</p>

    <div class="summary">
        <div><span>Pemasukan</span><strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong></div>
        <div><span>Pengeluaran</span><strong>Rp {{ number_format($totalExpense, 0, ',', '.') }}</strong></div>
        <div><span>Pengeluaran Asli</span><strong>Rp {{ number_format($totalExpense + $totalItemDonation, 0, ',', '.') }}</strong></div>
        <div><span>Saldo</span><strong>Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</strong></div>
    </div>

    <h2>Rekap Sumbangan per Rumah</h2>
    <table>
        <thead><tr><th>Rumah</th><th class="amount">Total</th></tr></thead>
        <tbody>
            @forelse ($houseRecap as $item)
            <tr>
                <td>{{ $item->house->display_name ?? '-' }}</td>
                <td class="amount">Rp {{ number_format($item->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td class="empty" colspan="2">Belum ada sumbangan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Iuran Wajib ({{ $iuranTransactions->count() }})</h2>
    <table>
        <thead><tr><th>Rumah</th><th class="amount">Jumlah</th></tr></thead>
        <tbody>
            @forelse ($iuranTransactions as $tx)
            <tr>
                <td>{{ $tx->is_anonymous ? 'Orang Baik ' . ($anonymousNumbers[$tx->id] ?? '') : ($tx->house->display_name ?? $tx->donor_name ?? '-') }}</td>
                <td class="amount">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td class="empty" colspan="2">Belum ada iuran wajib.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Donasi Uang ({{ $donasiTransactions->count() }})</h2>
    <table>
        <thead><tr><th>Donatur</th><th class="amount">Jumlah</th></tr></thead>
        <tbody>
            @forelse ($donasiTransactions as $tx)
            <tr>
                <td>{{ $tx->is_anonymous ? 'Orang Baik ' . ($anonymousNumbers[$tx->id] ?? '') : ($tx->house->display_name ?? $tx->donor_name ?? '-') }}</td>
                <td class="amount">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td class="empty" colspan="2">Belum ada donasi uang.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Donasi Barang ({{ $itemDonations->count() }})</h2>
    <table>
        <thead><tr><th>Donatur</th><th>Barang</th><th class="amount">Jumlah</th><th class="amount">Nilai</th></tr></thead>
        <tbody>
            @forelse ($itemDonations as $donation)
            <tr>
                <td>{{ $donation->is_anonymous ? 'Orang Baik ' . ($anonymousItemNumbers[$donation->id] ?? '') : ($donation->house->display_name ?? $donation->donor_name ?? '-') }}</td>
                <td>{{ $donation->item_name }}</td>
                <td class="amount">{{ $donation->quantity }} {{ $donation->unit }}</td>
                <td class="amount">{{ $donation->price ? 'Rp ' . number_format($donation->price * $donation->quantity, 0, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr><td class="empty" colspan="4">Belum ada sumbangan barang.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Pengeluaran ({{ $expenseTransactions->count() }})</h2>
    <table>
        <thead><tr><th>Donatur</th><th>Deskripsi</th><th class="amount">Jumlah</th></tr></thead>
        <tbody>
            @forelse ($expenseTransactions as $tx)
            <tr>
                <td>{{ $tx->is_anonymous ? 'Orang Baik ' . ($anonymousNumbers[$tx->id] ?? '') : ($tx->donor_name ?? $tx->house->display_name ?? '-') }}</td>
                <td>{{ $tx->description }}</td>
                <td class="amount">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td class="empty" colspan="3">Belum ada pengeluaran.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Rincian Hutang ({{ $debts->count() }})</h2>
    <table>
        <thead><tr><th>Kreditur</th><th>Deskripsi</th><th class="amount">Jumlah</th></tr></thead>
        <tbody>
            @forelse ($debts as $debt)
            <tr>
                <td>{{ $debt->creditor_name }}</td>
                <td>{{ $debt->description ?? '-' }}</td>
                <td class="amount">Rp {{ number_format($debt->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td class="empty" colspan="3">Belum ada hutang.</td></tr>
            @endforelse
        </tbody>
        @if ($debts->count() > 0)
        <tfoot>
            <tr><td colspan="2"><strong>Total Hutang</strong></td><td class="amount"><strong>Rp {{ number_format($totalDebt, 0, ',', '.') }}</strong></td></tr>
        </tfoot>
        @endif
    </table>

    <div style="margin-top: 48px; text-align: right;">
        <p>Tertanda,<br>Ketua PHBN</p>
        <p style="margin-top: 56px; font-weight: 600; text-decoration: underline;">Abdul Rozak</p>
    </div>
</body>

</html>
