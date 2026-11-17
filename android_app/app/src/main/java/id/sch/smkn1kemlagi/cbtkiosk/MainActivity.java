package id.sch.smkn1kemlagi.cbtkiosk;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.graphics.Bitmap;
import android.os.Build;
import android.os.Bundle;
import android.text.InputType;
import android.view.KeyEvent;
import android.view.View;
import android.view.WindowManager;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ProgressBar;
import android.widget.Toast;

/**
 * CBT Kiosk - Main Activity for SMKN 1 Kemlagi
 *
 * Mengunci layar perangkat Android (Screen Pinning / Kiosk Mode),
 * mematikan screenshot (FLAG_SECURE), dan menyematkan User-Agent khusus CBT.
 */
public class MainActivity extends Activity {

    // URL Ujian CBT Sekolah
    private static final String DEFAULT_EXAM_URL = "https://exam.smkn1kemlagi.sch.id";
    // PIN Pengawas untuk keluar dari aplikasi kiosk
    private static final String DEFAULT_PROCTOR_PIN = "PROKTOR123";

    private WebView webView;
    private ProgressBar progressBar;
    private int backPressCount = 0;
    private long lastBackPressTime = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // 1. BLOKIR SCREENSHOT & SCREEN RECORDING (FLAG_SECURE)
        getWindow().setFlags(
            WindowManager.LayoutParams.FLAG_SECURE,
            WindowManager.LayoutParams.FLAG_SECURE
        );

        // 2. Layar Penuh (Immersive Fullscreen)
        hideSystemUI();

        // 3. Bangun Tampilan WebView
        FrameLayout rootLayout = new FrameLayout(this);
        rootLayout.setBackgroundColor(0xFF0F172A);

        webView = new WebView(this);
        FrameLayout.LayoutParams webParams = new FrameLayout.LayoutParams(
            FrameLayout.LayoutParams.MATCH_PARENT,
            FrameLayout.LayoutParams.MATCH_PARENT
        );
        rootLayout.addView(webView, webParams);

        progressBar = new ProgressBar(this, null, android.R.attr.progressBarStyleHorizontal);
        progressBar.setMax(100);
        progressBar.setVisibility(View.GONE);
        FrameLayout.LayoutParams progressParams = new FrameLayout.LayoutParams(
            FrameLayout.LayoutParams.MATCH_PARENT, 8
        );
        rootLayout.addView(progressBar, progressParams);

        setContentView(rootLayout);

        // 4. Konfigurasi Keamanan WebSettings
        configureWebSettings();

        // 5. Muat Halaman CBT
        webView.loadUrl(DEFAULT_EXAM_URL);

        // 6. Kunci Aplikasi (Screen Pinning / Lock Task) jika didukung
        tryLockTask();
    }

    private void configureWebSettings() {
        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setDatabaseEnabled(true);
        settings.setAllowFileAccess(false);
        settings.setAllowContentAccess(false);
        settings.setSupportZoom(true);
        settings.setBuiltInZoomControls(true);
        settings.setDisplayZoomControls(false);

        // Sematkan identifikasi User-Agent Kiosk SMKN 1 Kemlagi
        String defaultUA = settings.getUserAgentString();
        settings.setUserAgentString(defaultUA + " CBT-Kiosk-Kemlagi/1.0");

        webView.setWebChromeClient(new WebChromeClient() {
            @Override
            public void onProgressChanged(WebView view, int newProgress) {
                if (newProgress < 100) {
                    progressBar.setVisibility(View.VISIBLE);
                    progressBar.setProgress(newProgress);
                } else {
                    progressBar.setVisibility(View.GONE);
                }
            }
        });

        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                return false; // Tetap di dalam WebView
            }

            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                hideSystemUI();
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                hideSystemUI();
            }
        });
    }

    private void tryLockTask() {
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                startLockTask();
            }
        } catch (Exception e) {
            // Ignore jika izin lock task belum diset oleh device admin
        }
    }

    private void hideSystemUI() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
            getWindow().getDecorView().setSystemUiVisibility(
                View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY
                | View.SYSTEM_UI_FLAG_FULLSCREEN
                | View.SYSTEM_UI_FLAG_HIDE_NAVIGATION
                | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN
                | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                | View.SYSTEM_UI_FLAG_LAYOUT_STABLE
            );
        }
    }

    @Override
    public void onWindowFocusChanged(boolean hasFocus) {
        super.onWindowFocusChanged(hasFocus);
        if (hasFocus) {
            hideSystemUI();
        }
    }

    // Tangani Tombol Kembali (Back Button)
    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
            return;
        }

        // Tekan 5x berturut-turut untuk membuka menu keluar Pengawas
        long currentTime = System.currentTimeMillis();
        if (currentTime - lastBackPressTime < 1500) {
            backPressCount++;
        } else {
            backPressCount = 1;
        }
        lastBackPressTime = currentTime;

        if (backPressCount >= 5) {
            showProctorExitDialog();
            backPressCount = 0;
        } else {
            Toast.makeText(this, "Aplikasi Kiosk Ujian Aktif. Hubungi Pengawas untuk keluar.", Toast.LENGTH_SHORT).show();
        }
    }

    // Dialog Verifikasi PIN Pengawas untuk Keluar Aplikasi
    private void showProctorExitDialog() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("🔒 Keluar Aplikasi (Khusus Pengawas)");
        builder.setMessage("Masukkan PIN Pengawas Ruangan untuk membuka kunci dan keluar:");

        final EditText pinInput = new EditText(this);
        pinInput.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_VARIATION_PASSWORD);
        pinInput.setHint("PIN Pengawas");
        builder.setView(pinInput);

        builder.setPositiveButton("Buka Kunci & Keluar", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                String inputPin = pinInput.getText().toString().trim().toUpperCase();
                if (DEFAULT_PROCTOR_PIN.equals(inputPin)) {
                    try {
                        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                            stopLockTask();
                        }
                    } catch (Exception ignored) {}
                    finish();
                } else {
                    Toast.makeText(MainActivity.this, "PIN Pengawas Salah!", Toast.LENGTH_SHORT).show();
                }
            }
        });

        builder.setNegativeButton("Batal", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
                hideSystemUI();
            }
        });

        builder.show();
    }
}
