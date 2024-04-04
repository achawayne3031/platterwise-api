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
    <title>Password Reset</title>

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
            <h5 class="inner-title">Password Token</h5>

            <p class="mail-inner-text">{{ $mailMessageData['token'] }}</p>

            <p class="mail-inner-text mt-3">Thank you for using Tabilli!</p>

            <p class="mail-inner-text">
              Best,
              <br />
              Tabilli Team
            </p>
          </div>
        </div>

      </div>
    </div>
  </body>
</html>
