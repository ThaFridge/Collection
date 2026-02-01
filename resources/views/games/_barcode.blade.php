<div class="form-group">
    <label class="form-label">Barcode scanner</label>
    <div style="display:flex;gap:0.5rem;">
        <button type="button" id="start-scanner" class="btn btn-secondary btn-sm" onclick="startScanner()">Camera scanner</button>
        <span id="scanner-status" style="font-size:0.8rem;color:var(--text-muted);align-self:center;"></span>
    </div>
    <div id="scanner-container" style="display:none;margin-top:0.5rem;max-width:400px;">
        <video id="scanner-video" style="width:100%;border-radius:8px;border:2px solid var(--border);"></video>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
var html5QrCode = null;

function startScanner() {
    var container = document.getElementById('scanner-container');
    var status = document.getElementById('scanner-status');
    var btn = document.getElementById('start-scanner');

    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(function() {
            container.style.display = 'none';
            btn.textContent = 'Camera scanner';
            status.textContent = '';
        });
        return;
    }

    container.style.display = 'block';
    btn.textContent = 'Stop scanner';
    status.textContent = 'Camera starten...';

    html5QrCode = new Html5Qrcode('scanner-container');

    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 250, height: 100 }, formatsToSupport: [
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.CODE_128,
        ]},
        function(decodedText) {
            // Barcode found
            var barcodeField = document.querySelector('[name="barcode"]');
            if (barcodeField) barcodeField.value = decodedText;
            status.textContent = 'Gevonden: ' + decodedText;
            status.style.color = 'var(--success)';

            // Stop scanner after success
            html5QrCode.stop().then(function() {
                container.style.display = 'none';
                btn.textContent = 'Camera scanner';
            });
        },
        function(errorMessage) {
            // Scanning...
        }
    ).catch(function(err) {
        status.textContent = 'Camera error: ' + err;
        status.style.color = 'var(--accent)';
        container.style.display = 'none';
        btn.textContent = 'Camera scanner';
    });
}
</script>
@endpush
