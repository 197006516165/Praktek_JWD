
    <div class="form-container text-center">
        <h2>Hotel Booking Form</h2>
        <form id="bookingForm">
            <div class="form-group">
                <label for="name">Nama Pemesan:</label>
                <input type="text" id="name" required>
            </div>
            <div class="form-group">
                <label for="gender">Jenis Kelamin:</label>
                <select id="gender" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Pria">Pria</option>
                    <option value="Wanita">Wanita</option>
                </select>
            </div>
            <div class="form-group">
                <label for="idNumber">Nomor Identitas:</label>
                <input type="text" id="idNumber" required>
            </div>
            <div class="form-group">
                <label for="roomType">Tipe Kamar:</label>
                <select id="roomType" required>
                    <option value="">Pilih Tipe Kamar</option>
                    <option value="Standard">Standard - 500,000 IDR</option>
                    <option value="Deluxe">Deluxe - 750,000 IDR</option>
                    <option value="Suite">Suite - 1,000,000 IDR</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bookingDate">Tanggal Pesan:</label>
                <input type="date" id="bookingDate" required>
            </div>
            <div class="form-group">
                <label for="duration">Durasi Menginap (malam):</label>
                <input type="number" id="duration" min="1" required>
            </div>
            <div class="form-group">
                <button type="button" onclick="calculateTotal()">Hitung Total Bayar</button>
            </div>
        </form>
        <div class="result" id="result" style="display: none;"></div>
    </div>

    <script>
        function calculateTotal() {
            const name = document.getElementById('name').value;
            const gender = document.getElementById('gender').value;
            const idNumber = document.getElementById('idNumber').value;
            const roomType = document.getElementById('roomType').value;
            const bookingDate = document.getElementById('bookingDate').value;
            const duration = parseInt(document.getElementById('duration').value);

            if (!name || !gender || !idNumber || !roomType || !bookingDate || !duration) {
                alert('Harap lengkapi semua field!');
                return;
            }

            let pricePerNight;

            switch (roomType) {
                case 'Standard':
                    pricePerNight = 500000;
                    break;
                case 'Deluxe':
                    pricePerNight = 750000;
                    break;
                case 'Suite':
                    pricePerNight = 1000000;
                    break;
                default:
                    alert('Tipe kamar tidak valid!');
                    return;
            }

            const totalPrice = pricePerNight * duration;

            const resultDiv = document.getElementById('result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <h3>Detail Pemesanan</h3>
                <p><strong>Nama Pemesan:</strong> ${name}</p>
                <p><strong>Jenis Kelamin:</strong> ${gender}</p>
                <p><strong>Nomor Identitas:</strong> ${idNumber}</p>
                <p><strong>Tipe Kamar:</strong> ${roomType}</p>
                <p><strong>Harga per Malam:</strong> ${pricePerNight.toLocaleString('id-ID')} IDR</p>
                <p><strong>Tanggal Pesan:</strong> ${bookingDate}</p>
                <p><strong>Durasi Menginap:</strong> ${duration} malam</p>
                <p><strong>Total Bayar:</strong> ${totalPrice.toLocaleString('id-ID')} IDR</p>
            `;
        }
    </script>