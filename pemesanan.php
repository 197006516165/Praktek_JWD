<!doctype html>
<html lang="en">
<?php
    // Koneksi
    include 'koneksi.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Ambil data dari form
        $name = $_POST['name'];
        $gender = $_POST['gender'];
        $idNumber = $_POST['idNumber'];
        $roomType = $_POST['roomType'];
        $pricePerNight = intval(str_replace('.', '', $_POST['price']));
        $bookingDate = $_POST['bookingDate'];
        $duration = intval($_POST['duration']);
        $breakfast = isset($_POST['breakfast']) ? 1 : 0;

        // Hitung total harga
        $totalPrice = $pricePerNight * $duration;
        if ($duration > 3) {
            $totalPrice *= 0.9; // Diskon 10%
        }
        if ($breakfast) {
            $totalPrice += 80000 * $duration;
        }

        // Query SQL
        $stmt = $conn->prepare("INSERT INTO bookings (name, gender, id_number, room_type, price_per_night, booking_date, duration, breakfast, totalPrice) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssidi", $name, $gender, $idNumber, $roomType, $pricePerNight, $bookingDate, $duration, $breakfast, $totalPrice);

        // Eksekusi query
        if ($stmt->execute()) {
            echo "<script>alert('Booking successful!');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
        $conn->close();
    }
?>
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Pemesanan | Daluna Hotel</title>
  </head>
  <body>

    <!-- Navbar Buka -->

    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
      <div class="container">
        <a class="navbar-brand text-white" href="index.php">DALUNA HOTEL</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link text-white" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" aria-current="page" href="kamar.php">Kamar</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" aria-current="page" href="fasilitas.php">Fasilitas</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" aria-current="page" href="#">Log-In</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Navbar Tutup -->

    <!-- Content Buka -->

    <div class="form-container">
        <h2 class="container mb-5" style="font-family: Inter;">Daluna Hotel | Booking Form</h2>
        <form method="POST" action="">
            <div class="form-group container mb-3">
                <label for="name">Nama Pemesan:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group container mb-3">
                <label>Jenis Kelamin:</label>
                <input type="radio" id="male" name="gender" value="Pria" required> <label for="male">Pria</label>
                <input type="radio" id="female" name="gender" value="Wanita"> <label for="female">Wanita</label>
            </div>
            <div class="form-group container mb-3">
                <label for="idNumber">Nomor Identitas:</label>
                <input type="text" id="idNumber" name="idNumber" required>
            </div>
            <div class="form-group container mb-3">
                <label for="roomType">Tipe Kamar:</label>
                <select id="roomType" name="roomType" onchange="updatePrice()" required>
                    <option value="">Pilih Tipe Kamar</option>
                    <option value="Standard">Standard</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="Executif">Executif</option>
                </select>
            </div>
            <div class="form-group container mb-3">
                <label for="price">Harga per Malam (IDR):</label>
                <input type="text" id="price" name="price" readonly>
            </div>
            <div class="form-group container mb-3">
                <label for="bookingDate">Tanggal Pesan:</label>
                <input type="date" id="bookingDate" name="bookingDate" required>
            </div>
            <div class="form-group container mb-3">
                <label for="duration">Durasi Menginap (malam):</label>
                <input type="number" id="duration" name="duration" min="1" required>
            </div>
            <div class="form-group container mb-3">
                <label for="breakfast">Tambahkan Breakfast (80,000 IDR):</label>
                <input type="checkbox" id="breakfast" name="breakfast">
            </div>
            <div class="form-group container mb-3">
                <label id="totalPrice">Total Harga (IDR)</label>
                <input type="text" id="totalPrice" name="totalPrice" readonly>
            </div>

            <div class="form-group container mb-3">
                <button type="button" onclick="validateAndCalculateTotal()"class="btn btn-primary">Hitung Total Bayar</button>
                <button type="submit" class="btn btn-primary">Book Now</button>
            </div>
        </form>
        <div class="result container mb-3" id="result" style="display: none;"></div>
    </div>

    <!-- Penghitungan -->

    <script>

      // Validasi tanggal sebelumnya tidak bisa dipilih
        document.addEventListener('DOMContentLoaded', () => {
            const bookingDateInput = document.getElementById('bookingDate');
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-based
            const day = String(today.getDate()).padStart(2, '0');
            const minDate = `${year}-${month}-${day}`; // Format: YYYY-MM-DD

            bookingDateInput.setAttribute('min', minDate);
        });


        function updatePrice() {
            const roomType = document.getElementById('roomType').value;
            const priceField = document.getElementById('price');

            let pricePerNight = 0;

            switch (roomType) {
                case 'Standard':
                    pricePerNight = 200000;
                    break;
                case 'Deluxe':
                    pricePerNight = 250000;
                    break;
                case 'Executif':
                    pricePerNight = 300000;
                    break;
                default:
                    pricePerNight = 0;
            }

            priceField.value = pricePerNight.toLocaleString('id-ID');
        }

        function validateForm() {
            const noIdentitas = document.getElementById('no_identitas').value;
            const durasi = document.getElementById('durasi').value;
            const errorDiv = document.getElementById('error_message');
            errorDiv.innerHTML = ''; // Clear previous errors

            // Validasi Nomor Identitas
            if (!/^\d{16}$/.test(noIdentitas)) {
                errorDiv.innerHTML += '<p>Isian salah..data harus 16 digit</p>';
                return false;
            }

            // Validasi Durasi Menginap
            if (isNaN(durasi) || durasi <= 0) {
                errorDiv.innerHTML += '<p>Durasi menginap harus isi angka</p>';
                return false;
            }

            return true;
        }

        function validateAndCalculateTotal() {
            const idNumber = document.getElementById('idNumber').value;

            // Allert untuk nomer identitas

            if (idNumber.length < 16) {
                alert('Nomor identitas harus minimal 16 angka.');
                return;
            }

            calculateTotal();
        }

        function calculateTotal() {
            const name = document.getElementById('name').value;
            const gender = document.querySelector('input[name="gender"]:checked');
            const idNumber = document.getElementById('idNumber').value;
            const roomType = document.getElementById('roomType').value;
            const bookingDate = document.getElementById('bookingDate').value;
            const duration = parseInt(document.getElementById('duration').value);
            const pricePerNight = parseInt(document.getElementById('price').value.replace(/\./g, ''));
            const breakfast = document.getElementById('breakfast').checked;

            // Allert kolom tidak boleh kosong

            if (!name || !gender || !idNumber || !roomType || !bookingDate || !duration || isNaN(pricePerNight)) {
                alert('Harap lengkapi semua field!');
                return;
            }

            let totalPrice = pricePerNight * duration;

            if (duration > 3) {
                totalPrice *= 0.9; // Diskon 10%
            }

            if (breakfast) {
                totalPrice += 80000 * duration;
            }

            const resultDiv = document.getElementById('result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <h3>Detail Pemesanan</h3>
                <p><strong>Nama Pemesan:</strong> ${name}</p>
                <p><strong>Jenis Kelamin:</strong> ${gender.value}</p>
                <p><strong>Nomor Identitas:</strong> ${idNumber}</p>
                <p><strong>Tipe Kamar:</strong> ${roomType}</p>
                <p><strong>Harga per Malam:</strong> ${pricePerNight.toLocaleString('id-ID')} IDR</p>
                <p><strong>Tanggal Pesan:</strong> ${bookingDate}</p>
                <p><strong>Durasi Menginap:</strong> ${duration} malam</p>
                <p><strong>Breakfast:</strong> ${breakfast ? 'Ya' : 'Tidak'}</p>
                <p><strong>Total Bayar:</strong> ${totalPrice.toLocaleString('id-ID')} IDR</p>
            `;
        }
    </script >

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>