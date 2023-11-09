<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width" name="viewport" />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
    />

    <link href="http://fonts.cdnfonts.com/css/verdana" rel="stylesheet" />
    <title>Platerwise Payment Verification</title>

    <style>
      .main-wrapper {
        height: 100vh;
      }
      .inner-wrapper {
        border-radius: 5px;
        padding: 50px 20px;
      }
      .verify-btn {
        background-color: #db4e2e;
        border-radius: 5px;
        padding: 8px 25px;
        color: #ffffff;
        border: 0px;
        font-weight: 700;
        font-size: 13px;
      }
      .mail-inner-text {
        color: #5c5c5c;
        font-size: 14px;
      }
      * {
        font-family: verdana;
      }
      .inner-title {
        font-weight: 700;
        font-family: Arial, sans-serif;
      }
      .social-wrapper {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
      }
      .social-icon {
        min-width: 24px;
        width: 24px;
        height: auto;
      }

      .platform-icon {
        min-width: 44px;
        width: 100px;
        height: auto;
      }
      .logo-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 30px;
      }
    </style>
  </head>

  <body width="100%">

    <div class="main-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="logo-wrapper">
              <img
                src="https://plugin.markaimg.com/public/f4702e25/pt4aJOyqx8g8Y0yr6XcGkygNmYQGvr.png"
                width="241"
                style="
                  max-width: 241px;
                  width: 100%;
                  height: auto;
                  display: block;
                "
              />
            </div>
          </div>
        </div>



        <div class="d-flex justify-content-center mt-4">
            <div class="inner-wrapper">
                <div class="d-flex justify-content-center mt-4 mb-4">
                <img
                    src="https://plugin.markaimg.com/public/f4702e25/0Vb8r2AH1i6ZgCV6jKc6DXxq2eWMrd.png"
                    class="img-fluid"
                />
                </div>
            </div>
        </div>


        @if($status)
            <div class="d-flex justify-content-center mt-4">
                <div class="inner-wrapper">
                    <h5 class="inner-title">Email Address Verified</h5>

                    <div class="d-flex justify-content-center mt-4 mb-4">
                    <img
                        src="https://plugin.markaimg.com/public/f4702e25/0Vb8r2AH1i6ZgCV6jKc6DXxq2eWMrd.png"
                        class="img-fluid"
                    />
                    </div>

                    <p class="mail-inner-text">
                    "Your email is verified!
                    <br />
                    Welcome to Platterwise – get ready for delightful dining
                    experiences!"
                    </p>
                    <p class="mail-inner-text mt-3">Thank you for using Platterwise!</p>
                    <p class="mail-inner-text">
                    Best,
                    <br />
                    Platterwise Team
                    </p>
                </div>
            </div>
        @endif




        <!-- <div class="d-flex justify-content-center mt-4">
            <div class="social-wrapper">
              <div></div>
              <div>
                <img
                  class="img-fluid"
                  src="https://www.freepnglogos.com/uploads/google-play-png-logo/new-get-it-on-google-play-png-logo-20.png"
                  alt=""
                  srcset=""
                />
              </div>
            </div>
          </div> -->

        <div class="social-wrapper">
          <div>
            <img
              class="social-icon"
              src="https://plugin.markaimg.com/public/f4702e25/2x4P8YN1pa7KLxfkHCu3HQiJkcAATQ.png"
              alt=""
              srcset=""
            />
          </div>
          <div>
            <img
              class="social-icon"
              src="https://plugin.markaimg.com/public/f4702e25/KMOeDH7Y41cZ74CdUrq0isKMmuJLKT.png"
              alt=""
              srcset=""
            />
          </div>
          <div>
            <img
              class="social-icon"
              src="https://plugin.markaimg.com/public/f4702e25/MJzfBtHk1tqSavcbF1jYYs6Dl01xPZ.png"
              alt=""
              srcset=""
            />
          </div>
        </div>
      </div>
    </div>
  </body>
</html>




