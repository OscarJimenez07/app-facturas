<template>
    
      <div class="card custom-login-card" >
        <div class="card-header bg-dark" >
          <h5 class="card-title text-center" Style = "background-color: #343a40; color:white">
            Iniciar Sesión
          </h5>
          <i class="bi bi-shield-lock" style="color: white; font-size: 25px;"></i>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        </div>
        <div class="card-body custom-card-body">
          <form @submit.prevent="login">
            <div class="form-group">
              <label for="UsrNam">Usuario</label>

              <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" style="background-color: #e6e6e6; border: none;">
                  <i class="bi bi-person-vcard" style="color: black; font-size: 20px;"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="UsrNam" v-model="UsrNam" required>
            </div>
            </div>
            <div class="form-group">
              <label for="Pass">Contraseña</label>
              <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" style="background-color: #e6e6e6; border: none;">
                  <i class="bi bi-key-fill" style="color: black; font-size: 20px;"></i>
                </span>
              </div>
              <input type="password" class="form-control" id="Pass" v-model="Pass" required>
            </div>
          </div> 
          <div v-if="showErrorAlert" class="alert alert-danger mt-3" role="alert">
      Inicio de sesión fallido. Por favor, verifica tu usuario y contraseña.
          </div>
            <button type="submit" class="btn btn-primary btn-block">
              Iniciar Sesión
            </button>
          </form>
        </div>
      </div>
</template>

<script>
import axios from 'axios';
  
  export default {
    data() {
      return {
        UsrNam: '',
        Pass: '',
        showErrorAlert: false,
      };
    },
    methods: {
      async login() {
        try {
          const originalUrl = this.$route.fullPath;
          
          const response = await axios.post('http://localhost:8081/DatosFacturas/login.php', {
            UsrNam: this.UsrNam,
            Pass: this.Pass,
            OriginalUrl: originalUrl,
          });
  
          if (response.data.success === 1) {
            const userRole = response.data.IdRol;
            sessionStorage.setItem('loggedin', true);
            sessionStorage.setItem('username', this.UsrNam);
            sessionStorage.setItem('userRole', userRole.toString());

            const redirectPath = sessionStorage.getItem('redirectPath') || '/listar';
            sessionStorage.removeItem('redirectPath'); 
            this.$router.push(redirectPath);
          } else {
            this.showErrorAlert = true;
          }
        } catch (error) {
          console.error('Error during login:', error.response ? error.response.data : error.message);
        }
      },
    },
  };
</script>

