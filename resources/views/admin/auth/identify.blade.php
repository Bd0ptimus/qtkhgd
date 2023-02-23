@extends('layouts/fullLayoutMaster')

@section('content')
<section class="row flexbox-container">
  <div class="col-xl-8 offset-xl-2 d-flex justify-content-center">
      <div class="card bg-authentication rounded-0 mb-0">
          <div class="row m-0">
              <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                  <img src="{{ asset('images/pages/login.png') }}" alt="branding logo">
              </div>
              <div class="col-lg-6 col-12 p-0">
                  <div class="card rounded-0 mb-0 px-2">
                      <div class="card-header pb-1">
                          <div class="card-title">
                              <h4 class="mb-0">Login</h4>
                          </div>
                      </div>
                      <div id="recaptcha-container"></div>
                      <p class="px-2">Vui lòng chờ tới khi OTP được gửi tới SĐT bạn đã đăng ký với hệ thống. Nhập sai OTP 5 lần TK sẽ bị khoá.</p> 
                      <div class="card-content">
                          <div class="card-body pt-1">
                          <form>
                              <input required class="form-control" style="margin-top:10px" type="text" id="verificationCode" placeholder="Nhập mã OTP">
                              <button id="submitOTP" type="button" style="margin-top:10px; text-align:center;" class="btn btn-success" onclick="codeverify();">Đang gửi mã xác thực ... </button>
                          </form>
                          </div>
                      </div>
                      <div class="login-footer">
                        
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>
      </div>
    </div>

@endsection

@push('scripts')
    <!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>

<!-- Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#config-web-app -->

<script>
    // Your web app's Firebase configuration
    var firebaseConfig = {
      apiKey: "AIzaSyBzdLqEIexJmtS6UUpCwAd79DV1bWlfq1U",
      authDomain: "daugiacuoc.firebaseapp.com",
      databaseURL: "https://daugiacuoc.firebaseio.com",
      projectId: "daugiacuoc",
      storageBucket: "daugiacuoc.appspot.com",
      messagingSenderId: "291252940845",
      appId: "1:291252940845:web:50494da770b89f06fec8a4",
      measurementId: "G-R6KJBG5BFD"
  };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    firebase.auth().languageCode = 'vn';
</script>
<script>
  window.onload=function () {
    render();
    phoneAuth();
  };  
  function render() {
      window.recaptchaVerifier=new firebase.auth.RecaptchaVerifier('recaptcha-container', {
        'size': 'invisible',
        'callback': function(response) {
          // reCAPTCHA solved, allow signInWithPhoneNumber.
          console.log('bypass capchar');        
        }
      });
      recaptchaVerifier.render();
  }
  function phoneAuth() {
      //get the number
      var number="+84{!! Admin::user()->phone_number !!}";
      firebase.auth().signInWithPhoneNumber(number,window.recaptchaVerifier).then(function (confirmationResult) {
          window.confirmationResult=confirmationResult;
          coderesult=confirmationResult;
          $('#submitOTP').html('Xác thực');
          alert("Vui lòng mở điện thoại với số điện thoại đã đăng ký và nhập OTP vào ô bên dưới!");
      }).catch(function (error) {
          alert('Cảnh báo: ' + error);
      });
  }
  function codeverify() {
      var code=document.getElementById('verificationCode').value;
      if(code != null && code != '') {
        $('#submitOTP').attr('disabled', true);
        $('#submitOTP').html('Verifing ...');
        coderesult.confirm(code).then(function (result) {
            saveUserInfo(); return;
            /* alert("Successfully registered");
            var user=result.user;
            console.log(user); */
        }).catch(function (error) {
            alert("Mã OTP Không hợp lệ");
            $('#submitOTP').attr('disabled', false);
            $('#submitOTP').html('Verify');
        });
      } else {
        alert("Vui lòng nhập OTP");
      }
      
  }

  function saveUserInfo()
  {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
      type:'POST',
      url:'/portal/auth/identify',
      data: {_token: CSRF_TOKEN},
      success:function(data){
        window.location.replace("/");
      }
    });
  }
</script>
@endpush