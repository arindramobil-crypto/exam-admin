<?php
/**
 * CBT Concurrency & Stress Testing Tool
 * SMK Negeri 1 Kemlagi
 *
 * Menguji ketahanan server (Exam-Admin & Moodle) terhadap lonjakan akses bersamaan (concurrency)
 * saat ujian serentak, mengukur throughput (RPS), latensi respon, serta mendeteksi error 502/504.
 *
 * Cara Penggunaan:
 *   php stress_test_cbt.php --url=https://exam.smkn1kemlagi.sch.id --concurrency=50 --requests=200
 *   php stress_test_cbt.php --url=https://moodle.smkn1kemlagi.sch.id --concurrency=30 --requests=100
 */

if (php_sapi_name() !== 'cli') {
    die("Alat pengujian ini hanya dapat dijalankan melalui terminal CLI: php stress_test_cbt.php\n");
}

// ─── Parsing Argumen CLI ──────────────────────────────────────────────────────
$options = getopt('', ['url:', 'concurrency::', 'requests::', 'timeout::', 'help::']);

if (isset($options['help'])) {
    echo "\n=== CBT CONCURRENCY STRESS TESTER ===\n";
    echo "Penggunaan:\n";
    echo "  php stress_test_cbt.php --url=<URL_TARGET> [opsi]\n\n";
    echo "Opsi:\n";
    echo "  --url          URL target yang akan diuji (contoh: https://exam.smkn1kemlagi.sch.id)\n";
    echo "  --concurrency  Jumlah koneksi simultan bersamaan (default: 30)\n";
    echo "  --requests     Total jumlah request yang akan dikirim (default: 100)\n";
    echo "  --timeout      Batas waktu per request dalam detik (default: 15)\n\n";
    exit(0);
}

$targetUrl   = $options['url'] ?? 'http://127.0.0.1/exam-admin';
$concurrency = isset($options['concurrency']) ? (int)$options['concurrency'] : 30;
$totalReqs   = isset($options['requests']) ? (int)$options['requests'] : 100;
$timeoutSec  = isset($options['timeout']) ? (int)$options['timeout'] : 15;

if ($concurrency <= 0) $concurrency = 10;
if ($totalReqs < $concurrency) $totalReqs = $concurrency;

echo "\n" . str_repeat('=', 65) . "\n";
echo "   🚀 CBT SERVER CONCURRENCY STRESS TESTER — SMKN 1 KEMLAGI\n";
echo str_repeat('=', 65) . "\n";
echo " Target URL   : {$targetUrl}\n";
echo " Concurrency  : {$concurrency} koneksi simultan\n";
echo " Total Request: {$totalReqs} permintaan\n";
echo " Timeout      : {$timeoutSec} detik per request\n";
echo str_repeat('-', 65) . "\n";
echo " Menyiapkan pengujian beban... Harap tunggu.\n\n";

// ─── Eksekusi Multi-Curl Non-Blocking ─────────────────────────────────────────
$mh = curl_multi_init();
$handles = [];
$latencies = [];
$statusCodes = [];
$errorCount = 0;
$completed = 0;

$startTime = microtime(true);
$dispatched = 0;

function createHandle($url, $timeout) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => $timeout,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT      => 'CBT-StressTester/1.0 (SMKN1Kemlagi)',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
    ]);
    return $ch;
}

// Isi slot awal multi-curl
while ($dispatched < $concurrency && $dispatched < $totalReqs) {
    $ch = createHandle($targetUrl, $timeoutSec);
    curl_multi_add_handle($mh, $ch);
    $handles[(int)$ch] = [
        'handle' => $ch,
        'start'  => microtime(true),
    ];
    $dispatched++;
}

// Loop eksekusi polling
$running = null;
do {
    curl_multi_exec($mh, $running);

    // Ambil info request yang sudah selesai
    while ($info = curl_multi_info_read($mh)) {
        $ch = $info['handle'];
        $id = (int)$ch;

        if (isset($handles[$id])) {
            $duration = (microtime(true) - $handles[$id]['start']) * 1000; // milidetik
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);

            $latencies[] = $duration;
            $statusCodes[$httpCode] = ($statusCodes[$httpCode] ?? 0) + 1;

            if ($httpCode >= 200 && $httpCode < 400) {
                // Success
            } else {
                $errorCount++;
            }

            $completed++;
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
            unset($handles[$id]);

            // Tampilkan progress bar
            $pct = round(($completed / $totalReqs) * 100);
            $barWidth = 25;
            $filled = round(($pct / 100) * $barWidth);
            $bar = str_repeat('█', $filled) . str_repeat('░', $barWidth - $filled);
            $avgCurrent = round(array_sum($latencies) / count($latencies));

            printf("\r [%s] %3d%% (%d/%d) | 200 OK: %d | Err: %d | Avg: %d ms",
                $bar, $pct, $completed, $totalReqs,
                ($statusCodes[200] ?? 0), $errorCount, $avgCurrent
            );

            // Tambahkan request baru ke antrean jika masih ada
            if ($dispatched < $totalReqs) {
                $newCh = createHandle($targetUrl, $timeoutSec);
                curl_multi_add_handle($mh, $newCh);
                $handles[(int)$newCh] = [
                    'handle' => $newCh,
                    'start'  => microtime(true),
                ];
                $dispatched++;
            }
        }
    }

    if ($running > 0) {
        curl_multi_select($mh, 0.05);
    }
} while ($running > 0 || !empty($handles));

curl_multi_close($mh);
$totalDuration = microtime(true) - $startTime;

echo "\n\n" . str_repeat('=', 65) . "\n";
echo "   📊 HASIL ANALISIS BEBAN & KINERJA SERVER (BENCHMARK)\n";
echo str_repeat('=', 65) . "\n";

// Kalkulasi statistik
sort($latencies);
$totalCount = count($latencies);
$minLat = round($latencies[0] ?? 0, 1);
$maxLat = round(end($latencies) ?? 0, 1);
$avgLat = round(array_sum($latencies) / max(1, $totalCount), 1);
$p50    = round($latencies[(int)floor($totalCount * 0.50)] ?? 0, 1);
$p90    = round($latencies[(int)floor($totalCount * 0.90)] ?? 0, 1);
$p95    = round($latencies[(int)floor($totalCount * 0.95)] ?? 0, 1);
$p99    = round($latencies[(int)floor($totalCount * 0.99)] ?? 0, 1);
$rps    = round($totalCount / max(0.001, $totalDuration), 2);
$successRate = round((($totalCount - $errorCount) / max(1, $totalCount)) * 100, 1);

printf(" Waktu Total Pengujian : %.2f detik\n", $totalDuration);
printf(" Throughput (RPS)      : %.2f requests/detik\n", $rps);
printf(" Tingkat Keberhasilan  : %.1f%% (%d sukses, %d gagal)\n", $successRate, ($totalCount - $errorCount), $errorCount);
echo str_repeat('-', 65) . "\n";
echo " Rincian Status HTTP:\n";
foreach ($statusCodes as $code => $count) {
    $codeDesc = match($code) {
        200 => 'OK (Normal)',
        301, 302 => 'Redirect',
        500 => 'Internal Server Error (PHP Crash/OOM)',
        502 => 'Bad Gateway (PHP-FPM Worker Kehabisan Slot/Overload)',
        504 => 'Gateway Timeout (Query Database Macet/Lock)',
        0   => 'Connection Refused / Timeout',
        default => 'HTTP ' . $code
    };
    printf("   • [%d] %-48s : %d kali\n", $code, $codeDesc, $count);
}

echo str_repeat('-', 65) . "\n";
echo " Latensi Waktu Respon Server (Response Time):\n";
printf("   • Tercepat (Min)     : %8.1f ms\n", $minLat);
printf("   • Rata-rata (Avg)    : %8.1f ms\n", $avgLat);
printf("   • Median (P50)       : %8.1f ms\n", $p50);
printf("   • 90%% Siswa (P90)   : %8.1f ms\n", $p90);
printf("   • 95%% Siswa (P95)   : %8.1f ms\n", $p95);
printf("   • Terlambat (Max)    : %8.1f ms\n", $maxLat);

echo str_repeat('=', 65) . "\n";
echo " 🩺 KESIMPULAN & REKOMENDASI KESIAPAN UJIAN:\n";

if ($errorCount === 0 && $avgLat < 500) {
    echo " 🟢 STATUS: SANGAT PRIMA (READY FOR CBT)\n";
    echo "    Server mampu melayani {$concurrency} akses simultan dengan sangat cepat dan stabil.\n";
    echo "    Siap digunakan untuk ujian serentak siswa.\n";
} elseif ($errorCount === 0 && $avgLat < 1500) {
    echo " 🟡 STATUS: CUKUP BAIK (STABIL)\n";
    echo "    Semua permintaan sukses tanpa error, namun terdapat sedikit kenaikan latensi.\n";
    echo "    Saran: Aktifkan Redis Cache di Moodle untuk menjaga respon tetap di bawah 500ms.\n";
} elseif (isset($statusCodes[502]) || isset($statusCodes[504])) {
    echo " 🔴 STATUS: BOTTLENECK / OVERLOAD DETECTED\n";
    echo "    Terjadi error 502/504 Bad Gateway! Hal ini menandakan worker PHP-FPM di aaPanel\n";
    echo "    kehabisan slot (exhausted) atau MySQL mengalami session locking.\n";
    echo "    Solusi:\n";
    echo "    1. Buka aaPanel > PHP > Service > Performance Tuning: naikkan max_children.\n";
    echo "    2. Ganti Moodle Session Handler ke Redis di config.php.\n";
    echo "    3. Atur MySQL: innodb_flush_log_at_trx_commit = 2 di my.cnf.\n";
} else {
    echo " 🟠 STATUS: PERLU OPTIMASI RINGAN\n";
    echo "    Terdapat latensi tinggi atau beberapa request gagal ({$errorCount} error).\n";
    echo "    Periksa log error di aaPanel (/www/wwwlogs/) untuk rincian error.\n";
}
echo str_repeat('=', 65) . "\n\n";
