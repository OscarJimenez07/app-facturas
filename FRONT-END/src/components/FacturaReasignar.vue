<template>
    <form v-on:submit.prevent="AsignarEncargado">  
        <label style="color:white">Usuario a Reasignar: </label>
        <select v-model="encargadoSeleccionado" class="form-control" style="background-color:#e6e6e6;">
            <option value="">Seleccione el Encargado</option>
            <option v-for="encargado in encargados" :key="encargado.id" :value="encargado.Nombre">{{ encargado.Nombre }} </option>
        </select>
        <p></p>
        <button type="submit" class="btn btn-warning">Reasignar Factura</button>
    </form>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            facturas: [],
            encargados: [],
            encargadoSeleccionado: '', 
        }
    },
    created:function() {
        this.getFacturas();
        this.getEncargados();
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
        axios.get('http://127.0.0.1:5000/encargados/consultar')
            .then(response => {
                this.encargados = response.data;
                console.log(response.data)
            });
    },
    
        AsignarEncargado() {
            const encargado = this.encargados.find(e => e.Nombre === this.encargadoSeleccionado);        
            const facturaId = this.$route.params.Id;
            const DatosEnviar = {
                Id: facturaId,
                Encargado: encargado.Nombre,
                Correo: encargado.Correo,
            };
                axios.post('http://127.0.0.1:5000/encargados/reasignar', DatosEnviar)
                    .then((response) => {
                        console.log(response.data);
                    })
        }
    },    
};
</script>
