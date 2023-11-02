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
    <title>Reservation Cancelled</title>

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
        background-color: #ffffff;
      }
      .verify-btn {
        background-color: #db4e2e;
        border-radius: 5px;
        padding: 8px 25px;
        color: #ffffff !important;
        border: 0px;
        font-weight: 700;
        font-size: 11px;
        text-decoration: none;
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
        font-size: 25px;
      }
      .semi-inner-title {
        font-weight: 700;
        font-family: Arial, sans-serif;
        font-size: 18px;
      }
      .social-wrapper {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
        width: 100%;
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


      .inner-bill{
        display: flex;
        gap: 10px;
        justify-content: space-between;
        margin-bottom: 15px;
      }

      .bill-value{
        color: black;
        font-weight: bolder;
        font-size: 14px;
      }

      .bill-title{
        font-size: 14px;
        color: #5c5c5c;


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
            <h5 class="inner-title">Reservation Cancelled</h5>

            <!-- <p class="mail-inner-text">Hi Michael,</p> -->

            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                <td width="480">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                <td>
                <div style="text-align:left;"><span style="color:#5c5c5c;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:left;">Hello {{ $mailMessageData['owner_name'] }},</span></div>
                </td>
                </tr>
                <tr>
                <td height="16" style="height:16px; min-height:16px; line-height:16px;"></td>
                </tr>
                <tr>
                <td>
                <div style="text-align:left;"><span style="color:#5c5c5c;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:left;">We regret to inform you that your reservation has been canceled at </span><span style="color:#5c5c5c;font-weight:700;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:left;">{{ $mailMessageData['restaurant'] }} Restaurant</span><span style="color:#5c5c5c;font-family:SF Pro Display,Arial,sans-serif;font-size:16px;letter-spacing:0.20000000298023224px;line-height:150%;text-align:left;">. We understand the inconvenience this may cause and would like to you to check other available restaurants. We sincerely apologize for the inconvenience.</span></div>
                </td>
                </tr>
                </table>
                </td>
                </tr>
                <tr>
                <td height="46" style="height:46px; min-height:46px; line-height:46px;"></td>
                </tr>
                <tr>
                <td align="center">
                <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                <td>




                </td>
                </tr>
                </table>
                </td>
                </tr>
                </table>
                </td>
                </tr>
                </table>





            <p class="mail-inner-text mt-3">Thank you for using Platterwise!</p>

            <p class="mail-inner-text">
              Best,
              <br />
              Platterwise Team
            </p>
          </div>
        </div>

      </div>
    </div>
  </body>
</html>
