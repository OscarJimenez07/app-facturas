from flask import Flask
from flask_cors import CORS
from encargados import consultar_encargados
from reasignar import reasignar_encargado

app = Flask(__name__)
CORS(app)

@app.route('/encargados/consultar', methods=['GET'])
def consultar_route():
    return consultar_encargados()

@app.route('/encargados/reasignar', methods=['POST'])
def reasignar_route():
    return reasignar_encargado()

if __name__ == '__main__':
    app.run(debug=True)
