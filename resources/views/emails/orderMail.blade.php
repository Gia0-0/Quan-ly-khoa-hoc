<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Zomato</title>
    <style type="text/css">
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }


        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        @media screen and (max-width: 480px) {
            .mobile-hide {
                display: none !important;
            }

            .mobile-center {
                text-align: center !important;
            }
        }

        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }
    </style>
</head>

<body style="margin: 0 !important; padding: 0 !important; background-color: #eeeeee;" bgcolor="#eeeeee">
    <div
        style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Open Sans, Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        For what reason would it be advisable for me to think about business content? That might be little bit risky to
        have crew member like them.
        </head>

        <body>
            <div class="container">
                // code giao dien mail trong nay
            </div>

            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center" style="background-color: #eeeeee;" bgcolor="#eeeeee">

                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="max-width:600px;">
                            <tr>
                                <td align="center" valign="top" style="font-size:0; padding: 35px;" bgcolor="#F44336">

                                    <div
                                        style="display:inline-block; max-width:50%; min-width:100px; vertical-align:top; width:100%;">
                                        <table align="left" border="0" cellpadding="0" cellspacing="0"
                                            width="100%" style="max-width:300px;">
                                            <tr>
                                                <td align="left" valign="top"
                                                    style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 36px; font-weight: 800; line-height: 48px;"
                                                    class="mobile-center">
                                                    <h1
                                                        style="font-size: 36px; font-weight: 800; margin: 0; color: #ffffff;">
                                                        BBBootstrap</h1>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding: 35px 35px 20px 35px; background-color: #ffffff;"
                                    bgcolor="#ffffff">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                                        style="max-width:600px;">
                                        <tr>
                                            <td align="center"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding-top: 25px;">
                                                <img src="https://img.icons8.com/carbon-copy/100/000000/checked-checkbox.png"
                                                    width="125" height="120"
                                                    style="display: block; border: 0px;" /><br>
                                                <h2
                                                    style="font-size: 30px; font-weight: 800; line-height: 36px; color: #333333; margin: 0;">
                                                    Thank You For Your Order!
                                                </h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" style="padding-top: 20px;">
                                                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                                    <tr>
                                                        <td width="75%" align="left" bgcolor="#eeeeee"
                                                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                                                            Order Confirmation #
                                                        </td>

                                                        <td width="25%" align="left" bgcolor="#eeeeee"
                                                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                                                            {{ $mailData['order_code'] }}
                                                        </td>
                                                    </tr>
                                                    @if (isset($mailData['courses']) && $data['courses']->count() > 0)
                                                        @foreach ($mailData['courses'] as $key => $value)
                                                            <tr>
                                                                <td width="75%" align="left"
                                                                    style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 15px 10px 5px 10px;">
                                                                    {{ $value->course_name }}
                                                                </td>
                                                                <td width="25%" align="left"
                                                                    style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 15px 10px 5px 10px;">
                                                                    {{ $value->price }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endIf
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" style="padding-top: 20px;">
                                                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                                    <tr>
                                                        <td width="75%" align="left"
                                                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                                                            TOTAL
                                                        </td>
                                                        <td width="25%" align="left"
                                                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                                                            {{ $mailData['total_price'] }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="75%" align="left"
                                                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                                                            PRICE SALE
                                                        </td>
                                                        <td width="25%" align="left"
                                                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                                                            {{ $mailData['price_sale'] }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
    </div>
</html>
