# enviarCorreo.py
import sys
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

def enviar_correo(encargado, correo, id_factura):
    try:
        # Configuración del correo
        smtp_server = 'smtp-relay.gmail.com'
        smtp_port = 587
        smtp_username = 'oscar.jimenez@ardisa.com'  
        smtp_password = 'Axiox941230'  

        destinatario = correo  
        subject = "Se le ha asignado una factura"
        body = f"""
            <html>
                <body>
                    <p>Hola {encargado},</p>
                    
                    <p>Se le ha asignado una nueva factura en la aplicación.</p>
                    
                    <p>Puede ver la factura haciendo clic en el siguiente link: http://localhost:8080/Factura/{id_factura}</p>
                </body>
            </html>
        """

        # Configuración del mensaje
        msg = MIMEMultipart()
        msg['From'] = smtp_username
        msg['To'] = destinatario
        msg['Subject'] = subject
        msg.attach(MIMEText(body, 'html'))

        # Configuración del servidor SMTP
        server = smtplib.SMTP(smtp_server, smtp_port)
        server.starttls()
        server.login(smtp_username, smtp_password)

        # Envío del correo
        server.sendmail(smtp_username, destinatario, msg.as_string())

        # Cerrar la conexión
        server.quit()

        return {"success": 1, "email_sent": 1}
    except Exception as e:
        return {"success": 0, "error": f"Error al enviar el correo: {str(e)}"}

# Llamada a la función
if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Uso: python enviarCorreo.py <encargado> <correo> <id_factura>")
    else:
        encargado = sys.argv[1]
        correo = sys.argv[2]
        id_factura = sys.argv[3]
        resultado = enviar_correo(encargado, correo, id_factura)
        print(resultado)
