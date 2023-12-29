<template>
    <div class="border" style="background-color:#212529;">      
      <label style="color: white;" class="p-1 mb-1"><strong>CARGAR FACTURA</strong></label>
    <form @submit="submitForm">
      <table class="table table-dark">
        <thead>
          <tr>
            <th style="width: 30%;">Cuenta Contable</th>
            <th style="width: 40%;">Centro de Costos</th>
            <th style="width: 30%;">Valor</th>
            <th style="width: 20%;">%</th>
            <th style="width: 20%;" v-if="cargas && cargas.length > 0  && editar && !autorizarVisible && !okVisible">Acción</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(carga, index) in cargas" :key="index">
            <td>{{ carga.Cuenta }}</td>
            <td>{{ carga.Centro }}</td>
            <td>${{ formatNumber(carga.Valor) }}</td> 
            <td>{{ carga.Porcentaje }}%</td>
            <td>
            <button type="button" v-on:click="borrarCarga(carga.Id)" class="btn btn-danger" v-if=" cargas.length > 0 && editar && !autorizarVisible && !okVisible" >Borrar</button>
           </td>
          </tr>

          <tr v-for="(row, index) in rows" :key="'empty' + index">
            <td>
              <select v-if="editar && !autorizarVisible && !okVisible" v-model="row.cuentaSeleccionada" name="cuenta" id="cuenta" style="width: 100%; background-color:#e6e6e6;" class="form-control">
                <option value="" >Seleccione la cuenta contable</option>
                <option v-for="cuenta in cuentas" :key="cuenta.Numero">
                  {{ cuenta.Numero }}
                </option>
              </select>
            </td>
            <td>
              <select v-if="editar && !autorizarVisible && !okVisible" v-model="row.centroSeleccionado" name="centro" id="centro" style="width: 100%; background-color:#e6e6e6;" class="form-control">
                <option value="" >Seleccione el centro de costos</option>
                <option v-for="centro in centros" :key="centro.Numero">
                  {{centro.Numero + ' - ' + centro.Nombre }}
                </option>
              </select>
            </td>
            <td>
              <input v-if="editar && !autorizarVisible && !okVisible" v-model="row.valor" type="number" style="width: 100%; background-color:#e6e6e6;" class="form-control" placeholder="Ingrese el valor" required>
            </td> 
            <td>   
              <input v-if="editar && !autorizarVisible && !okVisible" v-model="row.Porcen" type="number" style="width: 90px; background-color:#e6e6e6;" class="form-control" placeholder="%" readonly >  
            </td>
          </tr>
        </tbody>

        <tr v-if="rows.length > 0">
  <td colspan="3" class="text-right ml-5">
    <label style="color: white;" v-if=" editar"><strong>Total Temporal: ${{ formatNumber(totalTemporal) }}</strong></label>
  </td>
</tr>
      </table>

      <div class="row" style="justify-content: center;">    
       
        <div class="col-1">
        <button v-if="editar && !autorizarVisible && !okVisible" type="button" class="btn btn-success" @click="addRow">+</button>
      </div>
      <div class="col-1">
      <button v-if="editar && !autorizarVisible && !okVisible" type="button" class="btn btn-danger" @click="delRow">-</button>
    </div>
    </div>
   
<p></p>

      <button v-if="editar && !autorizarVisible && !okVisible" type="submit" class="btn btn-primary" style="color: black;"><i class="bi bi-send-check-fill"></i> Enviar a Autorizar</button>
      <button v-if="autorizarVisible" @click.prevent="autorizar" class="btn btn-success">Autorizar</button>
      <button v-if="!autorizarVisible && okVisible"  @click.prevent="Ok" class="btn btn-success">Ok</button>
      <button v-if="ContabilVisible"  @click.prevent="contabilizar" class="btn btn-success">Contabilizar</button>

<p></p>

    </form>

  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      centros: [],
      facturas: [],
      cuentas: [],
      cargas: [],
      rows: [],
      totalTemporal: 0, 
      autorizarVisible: false,
      editar: false,
      ver: false,
      okVisible: false,
      ContabilVisible: false
    };
  },
  created: function () {
    this.getCentros();
    this.getFacturas();
    this.getCuentas();
    this.getCargas();
    this.addRow();
    this.getBoton();
    this.getEditar();
    this.getOk();
    this.getContabilizada();
  },
  methods: {
    getFacturas(){
      fetch ('http://localhost:8081/DatosFacturas/facturas.php?consultarFactura=' + this.$route.params.Id).then((respuesta) => respuesta.json())
      .then((datosRespuesta) => {
          this.facturas = datosRespuesta
    })
 },
    getCentros() {
      const usuario = sessionStorage.getItem("username");  
      fetch('http://localhost:8081/DatosFacturas/centros.php?Encargado=' + usuario).then((respuesta) => respuesta.json())
      .then((DatosRespuesta) => {
         this.centros = DatosRespuesta;
      })
    },

    getCuentas() {
      const usuario = sessionStorage.getItem("username");  
      fetch('http://localhost:8081/DatosFacturas/cuentas.php?Encargado=' + usuario).then((respuesta) => respuesta.json())
      .then((DatosRespuesta) => {
         this.cuentas = DatosRespuesta;
      }) 
    },

    getCargas() {
      fetch('http://localhost:8081/DatosFacturas/cargas.php?consultarCargas=' + this.$route.params.Id)
        .then((respuesta) => respuesta.json())
        .then((DatosRespuesta) => {
          if (DatosRespuesta.length === 1 && Object.values(DatosRespuesta[0]).every(value => value === null)) {
            this.cargas = null;
          } else {
            this.cargas = DatosRespuesta;
          }
        })
    },

 getBoton() {
  const usuario = sessionStorage.getItem("username");
  const datosEnviar = {
    Id: this.$route.params.Id,
    UsrNam: usuario       
  };
  axios.post('http://localhost:8081/DatosFacturas/cargas.php?consultarBoton', datosEnviar)
    .then((respuesta) => {
      const DatosRespuesta = respuesta.data;

      if (DatosRespuesta.length > 0 && DatosRespuesta[0].resultado === '1') {
        this.autorizarVisible = true;
      } else {
        this.autorizarVisible = false;
      }
    })
    .catch(error => {
      console.error('Error en la solicitud:', error);
    });
},

    autorizar() {
  const usuario = sessionStorage.getItem("username");
  const datosEnviar = {
    Id: this.$route.params.Id,
    Usuario: usuario,
    Modifico: usuario       
  };
  axios.post('http://localhost:8081/DatosFacturas/cargas.php?autorizar=true', datosEnviar)
    .then((respuesta) => {
      console.log(respuesta.data);
      window.location.reload();
    })
    .catch((error) => {
      console.error(error);
    });
},

getEditar(){
  axios.post('http://localhost:8081/DatosFacturas/cargas.php?editar=' + this.$route.params.Id) .then((respuesta) => { 
    const datosRespuesta = respuesta.data;
    if (datosRespuesta[0].resultado === 'PENDIENTE POR AUTORIZAR'){
          this.editar = false;
    }else if (datosRespuesta[0].resultado === 'TRAMITADA'){
         this.ver = false;
         this.autorizarVisible = false;
    }else if (datosRespuesta[0].resultado === 'CONTABILIZADA'){ 
      this.editar = false;
      this.autorizarVisible = false;
      this.okVisible = false;
    }

    if (datosRespuesta[0].usuario === sessionStorage.getItem("username") && datosRespuesta[0].resultado !== 'TRAMITADA' && datosRespuesta[0].resultado !== 'CONTABILIZADA') {
        this.editar = true; 
      } else {
        this.editar = false;
      }
  
  })
},

getOk(){
   const usuario = sessionStorage.getItem("username");
   const datosEnviar = {
    Usuario : usuario,
    Id : this.$route.params.Id
   }
   axios.post('http://localhost:8081/DatosFacturas/cargas.php?getOk=', datosEnviar)
   .then((respuesta) => {
    const datosRespuesta = respuesta.data;
    if (datosRespuesta.length > 0 && datosRespuesta[0].resultado === '1'){
          this.okVisible = true;
    } else {
          this.okVisible = false;
    }
   })
},

getContabilizada(){
  const usuario = sessionStorage.getItem("username");
  const datosEnviar = {
        Id: this.$route.params.Id,
        Usuario: usuario
  }
  axios.post('http://localhost:8081/DatosFacturas/cargas.php?getContabilizada', datosEnviar)
        .then((respuesta) => {
          const datosRespuesta = respuesta.data;
          if (datosRespuesta.length > 0 && datosRespuesta[0].resultado === '1') {
                this.ContabilVisible = true;
          } else {
                this.ContabilVisible = false;
          }     
        })
},

contabilizar () {
  const usuario = sessionStorage.getItem("username");
      const datosEnviar = {
         Id: this.$route.params.Id,
         Modifico: usuario
      }
   axios.post('http://localhost:8081/DatosFacturas/cargas.php?Contabilizar', datosEnviar)
        .then( (respuesta) => {
          const datosRespuesta = respuesta.data;
          console.log(datosRespuesta);
          window.location.reload();
        })

},



Ok() {
  const usuario = sessionStorage.getItem("username");
        const DatosEnviar = {
            IdFac: this.$route.params.Id,
            Modifico: usuario
        };
        axios.post('http://localhost:8081/DatosFacturas/cargas.php?Ok', DatosEnviar)
            .then((respuesta) => {        
       console.log(respuesta.data.mensaje);
       window.location.reload();
            })
    },


    addRow() {
      this.rows.push({
        cuentaSeleccionada: '',
        centroSeleccionado: '',
        porcentaje: '',
        valor: '',
      });
    },
    delRow() {
      this.rows.pop({
        cuentaSeleccionada: '',
        centroSeleccionado: '',
        porcentaje: '',
        valor: '',
      });
    },

submitForm() {
    this.rows.forEach((row) => {
        const usuario = sessionStorage.getItem("username");
        const DatosEnviar = {
            Cuenta: row.cuentaSeleccionada,
            Centro: row.centroSeleccionado,
            Porcentaje: row.porcentaje,
            Valor: row.valor,
            IdFac: this.$route.params.Id,
            UsrNam: usuario,
            Modifico: usuario
        };
        axios.post('http://localhost:8081/DatosFacturas/cargas.php?insertarCargas', DatosEnviar)
            .then((respuesta) => {        
       console.log(respuesta.data.mensaje);
            })
    });
},

    borrarCarga(Id) {
  const usuario = sessionStorage.getItem("username");
  axios.post(`http://localhost:8081/DatosFacturas/cargas.php?borrarCargas=${Id}&UsrNam=${usuario}`)
    .then((respuesta) => {
      console.log(respuesta.data);
      window.location.reload();
    })
},

    formatNumber(value) {
      const parts = value.toString().split(".");
      parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      return parts.join(",");      
    },

    calculateTotal() {
      this.totalTemporal = this.rows.reduce((total, row) => total + (parseFloat(row.valor) || 0), 0);
      return this.totalTemporal;
    },
  },

  watch: 
  {
    'rows': {
      deep: true,
      handler(newRows) {
        newRows.forEach((row) => {
          const valor = parseFloat(row.valor) || 0;
          this.calculateTotal(); 
          row.Porcen = (valor / this.totalTemporal * 100).toFixed(2);
        });
      },
    },
  },
};
</script>

