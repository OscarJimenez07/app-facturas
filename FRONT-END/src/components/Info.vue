<template>
  
</template>

<script>
import axios from 'axios';

export default {
    
    data(){ return {} },
    
    computed: {} ,
    
    created:function() {
        getFacturas();
    } ,
    
    methods: {
        getFacturas(){
            axios.get('http://example.com')
                 .then((respuesta)=> {
                  console.log(respuesta.data)  
                 })
                 .catch(()=> {})
        },

        


    } ,

    props: {} ,

    watch: {},

    mounted() {
        axios.get('http://example.com')
                 .then((respuesta)=> {
                  console.log(respuesta.data)  
                 })
                 .catch(()=> {})
        }
    }


</script>

<style>

</style>