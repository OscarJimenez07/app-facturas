<template>
    <div v-if="ver" class="border p-3 mb-3" style="background-color:#212529;">
      <form @submit.prevent="AsignarEncargado">
        <div class="mb-3">
          <label style="color:white"><strong>Usuario a Reasignar:</strong></label>
          <select v-model="encargadoSeleccionado" class="form-control" style="background-color:#e6e6e6;" required>
            <option value="">Seleccione el Encargado</option>
            <option v-for="encargado in encargados" :key="encargado.id" :value="encargado.Nombre">{{ encargado.Nombre }}</option>
          </select>
        </div>
        <button type="submit" class="btn btn-warning position-relative" :disabled="cargando">
          <i class="bi bi-people-fill"></i>
          <span v-if="cargando" class="spinner"></span>
          <span v-else> Reasignar Factura</span>
        </button>
      </form>
    </div>
  </template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            facturas: [],
            encargados: [],
            encargadoSeleccionado: '', 
            ver: true,
            cargando: false,
        }
    },
    created:function() {
        this.getFacturas();
        this.getEncargados();
        this.getEditar();
    },
    methods: {
        IdFactura(Id) {
            if (this.facturas.length > 0) {
                return this.facturas.find(factura => factura.Id === Id);
            }
        },
        getFacturas() {
            axios.get('http://localhost:8081/DatosFacturas/facturas.php') 
                .then(response => {
                    this.facturas = response.data;
                })
        },

        getEncargados() {
    axios.post('http://localhost:8081/DatosFacturas/encargados.php?consultarEncargados=' + this.$route.params.Id)
        .then((response) => {
            if (response.data.length === 1) {
                axios.get('http://localhost:8081/DatosFacturas/encargados.php')
                    .then(response => {
                        this.encargados = response.data;
                    });
            } else {
                this.encargados = response.data;
            }
        })
        .catch(error => {
            console.error("Error al obtener encargados:", error);
        });
},


AsignarEncargado() {
      const encargado = this.encargados.find(e => e.Nombre === this.encargadoSeleccionado);
      const facturaId = this.$route.params.Id;
      const modifico = sessionStorage.getItem("username");
      const DatosEnviar = {
        Id: facturaId,
        Encargado: encargado.Nombre,
        Usuario: encargado.UsrNam,
        Correo: encargado.Correo,
        Modifico: modifico
      };

      this.cargando = true;

      axios.post('http://localhost:8081/DatosFacturas/reasignar.php?Encargado', DatosEnviar)
        .then((response) => {
          console.log(response.data);
          this.mensajeExito = "Factura reasignada con éxito";
        })
        .catch((error) => {
          console.error('Error al reasignar factura:', error);
          this.mensajeError = "Error al reasignar factura. Inténtalo de nuevo.";
        })
        .finally(() => {
          setTimeout(() => {
            this.cargando = false;
            window.location.reload();
          }, 3000); 
        });
    },

        getEditar() {
  axios.post('http://localhost:8081/DatosFacturas/cargas.php?editar=' + this.$route.params.Id)
    .then((respuesta) => {
      const datosRespuesta = respuesta.data;
      if (datosRespuesta[0].resultado === 'PENDIENTE POR AUTORIZAR') {
        this.editar = false;
      } else if (datosRespuesta[0].resultado === 'TRAMITADA' || datosRespuesta[0].resultado === 'CONTABILIZADA') {
        this.ver = false;
      }

      if (datosRespuesta[0].usuario === sessionStorage.getItem("username") && datosRespuesta[0].resultado !== 'TRAMITADA'  && datosRespuesta[0].resultado !== 'CONTABILIZADA') {
        this.ver = true;
      } else {
        this.ver = false;
      }

    })
}


    },    
};
</script>

<style>
.spinner {
  border: 7px solid grey;
  border-radius: 70%;
  border-top: 7px solid black;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
  display: inline-block;
  position: center;

  transform: translate(-50%, -50%);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>