<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>{{ $subjectText }}</title>
  <style>
    /* Reset some default styles */
    body, p, h2, a {
      margin: 0;
      padding: 0;
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      color: #333333;
    }
    body {
      background-color: #f4f6f8;
      padding: 40px 0;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      font-size: 16px;
      line-height: 1.6;
    }
    .email-wrapper {
      width: 100%;
      background-color: #f4f6f8;
      padding: 20px 0;
    }
    .email-content {
      max-width: 600px;
      background: #ffffff;
      margin: 0 auto;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border: 1px solid #e0e0e0;
      padding: 40px 50px;
    }
    h2 {
      color: #1a237e;
      font-weight: 700;
      font-size: 28px;
      margin-bottom: 24px;
    }
    p {
      font-size: 17px;
      color: #4a4a4a;
      margin-bottom: 32px;
    }
    a.button {
      display: inline-block;
      background-color: #2962ff;
      color: #ffffff !important;
      text-decoration: none;
      padding: 14px 40px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 17px;
      box-shadow: 0 3px 8px rgba(41, 98, 255, 0.4);
      transition: background-color 0.25s ease-in-out;
    }
    a.button:hover {
      background-color: #0039cb;
      box-shadow: 0 4px 12px rgba(0, 57, 203, 0.6);
    }
    .footer-text {
      margin-top: 40px;
      font-size: 14px;
      color: #999999;
      border-top: 1px solid #e0e0e0;
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
        font-size: 24px;
      }
      a.button {
        padding: 12px 30px;
        font-size: 16px;
      }
    }
  </style>
</head>
<body>
  <div class="email-wrapper">
    <div class="email-content" role="article" aria-label="Email content">
      <h2>Hello {{ $userName }},</h2>
      <p>{{ $messageText }}</p>
      <a href="{{ $link }}" class="button" target="_blank" rel="noopener noreferrer">Click Here</a>
      <p class="footer-text">This link will expire in 5 minutes.</p>
    </div>
  </div>
</body>
</html>
