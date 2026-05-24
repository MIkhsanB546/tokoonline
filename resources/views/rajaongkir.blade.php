<!DOCTYPE html>
<html>

<head>
    <title>Cek Ongkir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <form id="ongkirForm">

        <select name="province" id="province">
            <option value="">Pilih Provinsi</option>
        </select>

        <select name="city" id="city">
            <option value="">Pilih Kota</option>
        </select>

        <input type="number" name="weight" id="weight" placeholder="Berat (gram)">

        <select name="courier" id="courier">
            <option value="">Pilih Kurir</option>
            <option value="jne">JNE</option>
            <option value="tiki">TIKI</option>
            <option value="pos">POS Indonesia</option>
        </select>

        <button type="submit">Cek Ongkir</button>

    </form>

    <hr>

    <div id="result"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =========================
            // GET PROVINCES
            // =========================

            fetch('/provinces')
                .then(response => response.json())
                .then(data => {

                    console.log('Province:', data);

                    let provinceSelect =
                        document.getElementById('province');

                    provinceSelect.innerHTML =
                        '<option value="">Pilih Provinsi</option>';

                    data.forEach(province => {

                        let option =
                            document.createElement('option');

                        option.value = province.id;
                        option.textContent = province.name;

                        provinceSelect.appendChild(option);

                    });

                })
                .catch(error => {
                    console.error(error);
                });

            // =========================
            // GET CITIES
            // =========================

            document.getElementById('province')
                .addEventListener('change', function() {

                    let provinceId = this.value;

                    fetch(`/cities/${provinceId}`)
                        .then(response => response.json())
                        .then(data => {

                            console.log('Cities:', data);

                            let citySelect =
                                document.getElementById('city');

                            citySelect.innerHTML =
                                '<option value="">Pilih Kota</option>';

                            data.forEach(city => {

                                let option =
                                    document.createElement('option');

                                option.value = city.id;
                                option.textContent = city.name;

                                citySelect.appendChild(option);

                            });

                        })
                        .catch(error => {
                            console.error(error);
                        });

                });

            // =========================
            // CHECK ONGKIR
            // =========================

            document.getElementById('ongkirForm')
                .addEventListener('submit', function(event) {

                    event.preventDefault();

                    let origin = 649; // contoh ID kota asal
                    let destination =
                        document.getElementById('city').value;

                    let weight =
                        document.getElementById('weight').value;

                    let courier =
                        document.getElementById('courier').value;

                    let formData = new FormData();

                    formData.append('origin', origin);
                    formData.append('destination', destination);
                    formData.append('weight', weight);
                    formData.append('courier', courier);

                    fetch('/cost', {

                            method: 'POST',

                            headers: {
                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },

                            body: formData

                        })

                        .then(response => response.json())

                        .then(data => {

                            console.log('Cost:', data);

                            let resultDiv =
                                document.getElementById('result');

                            resultDiv.innerHTML = '';

                            data.forEach(cost => {

                                let div =
                                    document.createElement('div');

                                div.innerHTML = `
                                    <p>
                                        <strong>${cost.service}</strong><br>
                                        ${cost.description}<br>
                                        Rp ${cost.cost}<br>
                                        Estimasi: ${cost.etd} hari
                                    </p>
                                    <hr>
                                `;

                                resultDiv.appendChild(div);

                            });

                        })

                        .catch(error => {
                            console.error(error);
                        });

                });

        });
    </script>

</body>

</html>
