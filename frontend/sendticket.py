# sendticket.py

import sys
import smtplib
import logging
import os
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.application import MIMEApplication
from email.utils import formatdate

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('tickets/email_log.log'),
        logging.StreamHandler()
    ]
)

def send_ticket(recipient_email, pdf_path):
    # Email configuration (replace with your actual credentials)
    SMTP_SERVER = "smtp.gmail.com"
    SMTP_PORT = 587
    SENDER_EMAIL = "aragroupakash@gmail.com"
    SENDER_PASSWORD = "hynkcrjapkkhueal"  # Use App Password for Gmail
    
    # Validate inputs
    if not all([recipient_email, pdf_path]):
        logging.error("Missing required parameters")
        return False
        
    if not os.path.isfile(pdf_path):
        logging.error(f"PDF file not found: {pdf_path}")
        return False

    # Create email message
    msg = MIMEMultipart()
    msg['From'] = SENDER_EMAIL
    msg['To'] = recipient_email
    msg['Date'] = formatdate(localtime=True)
    msg['Subject'] = "Your Event Ticket - BookHeaven"
    
    # Email body
    body = f"""Dear Attendee,

Thank you for registering for our event. Please find your ticket attached.

Ticket Details:
- Please bring this ticket (printed or digital) to the event
- Present the QR code at the entrance for scanning
- This ticket is valid for one person only

If you have any questions, please contact support@bookheaven.com.

Best regards,
The BookHeaven Team
"""
    msg.attach(MIMEText(body, 'plain'))
    
    # Attach PDF
    try:
        with open(pdf_path, "rb") as f:
            part = MIMEApplication(f.read(), Name=os.path.basename(pdf_path))
        part['Content-Disposition'] = f'attachment; filename="{os.path.basename(pdf_path)}"'
        msg.attach(part)
    except Exception as e:
        logging.error(f"Failed to attach PDF: {e}")
        return False

    # Send email
    try:
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            server.starttls()
            server.login(SENDER_EMAIL, SENDER_PASSWORD)
            server.sendmail(SENDER_EMAIL, recipient_email, msg.as_string())
        logging.info(f"Ticket sent successfully to {recipient_email}")
        return True
    except Exception as e:
        logging.error(f"Failed to send email: {e}")
        return False

if __name__ == "__main__":
    if len(sys.argv) != 3:
        logging.error("Usage: python sendticket.py <email> <pdf_path>")
        sys.exit(1)
        
    recipient_email = sys.argv[1]
    pdf_path = sys.argv[2]
    
    success = send_ticket(recipient_email, pdf_path)
    if success:
        print("Ticket sent successfully.")
        sys.exit(0)
    else:
        print("Failed to send ticket.")
        sys.exit(1)