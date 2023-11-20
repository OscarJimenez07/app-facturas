from flask import Flask, request, jsonify
from flask_cors import CORS  # Importa la extensión CORS
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import mysql.connector

app = Flask(__name__)
CORS(app)  # Configura CORS para permitir todas las solicitudes

# Configuración de la base de datos
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'appfacturas'
}

def conectar_bd():
    return mysql.connector.connect(**db_config)

@app.route('/actualizar', methods=['POST'])
def actualizar():
    # Conexión a la base de datos
    conexion_bd = conectar_bd()
    cursor = conexion_bd.cursor()

    data = request.get_json()
    Id = data.get('Id', request.args.get('actualizar'))
    Encargado = data.get('Encargado')
    Correo = data.get('Correo')

    try:
        # Actualizar la tabla de facturas (ajusta la consulta según tu esquema de base de datos)
        cursor.execute("UPDATE facturas SET Encargado=%s, Correo=%s WHERE Id=%s", (Encargado, Correo, Id))
        conexion_bd.commit()

        # Envío de correo electrónico
        subject = "Asignación de nueva factura"
        body = f"Hola {Encargado},\n\nSe le ha asignado una nueva factura en la aplicación. Puede ver la factura haciendo clic en el siguiente enlace:\n\nVer factura: http://localhost:8080/Factura/{Id}"

        # Configuración del servidor SMTP
        smtp_server = 'smtp-relay.gmail.com'
        smtp_port = 587
        smtp_username = 'oscar.jimenez@ardisa.com'
        smtp_password = 'Axiox941230'

        # Configuración del mensaje de correo
        msg = MIMEMultipart()
        msg['From'] = smtp_username
        msg['To'] = Correo
        msg['Subject'] = subject
        msg.attach(MIMEText(body, 'plain'))

        # Configuración del servidor SMTP
        server = smtplib.SMTP(smtp_server, smtp_port)
        server.starttls()
        server.login(smtp_username, smtp_password)

        # Envío del correo electrónico
        server.sendmail(smtp_username, Correo, msg.as_string())
        server.quit()

        return jsonify({"success": 1, "email_sent": 1})
    except Exception as e:
        return jsonify({"success": 0, "error": f"Error en la actualización de las tablas: {str(e)}"})
    finally:
        # Cerrar la conexión a la base de datos
        cursor.close()
        conexion_bd.close()

@app.route('/consultar', methods=['GET'])
def consultar():
    conexion_bd = conectar_bd()
    cursor = conexion_bd.cursor()

    cursor.execute("SELECT * FROM encargados")

    resultados = cursor.fetchall()

    encargados = []
    for resultado in resultados:
        encargado = {
            'Id': str(resultado[0]),
            'Nombre': resultado[1],
            'Correo': resultado[2],
            'IdAre': str(resultado[3]),
            # Puedes agregar más propiedades según las columnas de tu tabla
        }
        encargados.append(encargado)

    cursor.close()
    conexion_bd.close()

    return jsonify(encargados)

if __name__ == '__main__':
    app.run(debug=True)




