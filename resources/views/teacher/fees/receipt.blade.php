<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #0f1115;
            color: #e2e8f0;
            padding: 30px;
        }
        .receipt-card {
            max-width: 700px;
            margin: 0 auto;
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .receipt-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .receipt-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 12px;
            background: white;
            padding: 6px;
        }
        .receipt-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .receipt-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        .receipt-item {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 12px;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .receipt-item span {
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .receipt-item div {
            margin-top: 6px;
            font-size: 1rem;
            color: #e2e8f0;
        }
        .btn-download {
            margin-top: 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div id="receiptArea" class="receipt-card">
        <div class="receipt-header">
            @if($settings->logo_path)
                <img src="{{ asset('storage/' . $settings->logo_path) }}" class="receipt-logo" alt="Logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22%3E%3Crect fill=%22%23e2e8f0%22 width=%2260%22 height=%2260%22 rx=%2212%22/%3E%3Ctext x=%2230%22 y=%2235%22 text-anchor=%22middle%22 font-size=%2224%22 font-weight=%22bold%22 fill=%22%23111827%22%3EAD%3C/text%3E%3C/svg%3E'">
            @else
                <img src="{{ asset('images/logo.png') }}" class="receipt-logo" alt="Logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22%3E%3Crect fill=%22%23e2e8f0%22 width=%2260%22 height=%2260%22 rx=%2212%22/%3E%3Ctext x=%2230%22 y=%2235%22 text-anchor=%22middle%22 font-size=%2224%22 font-weight=%22bold%22 fill=%22%23111827%22%3EAD%3C/text%3E%3C/svg%3E'">
            @endif
            <div>
                <div class="receipt-title">{{ $settings->header_text ?? 'Alor Disha' }}</div>
                <div class="text-muted">Payment Receipt</div>
            </div>
        </div>

        <div class="receipt-row">
            <div class="receipt-item">
                <span>Student</span>
                <div>{{ $student->name }}</div>
            </div>
            <div class="receipt-item">
                <span>Phone</span>
                <div>{{ $student->phone }}</div>
            </div>
            <div class="receipt-item">
                <span>Branch</span>
                <div>{{ $branch?->name ?? 'N/A' }}</div>
            </div>
            <div class="receipt-item">
                <span>Unit</span>
                <div>{{ $combinedYearLabel ?? $payment->year_label }}</div>
            </div>
            <div class="receipt-item">
                <span>Month</span>
                <div>{{ $monthName }} {{ $payment->year }}</div>
            </div>
            <div class="receipt-item">
                <span>Amount</span>
                <div>₹{{ number_format($totalAmount ?? $payment->amount) }}</div>
            </div>
            <div class="receipt-item">
                <span>Receipt No</span>
                <div>{{ $payment->receipt_no }}</div>
            </div>
            <div class="receipt-item">
                <span>Paid At</span>
                <div>{{ $payment->paid_at?->format('d-m-Y') }}</div>
            </div>
        </div>
    </div>

    <div style="text-align: center;">
        <button class="btn-download" onclick="downloadPNG()">Download PNG</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadPNG() {
            const node = document.getElementById('receiptArea');
            html2canvas(node, {scale: 2}).then(canvas => {
                const link = document.createElement('a');
                const filename = '{{ $student->name }}_{{ $monthName }}_{{ now()->format("d-m-Y") }}.png';
                link.download = filename;
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                // Auto-close window after download starts
                setTimeout(() => {
                    window.close();
                }, 500);
            });
        }

        // Auto-download on page load
        window.onload = function() {
            // Hide the button since we're auto-downloading
            document.querySelector('.btn-download').style.display = 'none';
            downloadPNG();
        };
    </script>
</body>
</html>
