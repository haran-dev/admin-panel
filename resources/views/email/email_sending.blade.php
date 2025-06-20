<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>{{ $subjectText }}</title>
    <style>
        body,
        p,
        h2,
        a {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
        }

        body {
            background-color: #f4f6f8;
            padding: 40px 0;
            font-size: 16px;
            line-height: 1.6;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f6f8;
            padding: 20px 0;
        }

        .email-content {
            max-width: 620px;
            background: #ffffff;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
            padding: 40px 50px;
        }

        h2 {
            color: #1e88e5;
            font-weight: bold;
            font-size: 26px;
            margin-bottom: 20px;
        }

        p {
            font-size: 17px;
            color: #444;
            margin-bottom: 30px;
        }

        .reward-box {
            background-color: #e3f2fd;
            border-left: 6px solid #1e88e5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 6px;
            color: #1565c0;
            font-size: 18px;
            font-weight: 600;
        }

        a.button {
            display: inline-block;
            background-color: #1e88e5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 17px;
            box-shadow: 0 4px 10px rgba(30, 136, 229, 0.4);
            transition: background-color 0.3s ease-in-out;
        }

        a.button:hover {
            background-color: #1565c0;
            box-shadow: 0 6px 14px rgba(21, 101, 192, 0.6);
        }

        .footer-text {
            margin-top: 40px;
            font-size: 13px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            text-align: center;
            font-style: italic;
        }

        @media screen and (max-width: 620px) {
            .email-content {
                padding: 30px 20px;
                width: 90%;
            }

            h2 {
                font-size: 22px;
            }

            a.button {
                padding: 12px 24px;
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-content" role="article" aria-label="Loyalty Reward Email">
            <h2>Hello {{ $userName }},</h2>

            <p>We’re thrilled to have you as part of our community. As a small token of our appreciation for your loyalty, we have a special reward just for you:</p>

            <div class="reward-box">
                🎁 Rs.500 OFF your next purchase!
                <br>
                Use Code: <strong>LOYAL500</strong>
            </div>

            <p>Apply this code at checkout to enjoy your exclusive discount. But hurry – this offer is valid for a limited time only!</p>

            <p>
                <a href="{{ url('/your-promo-page') }}" class="button">Redeem Your Reward</a>
            </p>

            <div class="footer-text">
                Thank you for being with us. <br>
                — The Team
            </div>
        </div>
    </div>
</body>

</html>