# encargados.py
from flask import request, jsonify
import mysql.connector

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'appfacturas'
}
def conectar_bd():
    return mysql.connector.connect(**db_config)
def consultar_encargados():
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
        }
        encargados.append(encargado)

    cursor.close()
    conexion_bd.close()

    return jsonify(encargados)

