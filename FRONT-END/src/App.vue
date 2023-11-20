<template>
  <div id="app">
    <div v-if="!isLoginView">
      <div class="dashboard">
        <div class="dashboard-nav">
          <header>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
            <a href="#" style="font-size: 17px;">
              <i class="bi bi-clipboard2-check-fill" style="font-size: 34px; margin-right: 5px;"></i>Facturas Ardisa S.A.
            </a>
          </header>
          <nav class="dashboard-nav-list">
            <a href="#" class="dashboard-nav-item" :class="{ active: isMenuActive('/') }">
              <i class="fas fa-home" style="font-size: 25px; margin-right: 40px;"></i> Inicio
            </a>
            <a href="../lista" class="dashboard-nav-item" :class="{ active: isMenuActive('/lista'), 'text-black': isMenuActive('/lista') }">
              <i class="bi bi-receipt-cutoff" style="font-size: 25px; margin-right: 35px;"></i> Facturas
            </a>
            <a href="#" class="dashboard-nav-item" :class="{ active: isMenuActive('/estados'), 'text-black': isMenuActive('/estados') }">
              <i class="fas fa-file-upload" style="font-size: 25px; margin-right: 40px;"></i> Estados
            </a>
            <div class="dashboard-nav-dropdown"></div>
            <div class="dashboard-nav-dropdown">
              <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle" :class="{ active: isMenuActive('/usuarios'), 'text-black': isMenuActive('/usuarios') }">
                <i class="fas fa-users" style="font-size: 20px; margin-right: 31px;"></i> Usuarios
              </a>
            </div>
            <div class="dashboard-nav-dropdown">
              <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle" :class="{ active: isMenuActive('/log'), 'text-black': isMenuActive('/log') }">
                <i class="fas fa-money-check-alt" style="font-size: 20px; margin-right: 43px;"></i> Log
              </a>
            </div>
            <div class="dashboard-nav-dropdown" style="pointer-events: none;">
              <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle" :class="{ active: isMenuActive('/log'), 'text-black': isMenuActive('/log') }">         
              </a>
            </div>
          </nav>
        </div>
        <div class="dashboard-app">
          <header class="dashboard-toolbar" style="background-color: #202124;" >
            <i class="bi bi-clipboard2-check-fill" style="font-size: 34px; margin-left: 0px; color: white;"></i>
            <span style="font-size: 17px; color: white; margin-left: 5px;">Facturas Ardisa S.A.</span>
          </header>
          <div class="dashboard-content">
            <div class="container">
              <router-view />
            </div>
          </div>
        </div>
      </div>
    </div>
    <router-view v-else class="custom-router-view" />
  </div>
</template>

<script>
export default {
  name: 'App',
  computed: {
    isLoginView() {
      return this.$route.name === 'VistaLogin';
    }
  },
  data() {
    return {
      username: null
    };
  },
  created() {
    this.getUsernameFromSessionStorage().then(username => {
      this.username = username;
    });
  },
  methods: {
    getUsernameFromSessionStorage() {
      return new Promise(resolve => {
        const checkUsername = () => {
          const username = sessionStorage.getItem('username');
          if (username) {
            resolve(username);
          } else {
            setTimeout(checkUsername, 100); 
          }
        };
        checkUsername();
      });
    },
    logout() {
  sessionStorage.removeItem('loggedin');
  sessionStorage.removeItem('username');

  this.$router.push({ name: 'Login' });

  window.location.reload();
},
isMenuActive(route) {
      return this.$route.path === route;

}
  }
};
</script>

<style scoped>
.text-black {
  color: black !important; 
  font-weight: bold; 
  font-size: 17px;
}

.dashboard-nav {
  border-right: 3px solid white; 
  box-shadow: 0 1px 5px white;
  min-width: 15px;
}

.dashboard-nav-list {
  padding-right: 1px; 
  min-width: 15px;
}

.dashboard-nav-item {
    background-image: linear-gradient(to right, transparent, white 50%, transparent);
    background-size: 100% 1px; 
    background-repeat: no-repeat;
    min-height: 1px; 
    padding: 0; 
  min-width: 1px;
}

.container {
  text-align: center;
  background-color: rgba(39, 44, 49, 0.397);
  height: 100%;
  overflow: hidden;
  padding: 10px; 
  max-width: 100%; 
}

.dashboard-app {
margin-top: 100px;
}


.custom-router-view {

    flex-direction: column;
    flex-grow: 2;
   
}

.dashboard-nav {
    min-width: 15px;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    overflow: auto;
    background-color: #202124;
}

.dashboard-nav header {
    min-height: 84px;
    padding:10px;  
}

.dashboard-nav header .menu-toggle {
    display: none;
    margin-right: auto;
}

.dashboard-nav a {
    color: #515151;
}

.dashboard-nav a:hover {
    text-decoration: none;
}

.dashboard-nav {
    background-color: #202124;
}

.dashboard-nav a {
    color: #fff;
}


.dashboard-nav-item {
    min-height: 56px;
    padding: 8px ;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    letter-spacing: 0.01em;
    transition: ease-out 0.5s;
    
}

.dashboard-nav-item:hover {
    background: grey;
}

.active {
    background: grey;
}


.dashboard-nav .dashboard-nav-dropdown-toggle:after {
    border-top-color: rgba(255, 255, 255, 0.72);
}

.dashboard-toolbar {
    min-height: 70px;
    background-color: #dfdfdf;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    padding: 8px 27px;
    position: fixed;
    top: 0;
    right: 0;
    left: 0;
    z-index: 1000;
  border-bottom: 3px solid white;

}


@media (min-width: 992px) {
    .dashboard-app {
        margin-left: 238px;
    }
  }
@media (max-width: 768px) {
    .dashboard-content {
        padding: 15px 0px;
    }
}

@media (max-width: 992px) {
    .dashboard-nav {
        display: none;
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        z-index: 1070;
    }

    .dashboard-nav.mobile-show {
        display: block;
    }
}

@media (min-width: 992px) {
  .dashboard-toolbar{margin-left: -20px;}
}
</style>
