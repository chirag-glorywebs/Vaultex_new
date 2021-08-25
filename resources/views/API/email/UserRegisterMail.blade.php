<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>{{$details['title']}}</title>
    <style type="text/css">
        @import url(http://fonts.googleapis.com/css?family=Lato:400);

        /* Take care of image borders and formatting */

        img {
            max-width: 600px;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        a {
            text-decoration: none;
            border: 0;
            outline: none;
        }

        a img {
            border: none;
        }

        /* General styling */

        td, h1, h2, h3 {
            font-family: Helvetica, Arial, sans-serif;
            font-weight: 400;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100%;
            height: 100%;
            color: #37302d;
            background: #ffffff;
        }

        table {
            background:
        }

        h1, h2, h3 {
            padding: 0;
            margin: 0;
            color: #ffffff;
            font-weight: 400;
        }

        h3 {
            color: #21c5ba;
            font-size: 24px;
        }
    </style>

    <style type="text/css" media="screen">
        @media screen {
            /* Thanks Outlook 2013! http://goo.gl/XLxpyl*/
            td, h1, h2, h3 {
                font-family: 'Lato', 'Helvetica Neue', 'Arial', 'sans-serif' !important;
            }
        }
    </style>

    <style type="text/css" media="only screen and (max-width: 480px)">
        /* Mobile styles */
        @media only screen and (max-width: 480px) {

            table[class="w320"] {
                width: 320px !important;
            }

            table[class="w300"] {
                width: 300px !important;
            }

            table[class="w290"] {
                width: 290px !important;
            }

            td[class="w320"] {
                width: 320px !important;
            }

            td[class="mobile-center"] {
                text-align: center !important;
            }

            td[class="mobile-padding"] {
                padding-left: 20px !important;
                padding-right: 20px !important;
                padding-bottom: 20px !important;
            }
        }
    </style>
</head>
<body class="body" style="padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none"
      bgcolor="#ffffff">
<table align="center" cellpadding="0" cellspacing="0" width="100%" height="100%">
    <tr>
        <td align="center" valign="top" bgcolor="#ffffff" width="100%">

            <table cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td style="border-bottom: 3px solid #3bcdc3;" width="100%">
                        <center>
                            <table cellspacing="0" cellpadding="0" width="500" class="w320">
                                <tr>
                                    <td valign="top" style="padding:10px 0; text-align:left;" class="mobile-center">
                                        <?php $settingLogo = \App\Models\Settings::where('name', 'logo')->first(); ?>
                                        <img src="{{ url($settingLogo->value) }}" alt="vaultex logo" height="62"
                                             width="200"/>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <img src="{{ asset('uploads/2021/02/about-us-video.png') }}" width="100%" height="300px">
                    <td style="background: url({{ asset('uploads/2021/02/about-us-video.png') }}) no-repeat center; background-color: #64594b; background-position: center;">

                        <div>
                            <center>
                                <table cellspacing="0" cellpadding="0" width="590" height="303" class="w320">
                                    <tr>
                                        <td valign="middle"
                                            style="vertical-align:middle; padding-right: 15px; padding-left: 15px; text-align:left;"
                                            class="mobile-center" height="303">

                                            <h1>Hello {{$details['name']}} ,</h1>
                                            <h2>Congratulations , Your Account has been successfully created. Welcome to
                                                Vaultex!</h2>
                                            <h2>
                                                From now on , please login to your account using Your email address and
                                                your password .</h2><br/>
                                            <h2>Thank you</h2>

                                        </td>
                                    </tr>
                                </table>
                            </center>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="background-color:#c2c2c2;">
                        <center>
                            <table style="margin:0 auto;" cellspacing="0" cellpadding="5" width="100%">
                                <tr>
                                    <td style="text-align:center; margin:0 auto;" width="100%">
                                        <p><br/>
                                            Copyright © <?php echo date("Y"); ?> SBM. All right reserved. </p>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            </center>
        </td>
    </tr>
</table>
</td>
</tr>
</table>
</body>
</html>
