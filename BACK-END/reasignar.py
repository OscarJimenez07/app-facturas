# facturas.py
from flask import request, jsonify
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import mysql.connector

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'appfacturas'
}

def conectar_bd():
    return mysql.connector.connect(**db_config)

def reasignar_encargado():
    conexion_bd = conectar_bd()
    cursor = conexion_bd.cursor()

    try:
        data = request.get_json()
        Id = data.get('Id', request.args.get('actualizar'))
        Encargado = data.get('Encargado')
        Correo = data.get('Correo')

        cursor.execute("UPDATE facturas SET Encargado=%s, Correo=%s WHERE Id=%s", (Encargado, Correo, Id))
        conexion_bd.commit()

        subject = "Asignación de nueva factura"
        body = f"Hola {Encargado},\n\nSe le ha asignado una nueva factura en la aplicación. Puede ver la factura haciendo clic en el siguiente enlace:\n\nVer factura: http://localhost:8080/Factura/{Id}"

        smtp_server = 'smtp-relay.gmail.com'
        smtp_port = 587
        smtp_username = 'oscar.jimenez@ardisa.com'
        smtp_password = 'Axiox941230'

        msg = MIMEMultipart()
        msg['From'] = smtp_username
        msg['To'] = Correo
        msg['Subject'] = subject
        msg.attach(MIMEText(body, 'plain'))

        server = smtplib.SMTP(smtp_server, smtp_port)
        server.starttls()
        server.login(smtp_username, smtp_password)

        server.sendmail(smtp_username, Correo, msg.as_string())
        server.quit()

        return jsonify({"success": 1, "email_sent": 1})
    except Exception as e:

        return jsonify({"success": 0, "error": "Error en la actualización de las tablas."})
    finally:
        cursor.close()
        conexion_bd.close()

