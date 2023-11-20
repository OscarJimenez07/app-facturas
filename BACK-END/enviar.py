import imaplib
import email
import os
from zipfile import ZipFile
import mysql.connector
import datetime
import quopri
import re
import smtplib
from email.mime.text import MIMEText

email_user = 'oscar.jimenez@ardisa.com'
email_pass = 'Axiox941230'
mail = imaplib.IMAP4_SSL('imap.gmail.com')
mail.login(email_user, email_pass)
mail.select('inbox')

db_connection = mysql.connector.connect(
    host='localhost',
    user='root',
    password='',
    database='appfacturas'
)
cursor = db_connection.cursor()

archivos_comprimidos_folder = 'archivos'
os.makedirs(archivos_comprimidos_folder, exist_ok=True)

fecha_limite = datetime.date(2023, 11, 6)

# Expresión regular para extraer el NIT de un formato inusual
nit_pattern = r'(\d+)(?=\s|$)'

result, data = mail.uid('search', None, f'(NOT X-GM-LABELS "procesado" SENTSINCE {fecha_limite.strftime("%d-%b-20%y")})')
if result == 'OK':
    email_ids = data[0].split()
    if not email_ids:
        print('No hay correos sin la etiqueta "procesado" desde la fecha límite.')
    else:
        for email_id in email_ids:
            result, data = mail.uid('fetch', email_id, '(RFC822)')
            if result == 'OK':
                raw_email = data[0][1].decode('iso-8859-1')
                msg = email.message_from_string(raw_email)
                subject = msg['Subject']
                subject = quopri.decodestring(subject).decode('iso-8859-1')
                subject_parts = subject.split(';')
                if len(subject_parts) < 5:
                    print('Correo ignorado. Estructura del asunto no válida.')
                else:
                    nit_match = re.search(nit_pattern, subject_parts[0])
                    if nit_match:
                        nit = nit_match.group(1)
                    else:
                        print('No se pudo extraer el NIT del asunto.')
                        nit = 'N/A'

                    nombre = subject_parts[1].strip()
                    numero_factura = subject_parts[2].strip()

                    print(f"De: {msg['From']}")
                    print(f"NIT: {nit}")
                    print(f"Nombre: {nombre}")
                    print(f"Número de Factura: {numero_factura}")

                    if nit == 'N/A':
                        estado = "INCORRECTA"
                    else:
                        estado = "CORRECTA"

                    folder_path = os.path.join(archivos_comprimidos_folder, str(email_id.decode()))
                    os.makedirs(folder_path, exist_ok=True)

                    extraccion_exitosa = False

                    for part in msg.walk():
                        if part.get_content_type() == 'application/zip':
                            attachment = part.get_payload(decode=True)
                            file_name = os.path.join(folder_path, 'archivo.zip')
                            with open(file_name, 'wb') as f:
                                f.write(attachment)

                            with ZipFile(file_name, 'r') as zip_ref:
                                zip_ref.extractall(folder_path)
                            pdf_files = [f for f in os.listdir(folder_path) if f.lower().endswith('.pdf')]
                            if pdf_files:
                                pdf_file_name = pdf_files[0]
                                new_pdf_file_name = f"{os.path.basename(folder_path)}.pdf"
                                pdf_file_path = os.path.join(folder_path, pdf_file_name)
                                new_pdf_file_path = os.path.join(folder_path, new_pdf_file_name)

                                try:
                                    os.rename(pdf_file_path, new_pdf_file_path)
                                    extraccion_exitosa = True
                                    print(f"Archivo PDF renombrado a: {new_pdf_file_path}")
                                except Exception as e:
                                    print(f"Error al renombrar el archivo PDF: {e}")

                    if not extraccion_exitosa:
                        print("Extracción fallida. Marcar el estado en consecuencia.")
                        estado = "INCORRECTA"

                    insert_query = "INSERT INTO facturas (nit, nombre, numero, adjunto, estado) VALUES (%s, %s, %s, %s, %s)"
                    values = (nit, nombre, numero_factura, email_id.decode(), estado)
                    cursor.execute(insert_query, values)
                    db_connection.commit()
                    print(f"Datos guardados en la base de datos con estado: {estado}")

                    # Realizar la consulta para obtener información de la factura
                    select_query = "SELECT Id, Encargado, Correo FROM facturas WHERE adjunto = %s"
                    cursor.execute(select_query, (email_id.decode(),))
                    result = cursor.fetchone()

                    if result:
                        factura_id, encargado, correo = result

                        # Enviar el correo electrónico al encargado
                        subject = f"Asignación de nueva factura: {numero_factura}"
                        body = f"""
                        Hola {encargado},

                        Se le ha asignado una nueva factura en la aplicación. Puede ver la factura haciendo clic en el siguiente enlace:

                        Ver factura: http://localhost:8080/Factura/{factura_id}
                        """
                        msg = MIMEText(body)
                        msg['Subject'] = subject
                        msg['From'] = email_user
                        msg['To'] = correo

                        smtp_server = 'smtp-relay.gmail.com'
                        smtp_port = 587

                        with smtplib.SMTP(smtp_server, smtp_port) as server:
                            server.starttls()
                            server.login(email_user, email_pass)
                            server.sendmail(email_user, correo, msg.as_string())

                        print(f"Correo electrónico enviado a {encargado} ({correo})")

                    mail.uid('store', email_id, '+FLAGS', '(\Seen)')
                    mail.uid('store', email_id, '+X-GM-LABELS', 'procesado')

cursor.fetchall()
cursor.close()
db_connection.close()
mail.logout()
