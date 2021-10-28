<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width"/><!-- IMPORTANT -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Reset Password</title>

    <style type="text/css">
        .mail-container {
            background: #f7f7f7;
            width: 100%;
            padding: 5px 0;
        }

        .mail-header .mail-logo {
            text-align: left;
            padding: 20px 10px;
        }

        .mail-body {
            padding: 10px;
            background: #ffffff;
        }

        .mail-header .mail-header-title {
            background: #5ebac9;
            color: #FFFFFF;
            text-align: center;
            padding: 15px;
            letter-spacing: 2px;
        }

        .mail-header .mail-header-title h1 {
            padding: 0;
            margin: 0;
        }

        .body-title {
        }

        .body-title h4 {
            padding: 5px 0;
            color: #404040;
            font-weight: normal;
            font-size: 18px;
            line-height: 30px;
            margin: 0;
            text-align: center;
        }

        .body-title h4 span {
            color: #f16334;
        }

        .body-title p {
            padding: 10px 0 5px 0;
            margin: 0;
            font-size: 14px;
            color: #777777;
            text-align: center;
        }


        .mail-footer {
            text-align: center;
        }

        .mail-footer p {
            font-size: 11px;
            padding: 5px 0;
            margin: 5px 0 5px 0;
            line-height: 20px;
            color: #999999;
            letter-spacing: 1px;
        }

        .mail-inner-content {
            width: 700px;
            margin: 20px auto;
            /*border: 1px solid #efefef;*/
            border-radius: 0px;
        }

        .mail-header .mail-logo {
            text-align: center;
            padding: 20px 10px;
        }
    </style>
</head>
<body>
<div class="mail-container">
    <div class="mail-inner-content">
        <!-- HEADER -->
        <div class="mail-header">
            <div class="mail-logo">
                <?php
                $settingLogo = \App\Models\Settings::where('name','logo')->first();
                $settingURL = \App\Models\Settings::where('name','frontend_url')->first();
                ?>
                <img src="{{ url($settingLogo->value) }}" alt="vaultex logo" height="50"/>
            </div>
            <div class="mail-header-title">
                <h1>Vaultex</h1>
            </div>
        </div>
        <div class="mail-body">
            <div class="body-title">
                <h4>
                    Hello, <span>
                </h4>
                <p>
                    <div>
                        <strong>Function Name: </strong>{{$data['function']}}
                    </div>
                    <div>
                        <strong>Message: </strong>{{$data['message']}}
                    </div>
                    <div>
                        <strong>Error: </strong>{{print_r($data['error'])}}
                    </div>
                </p>
                <p>Thank You</p>
            </div>
        </div>
        <!-- FOOTER -->
        <div class="mail-footer">
            <p><br/>
                Copyright © <?php echo date("Y"); ?> SBM. All right reserved. </p>
        </div>
    </div>
</div>
</body>
</html>



