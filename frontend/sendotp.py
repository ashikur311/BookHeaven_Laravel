#!/usr/bin/env python3
import sys, smtplib
from email.mime.text import MIMEText

def send_otp(receiver_email, otp):
    smtp_server = "smtp.gmail.com"
    smtp_port   = 587
    smtp_user   = "ashikur31169@gmail.com"
    smtp_pass   = "lqkl wdfv atsx jgwc"  # Gmail app password

    msg = MIMEText(f"Your OTP code is: {otp}\n\nIf you didn't request this, ignore this email.")
    msg["Subject"] = "BookHeaven OTP Code"
    msg["From"] = smtp_user
    msg["To"] = receiver_email

    with smtplib.SMTP(smtp_server, smtp_port) as server:
        server.starttls()
        server.login(smtp_user, smtp_pass)
        server.send_message(msg)

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python sendotp.py <email> <otp>")
        sys.exit(1)
    send_otp(sys.argv[1], sys.argv[2])
