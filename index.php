<?php
// filepath: c:\laragon\www\bigscreenproject\index.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Page</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Modal -->
    <div class="modal fade" id="choosePageModal" tabindex="-1" aria-labelledby="choosePageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="choosePageModalLabel">Choose Your Destination</h5>
                </div>
                <div class="modal-body text-center">
                    <p>Please select the page you want to visit:</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="mascot_index.php" class="btn btn-primary">Mascot Projects</a> or
                        <a href="costume_index.php" class="btn btn-primary">Costume Projects</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Tampilkan modal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const choosePageModal = new bootstrap.Modal(document.getElementById('choosePageModal'), {
            backdrop: 'static', // Mencegah modal tertutup saat mengklik di luar
            keyboard: false // Mencegah modal tertutup dengan tombol Escape
        });
        choosePageModal.show();
    });
    </script>
</body>

</html>