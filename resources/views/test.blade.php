<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Test</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<style>
    .center-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
</style>
<body>

<div class="row center-container">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-body text-center">
                <a href="javascript:void(0);" onClick="openCamera()" class="btn btn-primary">Open Camera</a>
                <p id="result" class="mt-3"></p>
            </div>
        </div>
    </div>
    <div class="col-sm-3"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="cameraModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Camera</h5>
            </div>
            <div class="modal-body text-center">
                <input type="file" id="imageInput" accept="image/*">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="ocrClick" class="btn btn-primary">OCR</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let stream;

    function openCamera() {
        $('#cameraModal').modal('show');
        // const video = document.getElementById('video');
        //
        // // Akses kamera melalui browser
        // navigator.mediaDevices.getUserMedia({video: true})
        //     .then(function (mediaStream) {
        //         stream = mediaStream;
        //         video.srcObject = stream;
        //     })
        //     .catch(function (err) {
        //         console.log("Terjadi masalah: " + err);
        //     });
    }

    function closeCamera() {
        // if (stream) {
        //     stream.getTracks().forEach(function (track) {
        //         track.stop();
        //     });
        //     const video = document.getElementById('video');
        //     video.srcObject = null; // Menghapus referensi stream dari video
        // }
        $('#cameraModal').modal('hide');
    }


    $('#ocrClick').click(function () {
        const fileInput = document.getElementById('imageInput');
        const file = fileInput.files[0]; // Ambil file pertama dari input

        if (!file) {
            alert('Please select an image first!');
            return;
        }
        showResult(file);
        // Mengosongkan input file
        fileInput.value = '';
    });

    function showResult(file) {
        Swal.fire({
            title: 'Please wait...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();

                const formData = new FormData();
                formData.append('image', file);

                $.ajax({
                    url: "{{ route('ocr.process') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    contentType: false, // Penting untuk menghindari jQuery menetapkan Content-Type secara otomatis
                    processData: false, // Penting untuk menghindari jQuery memproses data
                    success: function (data) {
                        $('#result').text(data.text);
                        Swal.close();
                        closeCamera();
                    },
                    error: function (data) {
                        console.log('Error:', data.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        })
                        Swal.close();
                        closeCamera();
                    }
                });
            }
        })
    }
</script>

</body>
</html>
