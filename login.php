<!DOCTYPE html>
<html>
<head>
    <title>Contoh Firebase SMS Login/Register</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <link href="intl/css/intlTelInput.css" rel="stylesheet" />
    <script src="intl/js/intlTelInput.js"></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
</head>
<body>

    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase-app.js"></script>

    <!-- TODO: Add SDKs for Firebase products that you want to use
         https://firebase.google.com/docs/web/setup#available-libraries -->
    <!-- <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase-analytics.js"></script> -->
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase-auth.js"></script>

    <script>
        // Your web app's Firebase configuration
        var firebaseConfig = {
            apiKey: "AIzaSyBOdH1PsuBYaeZ766gQQVAoJqWmnvGG1G0",
            authDomain: "test-34771.firebaseapp.com",
            databaseURL: "https://test-34771.firebaseio.com",
            projectId: "test-34771",
            storageBucket: "test-34771.appspot.com",
            messagingSenderId: "99369829382",
            appId: "1:99369829382:web:65b8d695315ac0390f6f87"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
    </script>
    <!-- FIREBASE END -->

    <script type="text/javascript">
        $(document).ready(function(){
            $(".form-group-step-1").show();
            $(".form-group-step-2").hide();
            $(".form-group-step-3").hide();
        });
    </script>

    <h1>Contoh ini adalah contoh register</h1>

    <form id="form" name="form" action="loginpost.php" method="post">
        <div>
            <p>Coba login/register dengan no.telp: '+16505551234' dan kode konfirmasi '123456'</p>
            <button onclick="navigator.clipboard.writeText('+16505551234');">copy no.telp '+16505551234'</button>
            <button onclick="navigator.clipboard.writeText('+123456');">copy kode konfirmasi '123456'</button>
        </div>
        <div class="form-group has-feedback form-group-step-1">
            <label for="txtUserID">Nomor Telepon (HP), akan menjadi username</label>
            <input type="text" name="txtUserID" id="txtUserID" class="form-control" placeholder="Username" value="+16505551234" required>
            <!-- INTL Script -->
            <script src="./dist/intl/js/intlTelInput.js"></script>
            <script>
                $(document).ready(function(){
                    var iti = window.intlTelInput(document.querySelector("#txtUserID"), {
                        // allowDropdown: false,
                        // autoHideDialCode: false,
                        // autoPlaceholder: "off",
                        // dropdownContainer: document.body,
                        // excludeCountries: ["us"],
                        // formatOnDisplay: false,
                        // geoIpLookup: function(callback) {
                        //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        //     var countryCode = (resp && resp.country) ? resp.country : "";
                        //     callback(countryCode);
                        //   });
                        // },
                        // hiddenInput: "full_number",
                        initialCountry: "id",
                        // localizedCountries: { 'de': 'Deutschland' },
                        // nationalMode: false,
                        // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                        // placeholderNumberType: "MOBILE",
                        preferredCountries: ['id', 'us', 'au', 'sg'],
                        separateDialCode: true,
                        utilsScript: "./intl/js/utils.js",
                    });
                    window.iti = iti;
                });
            </script>
        </div>

        <div class="form-group has-feedback form-group-step-1">
            <div id="recaptcha-container"></div>
            <div id="sign-in-button"></div>
            <script type="text/javascript">
                $(document).ready(function(){
                    // firebase.auth().settings.appVerificationDisabledForTesting = true; // comment ini apabila tidak menggunakan nomor telp khusus testing milik firebase, efeknya untuk meminta user di-verifikasi rechapta dulu sebelum bisa mengirim sms
                    firebase.auth().languageCode = 'id'; // set language to indonesian
                    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('sign-in-button', {
                        size: 'invisible',
                        callback: function(response) {
                            // reCAPTCHA solved, allow signInWithPhoneNumber.
                            onSignInSubmit();
                        }
                    });
                    //window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container');
                });
            </script>
            <div class="text-center form-group has-feedback" id="firebase_container">
                <input type="button" class="btn btn-primary" id="btn_send_firebase" value="Kirim Kode Konfirmasi">
            </div>
            <script type="text/javascript">
                $(document).ready(function(){
                    $('#btn_send_firebase').on('click',function(event){
                        event.preventDefault();
                        var phoneNumber=iti.getNumber();
                        $("#txtUserID").val(phoneNumber);

                        //$.post("function/ajax_function.php?fungsi=checknotelpbelumpakai&phone="+encodeURI(phoneNumber)).done(function(data){
                        $.post("ajaxcheckuserexist.php", {phone: encodeURI(phoneNumber) } ).done(function(data){
                            if(data == "1") {
                                firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier).then(function (confirmationResult) {
                                    // SMS sent. Prompt user to type the code from the message, then sign the
                                    // user in with confirmationResult.confirm(code).
                                    window.confirmationResult = confirmationResult;
                                    $(".form-group-step-2").show();
                                    $('#btn_send_firebase').prop("disabled", true);
                                    alert("SMS dengan kode konfirmasi terkirim ke "+phoneNumber);
                                }).catch(function (error) {
                                    // Error; SMS not sent
                                    // ...
                                    console.log(error);
                                    alert("Error; SMS tidak dapat terkirim");
                                    recaptchaVerifier.reset(window.recaptchaWidgetId);
                                    // Or, if you haven't stored the widget ID:
                                    //window.recaptchaVerifier.render().then(function(widgetId) {
                                    //  recaptchaVerifier.reset(widgetId);
                                    //});
                                    // ...
                                });
                            }
                            else
                            {
                                console.log(data);
                                alert("Nomor telepon tersebut sudah digunakan")
                            }
                        });
                    });

                    $('#btn_verify').on('click',function(event){
                        event.preventDefault();
                        var code = $("#txtVerification").val();
                        window.confirmationResult.confirm(code).then(function (result) {
                            // User signed in successfully.
                            var user = result.user;
                            console.log("User signed in successfully");
                            //console.log(user);
                            window.userr = user;

                            user.getIdToken().then(function(idToken){
                                //console.log(idToken);
                                // $("#input_backendauth_uid").val(user.uid);
                                // $("#input_backendauth_phone").val(user.phoneNumber);
                                // $("#input_backendauth_idtoken").val(idToken);
                                //$("#form_backendauth").submit();
                                $.post("ajaxcheckidtoken.php", {uid : user.uid, idtoken:idToken, phone : user.phoneNumber}).done(function(data){
                                    if(data=="1")
                                    {
                                        $('#btn_verify').prop("disabled", true);
                                        alert("Kode konfirmasi berhasil diverifikasi");
                                        $("#idtoken").val(idToken);
                                        $("#phone").val(user.phoneNumber);
                                        $("#uid").val(user.uid);
                                        $(".form-group-step-3").show();
                                    } 
                                    else 
                                    {
                                        alert(data);
                                    }
                                });

                            });
                        }).catch(function (error) {
                            // User couldn't sign in (bad verification code?)
                            // ...
                            alert("User couldn't verified (bad verification code?)");
                        });
                    });
                });
            </script>
        </div>

        <div class="form-group has-feedback form-group-step-2">
            <label for="txtVerification">Kode Konfirmasi</label>
            <input type="text" name="txtVerification" id="txtVerification" class="form-control" placeholder="Kode Konfirmasi" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <input type="hidden" name="idtoken" id="idtoken" value="">
            <input type="hidden" name="phone" id="phone" value="">
            <input type="hidden" name="uid" id="uid" value="">
        </div>
        <div class="form-group has-feedback text-center form-group-step-2">
            <input type="button" class="btn btn-primary" id="btn_verify" value="Konfirmasi Kode">
        </div>

        <div class="form-group has-feedback form-group-step-3">
            <label for="txtNama">Nama Lengkap (min. 6 karakter)</label>
            <input type="text" name="txtNama" id="txtNama" class="form-control" placeholder="Nama Lengkap" required>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback form-group-step-3">
            <label for="txtPassword">Password (min. 8 karakter)</label>
            <input type="password" name="txtPassword" id="txtPassword" class="form-control" placeholder="Password" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback form-group-step-3">
            <label for="txtPasswordConfirm">Ketik Ulang Password Anda</label>
            <input type="password" name="txtPasswordConfirm" id="txtPasswordConfirm" class="form-control" placeholder="Password Confirmation" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row form-group-step-3">
            <div class="col-xs-4">
                <button type="submit" class="button-login btn btn-primary btn-block btn-flat">Register</button>
            </div>
        </div>
    </form>

</body>
</html>
