<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Zomato</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: Helvetica Neue, serif;
        }

        .container {
            background: #f5f8f9;
            display: flex;
        }

        .box {
            background: #fff;
            padding: 40px 60px;
            max-width: 600px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center !important;
            width: 100%;
            padding: 20px 0 60px 0;
        }

        .logo .logo_container img {
            width: 40px;
            height: 40px;
        }

        .logo .logo_container h3 {
            font-family: sans-serif;
            font-size: 2.25rem;
            line-height: 2.5rem;
            font-weight: 600;
            color: hsl(243, 71%, 60%);
        }

        .logo .logo_container {
            display: flex;
            align-items: center;
            margin: 0 auto;
        }

        .title {
            font-weight: 600;
            padding: 0 40px 40px 40px;
            text-align: left;
            color: #333333;
            font-family: Helvetica Neue, serif;
            font-size: 20px;
            line-height: 1;
            text-transform: capitalize;
        }

        .content {
            font-family: Helvetica Neue, serif;
            font-size: 16px;
            line-height: 24px;
            text-align: left;
            color: #333333;
            padding: 0 40px;
        }

        .btn_link {
            background: hsl(243, 71%, 60%);
            color: #ffffff !important;
            font-family: sans-serif;
            margin: 45px auto;
            padding: 8px 26px;
            border-radius: 5px;
            font-size: 15px;
            line-height: normal;
            display: block;
            text-align: center;
            text-decoration: none;
        }

        .link {
            display: block;
            color: hsl(243, 71%, 60%) !important;
            word-break: break-all;
            text-decoration: none;
            font-family: sans-serif;
            margin-top: 10px;
        }

        .footer {
            padding: 60px 0;
        }

        .footer .separator {
            width: 100%;
            height: 1px;
            background: #ccc;
            margin-bottom: 40px;
        }

        .footer .footer_bottom {
            text-align: center;
            font-family: Helvetica Neue, serif;
            font-size: 12px;
            line-height: 20px;
            color: #000000;
        }

        .footer .footer_bottom a {
            color: hsl(243, 71%, 60%);
        }
    </style>
</head>

<body>
<div class="container" style="justify-content: center">
    <div class="box">
        <div class="logo">
            <div class="logo_container" style="margin: 0 auto; display: flex; align-items: center">
                <img src="{{$mailData['logo_url']}}" alt="Zomato"/>
                <h3>Zomato</h3>
            </div>
        </div>
        <h4 class="title">{{$mailData['title']}}</h4>
        <div class="content">
            Kính gửi khách hàng,
            <br>
            Để tăng cường bảo mật tài khoản của bạn, vui lòng nhấp vào nút bên dưới để hoàn tất xác minh trong vòng
            30 phút
            tới.
            <br>
            <a class="btn_link" href="{{$mailData['url']}}" target="_blank">Xác minh ngay bây giờ</a>
            Ngoài ra, bạn có thể sao chép link liên kết và mở nó trong trình duyệt của mình.
            <br>
            <a class="link" href="{{$mailData['url']}}" target="_blank">
                {{$mailData['url']}}
            </a>
        </div>
        <div class="footer">
            <div class="separator"></div>
            <div class="footer_bottom">
                Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua
                <a class="" href="#" target="_blank">Trò chuyện trực tiếp</a> hoặc gửi email tới <a class=""
                                                                                                    href="mailto:ducanhlekha1999@gmail.com"
                                                                                                    target="_blank">zomato@com.com</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
