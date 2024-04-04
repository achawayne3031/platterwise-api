



<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width" name="viewport" />
    <meta content="IE=edge" http-equiv="X-UA-Compatible" />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
    />

    <link href="http://fonts.cdnfonts.com/css/verdana" rel="stylesheet" />
    <title>Your Split Payment</title>

    <style>
      html {
        margin: 0 !important;
        padding: 0 !important;
      }

      * {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
      }
    </style>

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
        text-align: center;
      }
      * {
        font-family: verdana;
      }
      .inner-title {
        font-weight: 700;
        font-family: Arial, sans-serif;
        text-align: center;
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

      .summary-title-text{
        font-size: 15px;
        font-weight: bold;
      }

      .inner-tr{
        margin-top: 15px;
      }

      .img-wrapper{
        justify-content: center;
        display: flex;
      }

    </style>
  </head>

  <body width="100%" style="margin: 0; padding: 0 !important;">
    <div class="main-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="logo-wrapper">
              <img
                src="https://tabilli.com/Tabili.png"
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
                <div class="img-wrapper">
                    <img src="https://plugin.markaimg.com/public/1f9d7629/5jUekpQy0XYPFVMdimn4HJuHddbZge.png" width="276" border="0" style="
                    max-width:276px; width: 100%; height: auto; display: block;">
                </div>

                <h5 class="inner-title">Payment Successful</h5>
                <p class="mail-inner-text">Hi {{ $mailMessageData['guest_name'] }}! your payment of ₦{{ $mailMessageData['amount'] }} on {{ $mailMessageData['payment_date'] }}, <br> at {{ $mailMessageData['restaurant_name'] }} restaurant was successful</p>
                <h6 class="summary-title-text">Order summary:</h6>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr class="inner-tr">
                        <td>
                            <div class="inner-tr" style="text-align:left;"><span style="color:#5c5c5c;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:left;">Subtotal</span></div>
                        </td>
                        <td> </td>
                        <td>
                            <div class="inner-tr" style="text-align:right;"><span style="color:#5c5c5c;font-weight:700;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:right;">₦ {{ $mailMessageData['amount'] }} </span></div>
                        </td>
                    </tr>

                    <tr class="inner-tr">
                        <td>
                        <div class="inner-tr" style="text-align:left;"><span style="color:#5c5c5c;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:left;">Discount</span></div>
                        </td>
                        <td> </td>
                        <td>
                        <div class="inner-tr" style="text-align:right;"><span style="color:#5c5c5c;font-weight:700;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:right;">₦0</span></div>
                        </td>
                    </tr>

                    <tr class="inner-tr">
                        <td>
                        <div class="inner-tr" style="text-align:left;"><span style="color:#5c5c5c;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:left;">Tax (0%)</span></div>
                        </td>
                        <td> </td>
                        <td>
                        <div class="inner-tr" style="text-align:right;"><span style="color:#5c5c5c;font-weight:700;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:right;">₦0</span></div>
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <div class="inner-tr" style="text-align:left;"><span style="color:#5c5c5c;font-weight:700;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:left;">Total</span></div>
                        </td>
                        <td> </td>
                        <td>
                            <div class="inner-tr" style="text-align:right;"><span style="color:#5c5c5c;font-weight:700;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:right;">₦ {{ $mailMessageData['amount'] }} </span></div>
                        </td>
                    </tr>
                </table>
            <div>

            <!-- <div style="text-align: center;">
                <button class="verify-btn">Make Payment</button>
            </div> -->

        </div>

            <p class="mail-inner-text mt-3">Thank you for using Tabilli!</p>

            <p class="mail-inner-text">
              Best,
              <br />
              Tabilli Team
            </p>
          </div>

        </div>

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
